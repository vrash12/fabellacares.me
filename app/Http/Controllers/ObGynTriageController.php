<?php

namespace App\Http\Controllers;

use App\Models\OpdForm;
use App\Models\OpdSubmission;
use Illuminate\Http\Request;

class ObGynTriageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of OB-GYN triage submissions.
     */
    public function index()
    {
        $submissions = OpdSubmission::with('form')
            ->whereHas('form', fn($q) => $q->where('form_no', 'TRG-OBG-01'))
            ->latest()
            ->get();

        return view('opd_forms.obgyn_triage.index', compact('submissions'));
    }

  public function create()
{
    return view('opd_forms.obgyn_triage.create', [
        'triageForm' => null,
        'patient'    => null,
        'postRoute'  => route('triage.obgyn.store'),
    ]);
}

    /**
     * Store a newly created OB-GYN triage submission.
     */
    public function store(Request $request)
    {
        $template = OpdForm::where('form_no', 'TRG-OBG-01')->firstOrFail();

        $rules = [
              'patient_id'                   => 'required|exists:patients,id',
            'chief_complaint'               => 'nullable|string|max:255',
            'onset'                         => 'nullable|string|max:255',
            'duration'                      => 'nullable|string|max:255',
            'pain_scale'                    => 'nullable|integer|min:0|max:10',
            'description'                   => 'nullable|in:Sharp,Dull,Cramping,Burning',
            'associated_symptoms'           => 'nullable|array',
            'associated_symptoms.*'         => 'required|string',
            'associated_symptoms_other'     => 'nullable|string|max:255',
            'menarche_age'                  => 'nullable|integer|min:0',
            'cycle_length'                  => 'nullable|integer|min:0',
            'flow'                          => 'nullable|in:Light,Moderate,Heavy',
            'lmp'                           => 'nullable|date',
            'menstrual_concerns'            => 'nullable|array',
            'menstrual_concerns.*'          => 'required|string',
            'gravida'                       => 'nullable|integer|min:0',
            'para'                          => 'nullable|integer|min:0',
            'full_term'                     => 'nullable|integer|min:0',
            'preterm'                       => 'nullable|integer|min:0',
            'abortion'                      => 'nullable|integer|min:0',
            'living'                        => 'nullable|integer|min:0',
            'prev_pregnancy_type'           => 'nullable|in:Normal,Cesarean,Complicated',
            'current_pregnancy'             => 'nullable|in:Yes,No',
            'gestation_weeks'               => 'nullable|integer|min:0',
            'prenatal_done'                 => 'nullable|in:Yes,No',
            'danger_signs_present'          => 'nullable|in:Yes,No',
            'danger_signs_details'          => 'nullable|string|max:255',
            'pap_smear_done'                => 'nullable|in:Yes,No',
            'pap_smear_date'                => 'nullable|date',
            'sti_history'                   => 'nullable|in:Yes,No',
            'contraceptive_use'             => 'nullable|in:Pills,IUD,Injectables,Condom,None',
            'bp_systolic'                   => 'nullable|integer',
            'bp_diastolic'                  => 'nullable|integer',
            'heart_rate'                    => 'nullable|integer',
            'resp_rate'                     => 'nullable|integer',
            'temperature'                   => 'nullable|numeric',
            'height'                        => 'nullable|numeric',
            'weight'                        => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        OpdSubmission::create([
            'form_id' => $template->id,
            'user_id' => auth()->id(),
            'patient_id' => $data['patient_id'],
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.obgyn.index')
            ->with('success', 'OB-GYN triage submission saved!');
    }

    /**
     * Display the specified OB-GYN triage submission.
     */
    public function show(OpdSubmission $submission)
    {
        $submission->load('form');

        return view('opd_forms.obgyn_triage.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified OB-GYN triage submission.
     */
 public function edit(OpdSubmission $submission)
{
    $submission->load('form','patient');

    return view('opd_forms.obgyn_triage.edit', [
        'triageForm' => $submission->answers,
        'patient'    => $submission->patient,
        'postRoute'  => route('triage.obgyn.update', $submission),
    ]);
}

    /**
     * Update the specified OB-GYN triage submission.
     */
    public function update(Request $request, OpdSubmission $submission)
    {
        // same rules as store()
        $rules = (new self)->store(request())->rules ?? [];

        $data = $request->validate([
            'patient_id'                   => 'required|exists:patients,id',
            'chief_complaint'               => 'nullable|string|max:255',
            'onset'                         => 'nullable|string|max:255',
            'duration'                      => 'nullable|string|max:255',
            'pain_scale'                    => 'nullable|integer|min:0|max:10',
            'description'                   => 'nullable|in:Sharp,Dull,Cramping,Burning',
            'associated_symptoms'           => 'nullable|array',
            'associated_symptoms.*'         => 'required|string',
            'associated_symptoms_other'     => 'nullable|string|max:255',
            'menarche_age'                  => 'nullable|integer|min:0',
            'cycle_length'                  => 'nullable|integer|min:0',
            'flow'                          => 'nullable|in:Light,Moderate,Heavy',
            'lmp'                           => 'nullable|date',
            'menstrual_concerns'            => 'nullable|array',
            'menstrual_concerns.*'          => 'required|string',
            'gravida'                       => 'nullable|integer|min:0',
            'para'                          => 'nullable|integer|min:0',
            'full_term'                     => 'nullable|integer|min:0',
            'preterm'                       => 'nullable|integer|min:0',
            'abortion'                      => 'nullable|integer|min:0',
            'living'                        => 'nullable|integer|min:0',
            'prev_pregnancy_type'           => 'nullable|in:Normal,Cesarean,Complicated',
            'current_pregnancy'             => 'nullable|in:Yes,No',
            'gestation_weeks'               => 'nullable|integer|min:0',
            'prenatal_done'                 => 'nullable|in:Yes,No',
            'danger_signs_present'          => 'nullable|in:Yes,No',
            'danger_signs_details'          => 'nullable|string|max:255',
            'pap_smear_done'                => 'nullable|in:Yes,No',
            'pap_smear_date'                => 'nullable|date',
            'sti_history'                   => 'nullable|in:Yes,No',
            'contraceptive_use'             => 'nullable|in:Pills,IUD,Injectables,Condom,None',
            'bp_systolic'                   => 'nullable|integer',
            'bp_diastolic'                  => 'nullable|integer',
            'heart_rate'                    => 'nullable|integer',
            'resp_rate'                     => 'nullable|integer',
            'temperature'                   => 'nullable|numeric',
            'height'                        => 'nullable|numeric',
            'weight'                        => 'nullable|numeric',
        ]);

        $submission->update([
            'patient_id' => $data['patient_id'],
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.obgyn.index')
            ->with('success', 'OB-GYN triage submission updated!');
    }

    /**
     * Remove the specified OB-GYN triage submission from storage.
     */
    public function destroy(OpdSubmission $submission)
    {
        $submission->delete();

        return redirect()
            ->route('triage.obgyn.index')
            ->with('success', 'OB-GYN triage submission deleted.');
    }
}
