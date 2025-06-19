<?php

namespace App\Http\Controllers;

use App\Models\{OpdForm, OpdSubmission, Patient, Token, Department, Queue};
use Illuminate\Http\Request;

class ObOpdFormController extends Controller
{
    public function __construct()
    {
        // Require authentication for all routes
        $this->middleware('auth');
    }

    /**
     * 1. List all OB submissions, show unique patients & queues for issuing tokens.
     */
  public function index()
{
    // Fetch your submissions & patients & queues …
    $subs     = OpdSubmission::with('patient.user', 'form')
                 ->whereHas('form', fn($q) => $q->where('form_no','OPD-F-07'))
                 ->latest()
                 ->get();

    $patients = $subs->pluck('patient')->filter()->unique('id')->values();
    $queues   = Department::all();

    return view('opd_forms.opdb.index', compact('patients','queues'));
}


    /**
     * 2. Show blank OB-OPD form.
     */
    public function create()
    {
        return view('opd_forms.opdb.create', [
            'opd_form'   => null,
            'postRoute'  => route('ob-opd-forms.store'),
            'showButtons'=> true,
        ]);
    }

    /**
     * 3. Validate & store submission, upsert Patient & Profile, issue queue token.
     */
    public function store(Request $request)
    {
        // Fetch the OB template form definition
        $template = OpdForm::where('department', 'OB')
                           ->where('form_no', 'OPD-F-07')
                           ->firstOrFail();

        // Enhanced validation rules for all patient types
        $rules = [
            // Required fields
            'date'                => 'required|date',
            'last_name'           => 'required|string|max:255',
            'given_name'          => 'required|string|max:255',
            'sex'                 => 'required|in:male,female',
            'civil_status'        => 'required|in:single,married,widowed,separated,divorced',
            
            // Optional basic fields
            'time'                => 'nullable|date_format:H:i',
            'record_no'           => 'nullable|string|max:100',
            'middle_name'         => 'nullable|string|max:255',
            'age'                 => 'nullable|integer|min:0|max:120',
            'birth_date'          => 'nullable|date|before_or_equal:today',
            'place_of_birth'      => 'nullable|string|max:255',
            'occupation'          => 'nullable|string|max:100',
            'religion'            => 'nullable|string|max:100',
            'contact_no'          => 'nullable|string|max:50',
            'address'             => 'nullable|string|max:500',
            
            // Female-specific fields
            'maiden_name'         => 'nullable|string|max:255',
            
            // Marriage fields (conditional)
            'spouse_name'         => 'nullable|string|max:255',
            'spouse_occupation'   => 'nullable|string|max:255',
            'spouse_contact'      => 'nullable|string|max:50',
            'place_of_marriage'   => 'nullable|string|max:255',
            'date_of_marriage'    => 'nullable|date|before_or_equal:today',
            
            // Emergency contact fields
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'emergency_contact_phone'    => 'nullable|string|max:50',
            'emergency_contact_required' => 'nullable|boolean',
            
            // Additional notes
            'medical_notes'       => 'nullable|string|max:1000',
        ];

        // Add conditional validation
        $request->validate($rules + $this->getConditionalRules($request));

        $data = $request->only(array_keys($rules));

        // ① Create OPD submission (answers stored as JSON)
        $submission = OpdSubmission::create([
            'form_id'    => $template->id,
            'user_id'    => auth()->id(),
            'patient_id' => null,
            'answers'    => $data,
        ]);

        // ② Upsert Patient by name + birth_date
        $name      = trim("{$data['last_name']}, {$data['given_name']}");
        $birthDate = $data['birth_date'] ?? null;

        $patient = Patient::firstOrCreate(
            ['name' => $name, 'birth_date' => $birthDate],
            [
                'contact_no' => $data['contact_no'] ?? null, 
                'address' => $data['address'] ?? null
            ]
        );
        
        if (! $patient->user_id) {
            $patient->user_id = auth()->id();
            $patient->save();
        }

        // ③ Attach patient to submission
        $submission->patient_id = $patient->id;
        $submission->save();

        // ④ Build comprehensive profile data & upsert
        $profile = [
            'sex'                   => $data['sex'],
            'religion'              => $data['religion'] ?? null,
            'occupation'            => $data['occupation'] ?? null,
            'civil_status'          => $data['civil_status'],
            'place_of_birth'        => $data['place_of_birth'] ?? null,
            'maiden_name'           => $data['maiden_name'] ?? null,
            'spouse_name'           => $data['spouse_name'] ?? null,
            'spouse_occupation'     => $data['spouse_occupation'] ?? null,
            'spouse_contact'        => $data['spouse_contact'] ?? null,
            'place_of_marriage'     => $data['place_of_marriage'] ?? null,
            'date_of_marriage'      => $data['date_of_marriage'] ?? null,
            'emergency_contact_name'     => $data['emergency_contact_name'] ?? null,
            'emergency_contact_relation' => $data['emergency_contact_relation'] ?? null,
            'emergency_contact_phone'    => $data['emergency_contact_phone'] ?? null,
            'medical_notes'         => $data['medical_notes'] ?? null,
        ];
        
        $patient->profile()->updateOrCreate([], $profile);

        // ⑤ Issue a queue token in "OB" queue if not already waiting
        $obQueue = Queue::where('name', 'OB')->first();
        if ($obQueue) {
            $already = Token::where('queue_id', $obQueue->id)
                            ->where('patient_id', $patient->id)
                            ->whereNull('served_at')
                            ->exists();
            if (! $already) {
                $nextNum = Token::where('queue_id', $obQueue->id)->count() + 1;
                $code    = 'O' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);

                $newToken = Token::create([
                    'queue_id'   => $obQueue->id,
                    'patient_id' => $patient->id,
                    'code'       => $code,
                ]);
                session(['new_token_id' => $newToken->id]);
            }
        }

        return redirect()
            ->route('ob-opd-forms.index')
            ->with('success', 'Patient registration completed successfully.');
    }

    /**
     * Get conditional validation rules based on patient data
     */
    private function getConditionalRules(Request $request): array
    {
        $rules = [];
        $age = (int) $request->input('age', 0);
        $civilStatus = $request->input('civil_status');

        // Emergency contact required for minors
        if ($age > 0 && $age < 18) {
            $rules['emergency_contact_name'] = 'required|string|max:255';
            $rules['emergency_contact_relation'] = 'required|string|max:100';
            $rules['emergency_contact_phone'] = 'required|string|max:50';
        }

        // Spouse information validation for married patients
        if (in_array($civilStatus, ['married', 'widowed'])) {
            $rules['spouse_name'] = 'nullable|required_with:date_of_marriage|string|max:255';
        }

        return $rules;
    }

    /**
     * 4. Show a single submission's patient details.
     */
    public function show(OpdSubmission $submission)
    {
        $submission->load('patient.user', 'patient.profile', 'patient.visits');
        $patient = $submission->patient;
        return view('patients.show', compact('patient'));
    }

    /**
     * 5. Show edit form.
     */
    public function edit(OpdSubmission $submission)
    {
        $submission->load('form');
        return view('opd_forms.opdb.edit', [
            'submission' => $submission,
            'opd_form'   => $submission, // Pass submission as opd_form for the form helper
            'postRoute'  => route('ob-opd-forms.update', $submission),
            'showButtons'=> true,
        ]);
    }

    /**
     * 6. Update submission answers.
     */
    public function update(Request $request, OpdSubmission $submission)
    {
        // Use same validation as store
        $rules = [
            'date'                => 'required|date',
            'last_name'           => 'required|string|max:255',
            'given_name'          => 'required|string|max:255',
            'sex'                 => 'required|in:male,female',
            'civil_status'        => 'required|in:single,married,widowed,separated,divorced',
            'time'                => 'nullable|date_format:H:i',
            'record_no'           => 'nullable|string|max:100',
            'middle_name'         => 'nullable|string|max:255',
            'age'                 => 'nullable|integer|min:0|max:120',
            'birth_date'          => 'nullable|date|before_or_equal:today',
            'place_of_birth'      => 'nullable|string|max:255',
            'occupation'          => 'nullable|string|max:100',
            'religion'            => 'nullable|string|max:100',
            'contact_no'          => 'nullable|string|max:50',
            'address'             => 'nullable|string|max:500',
            'maiden_name'         => 'nullable|string|max:255',
            'spouse_name'         => 'nullable|string|max:255',
            'spouse_occupation'   => 'nullable|string|max:255',
            'spouse_contact'      => 'nullable|string|max:50',
            'place_of_marriage'   => 'nullable|string|max:255',
            'date_of_marriage'    => 'nullable|date|before_or_equal:today',
            'emergency_contact_name'     => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:100',
            'emergency_contact_phone'    => 'nullable|string|max:50',
            'emergency_contact_required' => 'nullable|boolean',
            'medical_notes'       => 'nullable|string|max:1000',
        ];

        $request->validate($rules + $this->getConditionalRules($request));
        $data = $request->only(array_keys($rules));

        $submission->answers = $data;
        $submission->save();

        // Update patient profile as well
        if ($submission->patient) {
            $profile = [
                'sex'                   => $data['sex'],
                'religion'              => $data['religion'] ?? null,
                'occupation'            => $data['occupation'] ?? null,
                'civil_status'          => $data['civil_status'],
                'place_of_birth'        => $data['place_of_birth'] ?? null,
                'maiden_name'           => $data['maiden_name'] ?? null,
                'spouse_name'           => $data['spouse_name'] ?? null,
                'spouse_occupation'     => $data['spouse_occupation'] ?? null,
                'spouse_contact'        => $data['spouse_contact'] ?? null,
                'place_of_marriage'     => $data['place_of_marriage'] ?? null,
                'date_of_marriage'      => $data['date_of_marriage'] ?? null,
                'emergency_contact_name'     => $data ['emergency_contact_name'] ?? null,
                'emergency_contact_relation' => $data['emergency_contact_relation'] ?? null,
                'emergency_contact_phone'    => $data['emergency_contact_phone'] ?? null,
                'medical_notes'         => $data['medical_notes'] ?? null,
            ];
            $submission->patient->profile()->updateOrCreate([], $profile);
        }
        return redirect()
            ->route('ob-opd-forms.index')
            ->with('success', 'Submission updated successfully.');
    }

    /**
     * 7. Delete a submission.
     */
    public function destroy(OpdSubmission $submission)
    {
        // Ensure only OPD-F-07 submissions can be deleted
        if ($submission->form->form_no !== 'OPD-F-07') {
            return redirect()->route('ob-opd-forms.index')
                ->withErrors('Invalid submission type for deletion.');
        }

        // Delete the submission and associated patient profile
        $patient = $submission->patient;
        $submission->delete();
        
        if ($patient) {
            $patient->profile()->delete();
            $patient->delete();
        }

        return redirect()
            ->route('ob-opd-forms.index')
            ->with('success', 'Submission deleted successfully.');
    }

    /**
     * 8. Show a single submission's patient details.
     */
    public function showSubmission(OpdSubmission $submission)
    {
        $submission->load('patient.user', 'patient.profile', 'patient.visits');
        $patient = $submission->patient;
        return view('patients.show', compact('patient'));
    }
    /**
     * 9. Show edit form for a specific submission.
     */
    public function editSubmission(OpdSubmission $submission)
    {
        $submission->load('form');
        return view('opd_forms.opdb.edit', [
            'submission' => $submission,
            'opd_form'   => $submission, // Pass submission as opd_form for the form helper
            'postRoute'  => route('ob-opd-forms.update', $submission),
            'showButtons'=> true,
        ]);
    }
    
}