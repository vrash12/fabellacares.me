<?php

namespace App\Http\Controllers;

use App\Models\OpdForm;
use App\Models\OpdSubmission;
use Illuminate\Http\Request;

class InternalMedicineTriageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of internal medicine triage submissions.
     */
    public function index()
    {
        $submissions = OpdSubmission::with('form')
            ->whereHas('form', fn($q) => $q->where('form_no', 'TRG-IM-01'))
            ->latest()
            ->get();

        return view('opd_forms.internal_medicine_triage.index', compact('submissions'));
    }

    /**
     * Show the form for creating a new internal medicine triage submission.
     */
    public function create()
    {
        return view('opd_forms.internal_medicine_triage.create', [
            'triageForm' => null,
            'postRoute'  => route('triage.internal.store'),
        ]);
    }

    /**
     * Store a newly created internal medicine triage submission.
     */
    public function store(Request $request)
    {
        $template = OpdForm::where('form_no', 'TRG-IM-01')->firstOrFail();

        $rules = [
            'chief_complaint'               => 'nullable|string|max:255',
            'onset'                         => 'nullable|string|max:255',
            'duration'                      => 'nullable|string|max:255',
            'progression'                   => 'nullable|in:Improving,Worsening,Unchanged',

            'associated_symptoms'           => 'nullable|array',
            'associated_symptoms.*'         => 'required|string',
            'associated_symptoms_other'     => 'nullable|string|max:255',

            'past_history'                  => 'nullable|array',
            'past_history.*'                => 'nullable|in:Yes,No',

            'current_medications'           => 'nullable|string|max:255',
            'allergies'                     => 'nullable|string|max:255',

            'bp_systolic'                   => 'nullable|integer',
            'bp_diastolic'                  => 'nullable|integer',
            'heart_rate'                    => 'nullable|integer',
            'resp_rate'                     => 'nullable|integer',
            'temperature'                   => 'nullable|numeric',
            'height'                        => 'nullable|numeric',
            'weight'                        => 'nullable|numeric',
            'blood_sugar'                   => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        OpdSubmission::create([
            'form_id' => $template->id,
            'user_id' => auth()->id(),
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.internal.index')
            ->with('success', 'Internal medicine triage saved!');
    }

    /**
     * Display the specified internal medicine triage submission.
     */
    public function show(OpdSubmission $submission)
    {
        $submission->load('form');

        return view('opd_forms.internal_medicine_triage.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified internal medicine triage submission.
     */
    public function edit(OpdSubmission $submission)
    {
        $submission->load('form');

        return view('opd_forms.internal_medicine_triage.edit', [
            'triageForm' => $submission->answers,
            'postRoute'  => route('triage.internal.update', $submission),
        ]);
    }

    /**
     * Update the specified internal medicine triage submission.
     */
    public function update(Request $request, OpdSubmission $submission)
    {
        $rules = [
            'chief_complaint'               => 'nullable|string|max:255',
            'onset'                         => 'nullable|string|max:255',
            'duration'                      => 'nullable|string|max:255',
            'progression'                   => 'nullable|in:Improving,Worsening,Unchanged',

            'associated_symptoms'           => 'nullable|array',
            'associated_symptoms.*'         => 'required|string',
            'associated_symptoms_other'     => 'nullable|string|max:255',

            'past_history'                  => 'nullable|array',
            'past_history.*'                => 'nullable|in:Yes,No',

            'current_medications'           => 'nullable|string|max:255',
            'allergies'                     => 'nullable|string|max:255',

            'bp_systolic'                   => 'nullable|integer',
            'bp_diastolic'                  => 'nullable|integer',
            'heart_rate'                    => 'nullable|integer',
            'resp_rate'                     => 'nullable|integer',
            'temperature'                   => 'nullable|numeric',
            'height'                        => 'nullable|numeric',
            'weight'                        => 'nullable|numeric',
            'blood_sugar'                   => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        $submission->update(['answers' => $data]);

        return redirect()
            ->route('triage.internal.index')
            ->with('success', 'Internal medicine triage updated!');
    }

    /**
     * Remove the specified internal medicine triage submission.
     */
    public function destroy(OpdSubmission $submission)
    {
        $submission->delete();

        return redirect()
            ->route('triage.internal.index')
            ->with('success', 'Internal medicine triage deleted.');
    }
}
