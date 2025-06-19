<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\TriageForm;
use Illuminate\Http\Request;

class PatientTriageFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * 1. List existing triage forms
     */
    public function index()
    {
        $forms = TriageForm::with('patient')->latest()->paginate(10);

        return view('opd_forms.triage.index', compact('forms'));
    }

    /**
     * 2. Show blank triage form
     */
    public function create()
    {
        $patients = Patient::orderBy('name')->get();

        return view('opd_forms.triage.create', [
            'triageForm' => null,
            'postRoute'  => route('opd_forms.triage.store'),
            'patients'   => $patients,
        ]);
    }

    /**
     * 3. Store new triage form
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'               => 'required|exists:patients,id',
            'tetanus_t1_date'          => 'nullable|date',
            'tetanus_t1_signature'     => 'nullable|string|max:255',
            'tetanus_t2_date'          => 'nullable|date',
            'tetanus_t2_signature'     => 'nullable|string|max:255',
            'tetanus_t3_date'          => 'nullable|date',
            'tetanus_t3_signature'     => 'nullable|string|max:255',
            'tetanus_t4_date'          => 'nullable|date',
            'tetanus_t4_signature'     => 'nullable|string|max:255',
            'tetanus_t5_date'          => 'nullable|date',
            'tetanus_t5_signature'     => 'nullable|string|max:255',
            'present_health_problems'  => 'nullable|array',
            'present_problems_other'   => 'nullable|string|max:255',
            'danger_signs'             => 'nullable|array',
            'danger_signs_other'       => 'nullable|string|max:255',
            'ob_history'               => 'nullable|array',
            'family_planning'          => 'nullable|in:Pills,IUD,Injectable,Withdrawal,Standard',
            'prev_pnc'                 => 'nullable|in:Private,MD,HC,TBA',
            'lmp'                      => 'nullable|date',
            'edc'                      => 'nullable|date',
            'gravida'                  => 'nullable|integer|min:0',
            'parity_t'                 => 'nullable|integer|min:0',
            'parity_p'                 => 'nullable|integer|min:0',
            'parity_a'                 => 'nullable|integer|min:0',
            'parity_l'                 => 'nullable|integer|min:0',
            'aog_weeks'                => 'nullable|integer|min:0',
            'chief_complaint'          => 'nullable|string',
            'physical_exam_log'        => 'nullable|array',
            'heent'                    => 'nullable|string',
            'heart_lungs'              => 'nullable|string',
            'diagnosis'                => 'nullable|string',
            'prepared_by'              => 'nullable|string|max:255',
            'blood_type'               => 'nullable|string|max:3',
            'delivery_type'            => 'nullable|string|max:50',
            'birth_weight'             => 'nullable|numeric',
            'birth_length'             => 'nullable|numeric',
            'apgar_appearance'         => 'nullable|integer|between:0,2',
            'apgar_pulse'              => 'nullable|integer|between:0,2',
            'apgar_grimace'            => 'nullable|integer|between:0,2',
            'apgar_activity'           => 'nullable|integer|between:0,2',
            'apgar_respiration'        => 'nullable|integer|between:0,2',
        ]);

        TriageForm::create($data);

        return redirect()
               ->route('opd_forms.triage.index')
               ->with('success','Triage form saved.');
    }

    /**
     * 4. Show a single triage form
     */
    public function show(TriageForm $triageForm)
    {
        return view('opd_forms.triage.show', compact('triageForm'));
    }

    /**
     * 5. Show edit form
     */
    public function edit(TriageForm $triageForm)
    {
        $patients = Patient::orderBy('name')->get();

        return view('opd_forms.triage.create', [
            'triageForm' => $triageForm,
            'postRoute'  => route('opd_forms.triage.update', $triageForm),
            'patients'   => $patients,
        ]);
    }

    /**
     * 6. Update an existing triage form
     */
    public function update(Request $request, TriageForm $triageForm)
    {
        $data = $request->validate([
        'patient_id'               => 'required|exists:patients,id',
            'tetanus_t1_date'          => 'nullable|date',
            'tetanus_t1_signature'     => 'nullable|string|max:255',
            'tetanus_t2_date'          => 'nullable|date',
            'tetanus_t2_signature'     => 'nullable|string|max:255',
            'tetanus_t3_date'          => 'nullable|date',
            'tetanus_t3_signature'     => 'nullable|string|max:255',
            'tetanus_t4_date'          => 'nullable|date',
            'tetanus_t4_signature'     => 'nullable|string|max:255',
            'tetanus_t5_date'          => 'nullable|date',
            'tetanus_t5_signature'     => 'nullable|string|max:255',
            'present_health_problems'  => 'nullable|array',
            'present_problems_other'   => 'nullable|string|max:255',
            'danger_signs'             => 'nullable|array',
            'danger_signs_other'       => 'nullable|string|max:255',
            'ob_history'               => 'nullable|array',
            'family_planning'          => 'nullable|in:Pills,IUD,Injectable,Withdrawal,Standard',
            'prev_pnc'                 => 'nullable|in:Private,MD,HC,TBA',
            'lmp'                      => 'nullable|date',
            'edc'                      => 'nullable|date',
            'gravida'                  => 'nullable|integer|min:0',
            'parity_t'                 => 'nullable|integer|min:0',
            'parity_p'                 => 'nullable|integer|min:0',
            'parity_a'                 => 'nullable|integer|min:0',
            'parity_l'                 => 'nullable|integer|min:0',
            'aog_weeks'                => 'nullable|integer|min:0',
            'chief_complaint'          => 'nullable|string',
            'physical_exam_log'        => 'nullable|array',
            'heent'                    => 'nullable|string',
            'heart_lungs'              => 'nullable|string',
            'diagnosis'                => 'nullable|string',
            'prepared_by'              => 'nullable|string|max:255',
            'blood_type'               => 'nullable|string|max:3',
            'delivery_type'            => 'nullable|string|max:50',
            'birth_weight'             => 'nullable|numeric',
            'birth_length'             => 'nullable|numeric',
            'apgar_appearance'         => 'nullable|integer|between:0,2',
            'apgar_pulse'              => 'nullable|integer|between:0,2',
            'apgar_grimace'            => 'nullable|integer|between:0,2',
            'apgar_activity'           => 'nullable|integer|between:0,2',
            'apgar_respiration'        => 'nullable|integer|between:0,2',
        ]);

        $triageForm->update($data);

        return redirect()
               ->route('opd_forms.triage.index')
               ->with('success','Triage form updated.');
    }

    /**
     * 7. Delete a triage form
     */
    public function destroy(TriageForm $triageForm)
    {
        $triageForm->delete();

        return redirect()
               ->route('opd_forms.triage.index')
               ->with('success','Triage form deleted.');
    }
}
