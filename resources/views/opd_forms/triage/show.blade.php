{{-- resources/views/opd_forms/triage/show.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="container col-lg-8">
  <div class="page-header mb-4">
    <h2 class="m-0">Triage Form Details</h2>
  </div>

  {{-- Patient --}}
  <div class="mb-3">
    <strong>Patient:</strong>
    {{ $triageForm->patient->name }}
  </div>

  {{-- I. Tetanus Series --}}
  <h4>I. Tetanus Series</h4>
  <table class="table table-sm mb-4">
    <thead>
      <tr>
        <th>Dose</th>
        <th>Date</th>
        <th>Signature</th>
      </tr>
    </thead>
    <tbody>
      @for($i = 1; $i <= 5; $i++)
      <tr>
        <td>T{{ $i }}</td>
        <td>{{ optional($triageForm)->{"tetanus_t{$i}_date"} ?: '—' }}</td>
        <td>{{ optional($triageForm)->{"tetanus_t{$i}_signature"} ?: '—' }}</td>
      </tr>
      @endfor
    </tbody>
  </table>

  {{-- II. Present Health Problems --}}
  <h4>II. Present Health Problems</h4>
  <ul class="mb-4">
    @forelse($triageForm->present_health_problems ?? [] as $problem)
      <li>{{ $problem }}</li>
    @empty
      <li class="text-muted">None</li>
    @endforelse
    @if($triageForm->present_problems_other)
      <li><em>Other:</em> {{ $triageForm->present_problems_other }}</li>
    @endif
  </ul>

  {{-- III. Danger Signs --}}
  <h4>III. Danger Signs</h4>
  <ul class="mb-4">
    @forelse($triageForm->danger_signs ?? [] as $sign)
      <li>{{ $sign }}</li>
    @empty
      <li class="text-muted">None</li>
    @endforelse
    @if($triageForm->danger_signs_other)
      <li><em>Other:</em> {{ $triageForm->danger_signs_other }}</li>
    @endif
  </ul>

  {{-- IV. OB History --}}
  <h4>IV. OB History</h4>
  <ul class="mb-4">
    @forelse($triageForm->ob_history ?? [] as $entry)
      <li>{{ $entry }}</li>
    @empty
      <li class="text-muted">None recorded</li>
    @endforelse
  </ul>

  {{-- V. Family Planning & PNC --}}
  <h4>V. Family Planning &amp; PNC</h4>
  <p>
    <strong>Family Planning:</strong> {{ $triageForm->family_planning ?: '—' }}<br>
    <strong>Previous PNC:</strong> {{ $triageForm->prev_pnc ?: '—' }}
  </p>

  {{-- VI. Dates & Counts --}}
  <h4>VI. Dates &amp; Counts</h4>
  <p>
    <strong>LMP:</strong> {{ $triageForm->lmp ?: '—' }}<br>
    <strong>EDC:</strong> {{ $triageForm->edc ?: '—' }}<br>
    <strong>Gravida:</strong> {{ $triageForm->gravida ?? '—' }}<br>
    <strong>Parity (T/P/A/L):</strong>
      {{ $triageForm->parity_t ?? '—' }} /
      {{ $triageForm->parity_p ?? '—' }} /
      {{ $triageForm->parity_a ?? '—' }} /
      {{ $triageForm->parity_l ?? '—' }}<br>
    <strong>Age of Gestation (weeks):</strong> {{ $triageForm->aog_weeks ?? '—' }}
  </p>

  {{-- VII. Physical Exam Log --}}
  <h4>VII. Physical Exam Log</h4>
  <ul class="mb-4">
    @forelse($triageForm->physical_exam_log ?? [] as $log)
      <li>{{ $log }}</li>
    @empty
      <li class="text-muted">None recorded</li>
    @endforelse
  </ul>

  {{-- VIII. Delivery & Newborn --}}
  <h4>VIII. Delivery &amp; Newborn</h4>
  <p>
    <strong>Prepared By:</strong> {{ $triageForm->prepared_by ?: '—' }}<br>
    <strong>Blood Type:</strong> {{ $triageForm->blood_type ?: '—' }}<br>
    <strong>Delivery Type:</strong> {{ $triageForm->delivery_type ?: '—' }}<br>
    <strong>Birth Weight:</strong> {{ $triageForm->birth_weight ?? '—' }} kg<br>
    <strong>Birth Length:</strong> {{ $triageForm->birth_length ?? '—' }} cm<br>
    <strong>Apgar Scores:</strong>
    A {{ $triageForm->apgar_appearance ?? '–' }},
    P {{ $triageForm->apgar_pulse ?? '–' }},
    G {{ $triageForm->apgar_grimace ?? '–' }},
    A {{ $triageForm->apgar_activity ?? '–' }},
    R {{ $triageForm->apgar_respiration ?? '–' }}
  </p>

  <div class="mt-4">
    <a href="{{ route('opd_forms.triage.index') }}" class="btn btn-secondary">
      ← Back to List
    </a>
    <a href="{{ route('opd_forms.triage.edit', $triageForm) }}" class="btn btn-primary ms-2">
      Edit
    </a>
  </div>
</div>
@endsection
