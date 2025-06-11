<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EncoderFollowUpOpdFormController extends Controller
{
   public function __construct()
    {
        $this->middleware(['auth','role:encoder']);
    }

    /**
     * Index: list all OPD follow-up submissions.
     */
    public function index()
    {
        $submissions = OpdSubmission::with('patient.user', 'form')
            ->whereHas('form', fn($q) => $q->where('form_no', 'OPD-F-08'))
            ->latest()
            ->get();

        return view('opd_forms.follow_up.index', compact('submissions'));
    }

public function create(Request $request)
{
    // 1) Retrieve the form template (if needed)
    $template = OpdForm::where('form_no', 'OPD-F-08')->firstOrFail();

    // 2) Load all patients (only id + name) so the datalist has options
    $patients = Patient::select('id', 'name')
                       ->orderBy('name')
                       ->get();

    // 3) Determine which department was passed (e.g. via ?department_id=123)
    //    or set to null if not provided
    $selectedDeptId = $request->query('department_id'); // might be null
    $departmentName = $selectedDeptId
        ? optional(Queue::find($selectedDeptId))->name
        : '';

    // 4) Load all queues so your Blade can render a <select> of departments
    $queues = Queue::all();

    // 5) If the controller also passed a prefillPatient ID in the query string:
    $prefillPatientId = $request->query('prefillPatient');

  return view('opd_forms.follow_up.create', [
    'patients'       => $patients,
    'departmentName' => $departmentName,
    'prefillPatient' => $prefillPatientId,
    'prefillDept'    => $selectedDeptId,
    'opd_form'       => null,
    'queues'         => $queues,
]);

}


    /**
     * Store a newly created follow-up submission.
     */
 public function store(Request $request)
    {
    $validated = $request->validate([
            'patient_id'      => 'required|exists:patients,id',
   // after (optional)
'department_id'   => 'nullable|exists:queues,id',

            'last_name'       => 'nullable|string|max:255',
            'given_name'      => 'nullable|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'age'             => 'nullable|integer|min:0',
            'sex'             => 'nullable|in:male,female',
            'followups'              => 'nullable|array',
            'followups.*.date'       => 'nullable|date',
            'followups.*.gest_weeks' => 'nullable|integer|min:0',
            'followups.*.weight'     => 'nullable|numeric',
            'followups.*.bp'         => 'nullable|string|max:20',
            'followups.*.remarks'    => 'nullable|string',
        ]);

        $template = OpdForm::where('form_no', 'OPD-F-08')->firstOrFail();

        // ② Create the OpdSubmission record
        $submission = OpdSubmission::create([
            'form_id'      => $template->id,
            'user_id'      => auth()->id(),
            'patient_id'   => $validated['patient_id'],
            'department_id'=> $validated['department_id'] ?? null,
            'answers'      => $validated,
        ]);

        // ③ Record the visit
        Visit::create([
            'patient_id'    => $validated['patient_id'],
            'visited_at'    => now(),
            'department_id' => $validated['department_id'] ?? null
,
            'queue_id'      => null,
            'token_id'      => null,
        ]);

        return redirect()
            ->route('follow-up-opd-forms.index')
            ->with('success','Follow-up record saved!');
    }
    /**
     * Display the specified submission.
     */
   public function show(OpdSubmission $submission)
{
    // 1) Load relations
    $submission->load('form', 'patient.user');

    // 2) Still load all queues so we can look up names
    $queues = Queue::all();

    // 3) Extract the existing “followups” array (or default to one blank row)
    $rows = data_get($submission->answers, 'followups', [[]]);

    // 4) Find out which department_id was used when saving
    $selectedDeptId   = $submission->department_id;
    $selectedDeptName = optional(Queue::find($selectedDeptId))->name;

    // 5) Prefill patient ID just in case the view needs it
    $prefillPatientId = $submission->patient_id;

    return view('opd_forms.follow_up.show', [
        'submission'       => $submission,
        'queues'           => $queues,
        'rows'             => $rows,
        'selectedDeptId'   => $selectedDeptId,
        'departmentName'   => $selectedDeptName,
        'prefillPatient'   => $prefillPatientId,
    ]);
}

    /**
     * Show the form for editing the specified submission.
     */
public function edit(OpdSubmission $submission)
{
    // 1) Eager‐load relations if needed
    $submission->load('form', 'patient.user');

    // 2) Load all patients for the datalist (same as create)
    $patients = Patient::select('id', 'name')
                       ->orderBy('name')
                       ->get();

    // 3) Load all queues (departments) so that each row’s <select> can be populated
    $queues = Queue::all();

    // 4) Extract any existing follow‐up “rows” from answers
    //    If answers does not contain a 'followups' key, default to one empty row
    $rows = data_get($submission->answers, 'followups', [[]]);

    // 5) Determine which department was originally saved on this submission
    $selectedDeptId   = $submission->department_id;
    $selectedDeptName = optional(Queue::find($selectedDeptId))->name;

    // 6) If your form requires you to know which patient was selected previously:
    $prefillPatientId = $submission->patient_id;

    // 7) Build the “update” route (if you pass it explicitly to the Blade)
    $postRoute = route('follow-up-opd-forms.update', [
        'follow_up_opd_form' => $submission->id
    ]);

    return view('opd_forms.follow_up.edit', [
        'submission'       => $submission,
        'patients'         => $patients,
        'queues'           => $queues,
        'rows'             => $rows,
        'selectedDeptId'   => $selectedDeptId,
        'departmentName'   => $selectedDeptName,
        'prefillPatient'   => $prefillPatientId,
        'postRoute'        => $postRoute,
    ]);
}


    /**
     * Update the specified submission in storage.
     */
 public function update(Request $request, OpdSubmission $submission)
    {
        // 1) Re‐validate exactly the same fields as in store(), including department_id
        $validated = $request->validate([
            'patient_id'             => 'required|exists:patients,id',
         // after (optional)
'department_id'   => 'nullable|exists:queues,id',

            'last_name'              => 'nullable|string|max:255',
            'given_name'             => 'nullable|string|max:255',
            'middle_name'            => 'nullable|string|max:255',
            'age'                    => 'nullable|integer|min:0',
            'sex'                    => 'nullable|in:male,female',
            'followups'              => 'nullable|array',
            'followups.*.date'       => 'nullable|date',
            'followups.*.gest_weeks' => 'nullable|integer|min:0',
            'followups.*.weight'     => 'nullable|numeric',
            'followups.*.bp'         => 'nullable|string|max:20',
            'followups.*.remarks'    => 'nullable|string',
        ]);

        // 2) Update the OpdSubmission itself
        $submission->patient_id    = $validated['patient_id'];
        $submission->department_id = $validated['department_id'] ?? null;   // ⇦ NEW
        $submission->answers       = $validated;
        $submission->save();


        return redirect()
            ->route('follow-up-opd-forms.index')
            ->with('success', 'Follow-up record updated!');
    }
    /**
     * Remove the specified submission from storage.
     */
public function destroy(OpdSubmission $submission)
    {
        $submission->delete();
        return redirect()
            ->route('follow-up-opd-forms.index')
            ->with('success', 'Follow-up record deleted.');
    }
}
