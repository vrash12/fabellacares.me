{{-- resources/views/opd_forms/teens_triage/show.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';

    // Pull your JSON answers into an object
    $answers = (object) $submission->answers;

    // Fetch the Patient model from the saved patient_id
    $patient = \App\Models\Patient::find(data_get($answers, 'patient_id'));

    // Labels for your triage fields
    $labels = [
        'chief_complaint'            => 'Chief Complaint',
        'puberty_onset'              => 'Puberty Onset',
        'menarche'                   => 'Menarche',
        'emotional_concerns'         => 'Emotional Concerns',
        'emotional_concerns_details' => 'If Yes, Details',
        'sexual_activity'            => 'Sexual Activity',
        'contraceptive_use'          => 'Contraceptive Use',
        'contraceptive_use_type'     => 'If Yes, Type',
        'smoking'                    => 'Smoking',
        'alcohol'                    => 'Alcohol',
        'drugs'                      => 'Drug Use',
        'sleeping_habits'            => 'Sleeping Habits',
        'nutrition_issues'           => 'Nutrition Issues',
        'vaccination_status'         => 'Vaccination Status',
        'last_vaccines'              => 'Last Vaccines & Date',
        'bp_systolic'                => 'BP Systolic (mmHg)',
        'bp_diastolic'               => 'BP Diastolic (mmHg)',
        'heart_rate'                 => 'Heart Rate (bpm)',
        'resp_rate'                  => 'Respiratory Rate (cpm)',
        'temperature'                => 'Temperature (°C)',
        'height'                     => 'Height (cm)',
        'weight'                     => 'Weight (kg)',
        'bmi'                        => 'BMI',
    ];
@endphp

@extends($layout)

@section('content')
  <div class="page-header mb-4">
    <h1 class="h3">Submission #{{ $submission->id }} – Teens Triage</h1>
    <small class="text-muted">
      Filed {{ optional($submission->created_at)->format('M d, Y h:i A') ?? '—' }}
      by <strong>{{ optional($submission->user)->name ?? '—' }}</strong>
      @if($patient)
        | Patient: <strong>{{ $patient->name }}</strong>
      @endif
    </small>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">

      {{-- Patient Information --}}
      <h5 class="mb-3"><i class="bi bi-person-fill me-2"></i> Patient Information</h5>
      <dl class="row mb-4">
        <dt class="col-sm-4 text-secondary small">Name</dt>
        <dd class="col-sm-8 fw-semibold">{{ optional($patient)->name ?? '—' }}</dd>

        {{-- add more patient fields here if you want --}}
        {{-- 
        <dt class="col-sm-4 text-secondary small">Age</dt>
        <dd class="col-sm-8 fw-semibold">{{ $patient ? $patient->age : '—' }}</dd>
        --}}
      </dl>

      {{-- Triage Responses --}}
      <h5 class="mb-3"><i class="bi bi-journal-text me-2"></i> Triage Responses</h5>
      <dl class="row">
        @foreach($labels as $key => $label)
          <dt class="col-sm-4 text-secondary small">{{ $label }}</dt>
          <dd class="col-sm-8 fw-semibold">
            @php $val = data_get($answers, $key); @endphp
            {{ $val !== null && $val !== ''
              ? (is_array($val) ? implode(', ', $val) : $val)
              : '—' }}
          </dd>
        @endforeach
      </dl>
    </div>

    <div class="card-footer bg-white text-end">
      <a href="{{ route('triage.teens.index') }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-chevron-left"></i> Back to list
      </a>
<a href="{{ route('triage.teens.edit', $submission) }}" class="btn btn-primary">
  <i class="bi bi-pencil-square"></i> Edit
</a>
    </div>
  </div>
@endsection
