<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Patient;
use App\Models\OpdSubmission;
use App\Models\PatientProfile;
use App\Exports\PatientRecordExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use PDF;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collections;
use App\Models\Queue;

class PatientRecordController extends Controller
{
    public function __construct()
    {
        // Only admins can manage patient records
        $this->middleware(['auth','role:admin']);
    }

    /**
     * AJAX patient search for Select2.
     */
    public function search(Request $request)
    {
        $q = $request->input('q', '');

        $patients = Patient::with('profile')
            ->where('name', 'like', "%{$q}%")
            ->limit(20)
            ->get();

        $results = $patients->map(function($p) {
            // split “Last, Given” into two parts
            [$last, $given] = array_pad(explode(',', $p->name, 2), 2, '');

            return [
                'id'          => $p->id,
                'text'        => $p->name,              // what Select2 shows in dropdown
                'last_name'   => trim($last),
                'given_name'  => trim($given),
                'middle_name' => $p->profile->middle_name ?? '',
                'age'         => $p->birth_date
                                   ? now()->diffInYears($p->birth_date)
                                   : '',
                'sex'         => ucfirst($p->profile->sex ?? ''),
            ];
        });

        return response()->json(['results' => $results]);
    }
public function index(Request $request)
{
    // --------- 1. filter patients ----------
    $query = OpdSubmission::with(['patient.profile'])
        ->whereHas('form', fn ($q) => $q->where('form_no', 'OPD-F-07'));

    if ($search = $request->input('search')) {
        $query->whereHas('patient', fn ($q) =>
            $q->where('name', 'like', "%{$search}%")
        );
    }

    if ($sex = $request->input('sex')) {
        $query->whereHas('patient.profile', fn ($q) =>
            $q->where('sex', $sex)
        );
    }

    $patientIds = $query->get()
                        ->pluck('patient.id')
                        ->unique()
                        ->values()
                        ->all();

    $patients = Patient::with('profile')
                ->withCount('visits')
                ->whereIn('id', $patientIds)
                ->get();

 $queues = Queue::with('parent')          // eager-load parent window name
                   ->whereNotNull('parent_id')
                   ->orderBy('parent_id')
                   ->orderBy('name')
                   ->get();

    return view('patients.index', compact('patients', 'queues'));
}

public function visits(Patient $patient)
{
    $visits = Visit::with('queue', 'token')   // eager-load
                   ->where('patient_id', $patient->id)
                   ->latest('visited_at')
                   ->paginate(15);

    return view('visits.index', compact('patient','visits'));
}

    /**
     * Show the form for creating a new patient + profile.
     */
    public function create()
    {
        return view('patients.create');
    }

    /**
     * Store a newly created patient (and user + profile).
     */
    public function store(Request $request)
    {
        // 1) Validate user + patient fields
        $baseRules = [
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|confirmed|min:8',
            'name'       => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'contact_no' => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:255',
        ];

        // 2) Validate profile fields
        $profileRules = [
            'sex'               => 'nullable|in:male,female',
            'religion'          => 'nullable|string|max:100',
            'date_recorded'     => 'nullable|date',
            'father_name'       => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name'       => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'place_of_marriage' => 'nullable|string|max:255',
            'date_of_marriage'  => 'nullable|date',
            'blood_type'        => 'nullable|string|max:3',
            'delivery_type'     => 'nullable|string|max:50',
            'birth_weight'      => 'nullable|numeric|min:0|max:20',
            'birth_length'      => 'nullable|numeric|min:0|max:100',
            'apgar_appearance'  => 'nullable|integer|between:0,2',
            'apgar_pulse'       => 'nullable|integer|between:0,2',
            'apgar_grimace'     => 'nullable|integer|between:0,2',
            'apgar_activity'    => 'nullable|integer|between:0,2',
            'apgar_respiration' => 'nullable|integer|between:0,2',
        ];

        $data = $request->validate(array_merge($baseRules, $profileRules));

        // 3) Create the user
        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'role'     => 'patient',
        ]);

        // 4) Create the patient
        $patient = Patient::create([
            'user_id'    => $user->id,
            'name'       => $data['name'],
            'birth_date' => $data['birth_date'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,
            'address'    => $data['address'] ?? null,
        ]);

        // 5) Create the profile
        $patient->profile()->create(
            $request->only(array_keys($profileRules))
        );

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient added successfully.');
    }

   public function show(Patient $patient)
{
    $patient->load([
        'user',
        'profile',
        'visits.form',          // existing visits
        'highRiskSubmissions.form', // NEW eager-load
    ]);

    return view('patients.show', compact('patient'));
}
    /**
     * Show the form for editing the specified patient+profile ONLY.
     *
     * We have removed all user‐editing here: no email/password fields.
     */
    public function edit(Patient $patient)
    {
        $patient->load(['profile']);
        return view('patients.edit', compact('patient'));
    }

    /**
     * Update the specified patient and profile in storage.
     *
     * Notice: we no longer update $patient->user at all.
     */
    public function update(Request $request, Patient $patient)
    {
        // 1) Validate only patient‐fields (no email/password)
        $baseRules = [
            'name'       => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'contact_no' => 'nullable|string|max:50',
            'address'    => 'nullable|string|max:255',
        ];

        // 2) Same profile rules as store
        $profileRules = [
            'sex'               => 'nullable|in:male,female',
            'religion'          => 'nullable|string|max:100',
            'date_recorded'     => 'nullable|date',
            'father_name'       => 'nullable|string|max:255',
            'father_occupation' => 'nullable|string|max:255',
            'mother_name'       => 'nullable|string|max:255',
            'mother_occupation' => 'nullable|string|max:255',
            'place_of_marriage' => 'nullable|string|max:255',
            'date_of_marriage'  => 'nullable|date',
            'blood_type'        => 'nullable|string|max:3',
            'delivery_type'     => 'nullable|string|max:50',
            'birth_weight'      => 'nullable|numeric|min:0|max:20',
            'birth_length'      => 'nullable|numeric|min:0|max:100',
            'apgar_appearance'  => 'nullable|integer|between:0,2',
            'apgar_pulse'       => 'nullable|integer|between:0,2',
            'apgar_grimace'     => 'nullable|integer|between:0,2',
            'apgar_activity'    => 'nullable|integer|between:0,2',
            'apgar_respiration' => 'nullable|integer|between:0,2',
        ];

        $data = $request->validate(array_merge($baseRules, $profileRules));

        // 3) Update patient only:
        $patient->update([
            'name'       => $data['name'],
            'birth_date' => $data['birth_date'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,
            'address'    => $data['address'] ?? null,
        ]);

        // 4) Update or create profile
        $patient->profile()
                ->updateOrCreate([], $request->only(array_keys($profileRules)));

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    /**
     * Remove the specified patient (and user) from storage.
     */
    public function destroy(Patient $patient)
    {
        // also remove the linked User:
        if ($patient->user) {
            $patient->user()->delete();
        }
        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient deleted.');
    }

    /**
     * Export a single patient record (and visits) to Excel.
     */
    public function exportExcel(Patient $patient)
    {
        $patient->load(['user','visits']);
        return Excel::download(
            new PatientRecordExport($patient),
            "patient-{$patient->id}-record.xlsx"
        );
    }

    /**
     * Export a single patient record (and visits) to PDF.
     */
    public function exportPdf(Patient $patient)
    {
        $patient->load(['user','visits']);
        $pdf = PDF::loadView('patients.pdf', compact('patient'))
                  ->setPaper('a4','portrait');

        return $pdf->download("patient-{$patient->id}-record.pdf");
    }
}
