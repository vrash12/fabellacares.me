<?php

namespace App\Http\Controllers;

use App\Models\OpdForm;
use App\Models\OpdSubmission;
use Illuminate\Http\Request;
use App\Models\Patient;

class PediaTriageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of pedia triage submissions.
     */
    public function index()
    {
        $submissions = OpdSubmission::with('form')
            ->whereHas('form', fn($q) => $q->where('form_no', 'TRG-PED-01'))
            ->latest()
            ->get();

        return view('opd_forms.pedia_triage.index', compact('submissions'));
    }

public function create()
{
    return view('opd_forms.pedia_triage.create', [
        'patients'   => Patient::all(),
        'pediaForm'  => null,
        'postRoute'  => route('triage.pedia.store'),
    ]);
}

    /**
     * Store a newly created pedia triage submission.
     */
    public function store(Request $request)
    {
        $template = OpdForm::where('form_no', 'TRG-PED-01')->firstOrFail();

        $rules = [
            'main_concern'            => 'nullable|string|max:255',
            'date_started'            => 'nullable|date',
            'progression'             => 'nullable|in:Improving,Worsening,Same',

            'assoc_symptoms'          => 'nullable|array',
            'assoc_symptoms.*'        => 'required|string',
            'assoc_symptoms_other'    => 'nullable|string|max:255',

            'delivery_type'           => 'nullable|in:Normal Spontaneous,CS,Instrumental',
            'delivery_place'          => 'nullable|string|max:255',
            'birth_weight'            => 'nullable|numeric|min:0',
            'term'                    => 'nullable|in:Full Term,Preterm',

            'nicu_admission'          => 'nullable|in:Yes,No',
            'nicu_reason'             => 'nullable|string|max:255',

            'immunization_status'     => 'nullable|in:Fully Immunized,Incomplete,Unknown',
            'missed_vaccines'         => 'nullable|string|max:255',
            'last_vaccine'            => 'nullable|string|max:255',
            'last_vaccine_date'       => 'nullable|date',

            'feeding_type'            => 'nullable|in:Breastfed,Formula,Mixed',
            'solids_introduced'       => 'nullable|in:Yes,No',
            'solids_age'              => 'nullable|integer|min:0',
            'appetite'                => 'nullable|in:Good,Poor',
            'weight_gain'             => 'nullable|in:Normal,Not gaining',
            'diet_recall'             => 'nullable|string|max:255',

            'temp'                    => 'nullable|numeric',
            'hr'                      => 'nullable|integer',
            'rr'                      => 'nullable|integer',
            'bp_systolic'             => 'nullable|integer',
            'bp_diastolic'            => 'nullable|integer',
            'o2_sat'                  => 'nullable|integer|min:0|max:100',

            'weight'                  => 'nullable|numeric|min:0',
            'height'                  => 'nullable|numeric|min:0',
            'muac'                    => 'nullable|numeric|min:0',
        ];

        $data = $request->validate($rules);

        OpdSubmission::create([
            'form_id' => $template->id,
            'user_id' => auth()->id(),
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.pedia.index')
            ->with('success', 'Pedia triage submission saved!');
    }

    /**
     * Display the specified pedia triage submission.
     */
    public function show(OpdSubmission $submission)
    {
        // load the template if your show view needs it
        $submission->load('form');

        return view('opd_forms.pedia_triage.show', compact('submission'));
    }

public function edit(OpdSubmission $submission)
{
    return view('opd_forms.pedia_triage.edit', [
        'patients'   => Patient::all(),
        'submission' => $submission,
        'pediaForm'  => $submission->answers,
        'postRoute'  => route('triage.pedia.update', ['pedia' => $submission->id]),
    ]);
}
    /**
     * Update the specified pedia triage submission.
     */
    public function update(Request $request, OpdSubmission $submission)
    {
        // same validation rules as store
        $rules = collect((new self)->store)->filter()->all();

        $data = $request->validate([
            'main_concern'            => 'nullable|string|max:255',
            'date_started'            => 'nullable|date',
            'progression'             => 'nullable|in:Improving,Worsening,Same',
            // ... (repeat all rules from store)
            'muac'                    => 'nullable|numeric|min:0',
        ]);

        $submission->update([
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.pedia.index')
            ->with('success', 'Pedia triage submission updated!');
    }

    /**
     * Remove the specified pedia triage submission from storage.
     */
    public function destroy(OpdSubmission $submission)
    {
        $submission->delete();

        return redirect()
            ->route('triage.pedia.index')
            ->with('success', 'Pedia triage submission deleted.');
    }
}
