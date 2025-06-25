<?php

namespace App\Http\Controllers;

use App\Models\OpdForm;
use App\Models\OpdSubmission;
use Illuminate\Http\Request;

class InternalConsultationController extends Controller
{
   public function __construct()
{
   $this->middleware(['auth']);
}


    /**
     * Display a listing of internal medicine consultation submissions.
     */
    public function index()
    {
        $submissions = OpdSubmission::with('form')
            ->whereHas('form', fn($q) => $q->where('form_no', 'OPD-F-10'))  // adjust form_no as needed
            ->latest()
            ->get();

        return view('opd_forms.internal_consultation.index', compact('submissions'));
    }

    /**
     * Show the form for creating a new consultation.
     */
    public function create()
    {
        return view('opd_forms.internal_consultation.create', [
            'consultForm' => null,
             'patient'     => null,   
            'postRoute'   => route('consult.internal.store'),
        ]);
    }

    /**
     * Store a newly created consultation submission.
     */
    public function store(Request $request)
    {
        $template = OpdForm::where('form_no', 'OPD-F-10')->firstOrFail(); // adjust form_no

        $rules = [
              'patient_id'                  => 'required|exists:patients,id',
            'main_complaint'               => 'nullable|string|max:255',

            'started_when'                 => 'nullable|date',
            'duration'                     => 'nullable|string|max:255',
            'progression'                  => 'nullable|in:Better,Worse,Same',

            'other_symptoms'               => 'nullable|array',
            'other_symptoms.*'             => 'required|string',
            'other_symptoms_other'         => 'nullable|string|max:255',

            'past_illnesses'               => 'nullable|array',
            'past_illnesses.*'             => 'required|string',
            'past_illnesses_other'         => 'nullable|string|max:255',

            'current_medicines'            => 'nullable|string|max:255',
            'allergies'                    => 'nullable|string|max:255',

            'bp_systolic'                  => 'nullable|integer',
            'bp_diastolic'                 => 'nullable|integer',
            'hr'                           => 'nullable|integer',
            'rr'                           => 'nullable|integer',
            'temperature'                  => 'nullable|numeric',
            'height'                       => 'nullable|numeric',
            'weight'                       => 'nullable|numeric',
            'blood_sugar'                  => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        OpdSubmission::create([
            'form_id' => $template->id,
             'user_id'    => auth()->id(), 
            'user_id' => auth()->id(),
            'answers' => $data,
        ]);

        return redirect()
            ->route('consult.internal.index')
            ->with('success', 'Consultation saved!');
    }

    /**
     * Display the specified consultation.
     */
    public function show(OpdSubmission $submission)
    {
        $submission->load('form');

        return view('opd_forms.internal_consultation.show', compact('submission'));
    }

    /**
     * Show the form for editing the specified consultation.
     */
    public function edit(OpdSubmission $submission)
    {
        $submission->load('form');

        return view('opd_forms.internal_consultation.edit', [
            'consultForm' => $submission->answers,
             'patient'     => $submission->patient, 
            'postRoute'   => route('consult.internal.update', $submission),
        ]);
    }

    /**
     * Update the specified consultation.
     */
    public function update(Request $request, OpdSubmission $submission)
    {
        $rules = [
            'main_complaint'               => 'nullable|string|max:255',
            'started_when'                 => 'nullable|date',
            'duration'                     => 'nullable|string|max:255',
            'progression'                  => 'nullable|in:Better,Worse,Same',

            'other_symptoms'               => 'nullable|array',
            'other_symptoms.*'             => 'required|string',
            'other_symptoms_other'         => 'nullable|string|max:255',

            'past_illnesses'               => 'nullable|array',
            'past_illnesses.*'             => 'required|string',
            'past_illnesses_other'         => 'nullable|string|max:255',

            'current_medicines'            => 'nullable|string|max:255',
            'allergies'                    => 'nullable|string|max:255',

            'bp_systolic'                  => 'nullable|integer',
            'bp_diastolic'                 => 'nullable|integer',
            'hr'                           => 'nullable|integer',
            'rr'                           => 'nullable|integer',
            'temperature'                  => 'nullable|numeric',
            'height'                       => 'nullable|numeric',
            'weight'                       => 'nullable|numeric',
            'blood_sugar'                  => 'nullable|numeric',
        ];

        $data = $request->validate($rules);

        $submission->update([ 'patient_id' => $data['patient_id'],'answers' => $data]);

        return redirect()
            ->route('consult.internal.index')
            ->with('success', 'Consultation updated!');
    }

    /**
     * Remove the specified consultation from storage.
     */
    public function destroy(OpdSubmission $submission)
    {
        $submission->delete();

        return redirect()
            ->route('consult.internal.index')
            ->with('success', 'Consultation deleted.');
    }
}
