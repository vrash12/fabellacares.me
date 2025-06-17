<?php

namespace App\Http\Controllers;

use App\Models\OpdForm;
use App\Models\OpdSubmission;
use App\Models\Patient;
use App\Models\Queue;
use App\Models\Visit;
use Illuminate\Http\Request;

class FollowUpOpdFormController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $submissions = OpdSubmission::with('patient.user','form')
            ->whereHas('form', fn($q) => $q->where('form_no','OPD-F-08'))
            ->latest()
            ->get();

        return view('opd_forms.follow_up.index', compact('submissions'));
    }

    public function create(Request $request)
    {
        $patients       = Patient::select('id','name')->orderBy('name')->get();
        $queues         = Queue::all();
        $prefillPatient = $request->query('prefillPatient');
        $prefillDept    = $request->query('department_id');
        $departmentName = $prefillDept
            ? optional(Queue::find($prefillDept))->name
            : '';

        return view('opd_forms.follow_up.create', [
            'patients'       => $patients,
            'queues'         => $queues,
            'prefillPatient' => $prefillPatient,
            'prefillDept'    => $prefillDept,
            'departmentName' => $departmentName,
            'opd_form'       => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id'             => 'required|exists:patients,id',
            'department_id'          => 'nullable|exists:queues,id',
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

        $template = OpdForm::where('form_no','OPD-F-08')->firstOrFail();

        $submission = OpdSubmission::create([
            'form_id'      => $template->id,
            'user_id'      => auth()->id(),
            'patient_id'   => $validated['patient_id'],
            'department_id'=> $validated['department_id'] ?? null,
            'answers'      => $validated,
        ]);

        Visit::create([
            'patient_id'    => $validated['patient_id'],
            'visited_at'    => now(),
            'department_id' => $validated['department_id'] ?? null,
            'queue_id'      => null,
            'token_id'      => null,
        ]);

        return redirect()
            ->route('follow-up-opd-forms.index')
            ->with('success','Follow‐up record saved!');
    }

    public function show(OpdSubmission $submission)
    {
        $submission->load('patient.user','form');

        $queues         = Queue::all();
        $rows           = data_get($submission->answers,'followups',[[]]);
        $selectedDeptId = $submission->department_id;
        $departmentName = optional(Queue::find($selectedDeptId))->name;
        $prefillPatient = $submission->patient_id;

        return view('opd_forms.follow_up.show', compact(
            'submission',
            'queues',
            'rows',
            'selectedDeptId',
            'departmentName',
            'prefillPatient'
        ));
    }

    public function edit(OpdSubmission $submission)
    {
        $submission->load('patient.user','form');

        $patients       = Patient::select('id','name')->orderBy('name')->get();
        $queues         = Queue::all();
        $rows           = data_get($submission->answers,'followups',[[]]);
        $selectedDeptId = $submission->department_id;
        $departmentName = optional(Queue::find($selectedDeptId))->name;
        $prefillPatient = $submission->patient_id;

        return view('opd_forms.follow_up.edit', [
            'submission'     => $submission,
            'patients'       => $patients,
            'queues'         => $queues,
            'rows'           => $rows,
            'selectedDeptId' => $selectedDeptId,
            'departmentName' => $departmentName,
            'prefillPatient' => $prefillPatient,
        ]);
    }

    public function update(Request $request, OpdSubmission $submission)
    {
        $validated = $request->validate([
            'patient_id'             => 'required|exists:patients,id',
            'department_id'          => 'nullable|exists:queues,id',
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

        $submission->patient_id    = $validated['patient_id'];
        $submission->department_id = $validated['department_id'] ?? null;
        $submission->answers       = $validated;
        $submission->save();

        return redirect()
            ->route('follow-up-opd-forms.index')
            ->with('success','Follow‐up record updated!');
    }

    public function destroy(OpdSubmission $submission)
    {
        $submission->delete();

        return redirect()
            ->route('follow-up-opd-forms.index')
            ->with('success','Follow‐up record deleted.');
    }
}
