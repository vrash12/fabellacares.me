<?php

namespace App\Http\Controllers;

use App\Models\OpdForm;
use App\Models\OpdSubmission;
use Illuminate\Http\Request;

class TeensTriageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of teens triage submissions.
     */
    public function index()
    {
        $submissions = OpdSubmission::with('form')
            ->whereHas('form', fn($q) => $q->where('form_no', 'TRG-TEEN-01'))
            ->latest()
            ->get();

        return view('opd_forms.teens_triage.index', compact('submissions'));
    }

    /**
     * Show the form for creating a new teens triage submission.
     */
    public function create()
    {
        return view('opd_forms.teens_triage.create', [
            'teensForm' => null,
            'postRoute' => route('triage.teens.store'),
        ]);
    }

    /**
     * Store a newly created teens triage submission.
     */
    public function store(Request $request)
    {
        $template = OpdForm::where('form_no', 'TRG-TEEN-01')->firstOrFail();

        $rules = [
            'chief_complaint'            => 'nullable|string|max:255',
            'puberty_onset'              => 'nullable|string|max:255',
            'menarche'                   => 'nullable|string|max:255',
            'emotional_concerns'         => 'nullable|in:Yes,No',
            'emotional_concerns_details' => 'nullable|string|max:255',
            'sexual_activity'            => 'nullable|in:Yes,No',
            'contraceptive_use'          => 'nullable|in:Yes,No',
            'contraceptive_use_type'     => 'nullable|string|max:255',
            'smoking'                    => 'nullable|in:Yes,No',
            'alcohol'                    => 'nullable|in:Yes,No',
            'drugs'                      => 'nullable|in:Yes,No',
            'sleeping_habits'            => 'nullable|in:Normal,Disrupted',
            'nutrition_issues'           => 'nullable|in:Yes,No',
            'vaccination_status'         => 'nullable|in:Complete,Incomplete,Not known',
            'last_vaccines'              => 'nullable|string|max:255',
            'bp_systolic'                => 'nullable|integer',
            'bp_diastolic'               => 'nullable|integer',
            'heart_rate'                 => 'nullable|integer',
            'resp_rate'                  => 'nullable|integer',
            'temperature'                => 'nullable|numeric',
            'height'                     => 'nullable|numeric',
            'weight'                     => 'nullable|numeric',
            'bmi'                         => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        OpdSubmission::create([
            'form_id' => $template->id,
            'user_id' => auth()->id(),
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.teens.index')
            ->with('success', 'Teens triage submission saved!');
    }

    /**
     * Display the specified submission.
     */
    public function show(OpdSubmission $teens)
    {
        $teens->load('form');

        return view('opd_forms.teens_triage.show', [
            'submission' => $teens,
        ]);
    }

    /**
     * Show the form for editing the specified submission.
     */
 public function edit(OpdSubmission $teens)
{
    $teens->load('form');

    return view('opd_forms.teens_triage.edit', [
        'submission' => $teens,
        'teensForm'  => $teens->answers,

        // ← was: route('triage.teens.edit', …)
        //     must be: route('triage.teens.update', …)
        'postRoute'  => route('triage.teens.update', ['teen' => $teens->id]),
    ]);
}



    /**
     * Update the specified submission.
     */
    public function update(Request $request, OpdSubmission $teens)
    {
        $rules = [
            'chief_complaint'            => 'nullable|string|max:255',
            'puberty_onset'              => 'nullable|string|max:255',
            'menarche'                   => 'nullable|string|max:255',
            'emotional_concerns'         => 'nullable|in:Yes,No',
            'emotional_concerns_details' => 'nullable|string|max:255',
            'sexual_activity'            => 'nullable|in:Yes,No',
            'contraceptive_use'          => 'nullable|in:Yes,No',
            'contraceptive_use_type'     => 'nullable|string|max:255',
            'smoking'                    => 'nullable|in:Yes,No',
            'alcohol'                    => 'nullable|in:Yes,No',
            'drugs'                      => 'nullable|in:Yes,No',
            'sleeping_habits'            => 'nullable|in:Normal,Disrupted',
            'nutrition_issues'           => 'nullable|in:Yes,No',
            'vaccination_status'         => 'nullable|in:Complete,Incomplete,Not known',
            'last_vaccines'              => 'nullable|string|max:255',
            'bp_systolic'                => 'nullable|integer',
            'bp_diastolic'               => 'nullable|integer',
            'heart_rate'                 => 'nullable|integer',
            'resp_rate'                  => 'nullable|integer',
            'temperature'                => 'nullable|numeric',
            'height'                     => 'nullable|numeric',
            'weight'                     => 'nullable|numeric',
            'bmi'                        => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        $teens->update([
            'answers' => $data,
        ]);

        return redirect()
            ->route('triage.teens.index')
            ->with('success', 'Teens triage submission updated!');
    }

    /**
     * Remove the specified submission from storage.
     */
    public function destroy(OpdSubmission $teens)
    {
        $teens->delete();

        return redirect()
            ->route('triage.teens.index')
            ->with('success', 'Teens triage submission deleted.');
    }
}
