{{-- resources/views/patients/show.blade.php --}}
@extends('layouts.admin')

@section('content')
  <div class="container py-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0">
        <i class="bi bi-person-lines-fill me-2"></i>
        Patient Record: {{ $patient->name }}
      </h1>
      <div>
        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-left-circle me-1"></i> Back to List
        </a>
      </div>
    </div>

    {{-- Tab‐style headers (purely visual; no JS toggling) --}}
    <ul class="nav nav-tabs mb-4" role="tablist" style="pointer-events: none;">
      <li class="nav-item" role="presentation">
        <span class="nav-link active">
          <i class="bi bi-person me-1"></i> Overview
        </span>
      </li>
      <li class="nav-item" role="presentation">
        <span class="nav-link">
          <i class="bi bi-journal-text me-1"></i> Patient History
        </span>
      </li>
    </ul>

    {{-- ==================== OVERVIEW SECTION (always visible) ==================== --}}
    <div class="p-4 mb-4 bg-white shadow-sm rounded">
      {{-- “Basic Info” Card --}}
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
          <i class="bi bi-info-circle me-2"></i> Basic Information
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>Name:</strong> {{ $patient->name }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Birth Date:</strong>
              {{ $patient->birth_date
                  ? \Carbon\Carbon::parse($patient->birth_date)->format('F j, Y')
                  : '—' }}
            </div>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <strong>Contact No.:</strong> {{ $patient->contact_no ?? '—' }}
            </div>
            <div class="col-md-6 mb-3">
              <strong>Address:</strong> {{ $patient->address ?? '—' }}
            </div>
          </div>
          <div class="row">
            <div class="col-md-4 mb-3">
              <strong>Sex:</strong> {{ ucfirst($patient->profile->sex ?? '—') }}
            </div>
            <div class="col-md-4 mb-3">
              <strong>Religion:</strong> {{ $patient->profile->religion ?? '—' }}
            </div>
            <div class="col-md-4 mb-3">
              <strong>Age:</strong>
              @if($patient->birth_date)
                {{ \Carbon\Carbon::parse($patient->birth_date)->age }} years
              @else
                —
              @endif
            </div>
          </div>
        </div>
      </div>

      {{-- “Patient Details & Visits Summary” Card --}}
      <div class="card mb-4 shadow-sm">
        <div class="card-header bg-success text-white">
          <i class="bi bi-clipboard-data me-2"></i> Patient Details & Visits
        </div>
        <div class="card-body">
          <div class="row mb-3">
            @if(isset($patient->record_no))
              <div class="col-md-4 mb-2">
                <strong>Record No.:</strong> {{ $patient->record_no }}
              </div>
            @endif

            <div class="col-md-4 mb-2">
              <strong>Total Visits:</strong>
              <span class="badge bg-info">{{ $patient->visits->count() }}</span>
            </div>

            @if(isset($patient->profile->blood_type))
              <div class="col-md-4 mb-2">
                <strong>Blood Type:</strong> {{ $patient->profile->blood_type }}
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- ==================== PATIENT HISTORY SECTION (always visible) ==================== --}}
    <div class="p-4 bg-white shadow-sm rounded">
      <div class="card shadow-sm h-100">
        <div class="card-header bg-secondary text-white">
          <i class="bi bi-clock-history me-2"></i> Past Diagnoses & Records
        </div>
        <div class="card-body d-flex flex-column">
          @php
            $visits    = $patient->visits;
            $highRisks = $patient->highRiskSubmissions;
          @endphp

          {{-- If nothing in either collection, show “no history” --}}
          @if($visits->isEmpty() && $highRisks->isEmpty())
            <div class="text-center text-muted py-5">
              <i class="bi bi-info-circle me-1"></i>
              No history available for this patient.
            </div>
          @else
            {{-- ── Regular OPD Visits ── --}}
            <h5 class="fw-bold mb-3">
              <i class="bi bi-hospital me-1"></i> Regular OPD Visits
            </h5>
            <div class="table-responsive mb-4">
              <table class="table table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                  <tr>
                    <th>Date</th>
                    <th>Form No.</th>
                    <th>Type</th>
                    <th>Diagnosis / Notes</th>
                    <th class="text-center" style="width:140px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($visits as $visit)
                    <tr>
                      <td class="text-center">{{ $visit->created_at->format('Y-m-d') }}</td>
                      <td class="text-center">{{ $visit->form->form_no ?? '—' }}</td>
                      <td class="text-center">{{ $visit->form->name ?? '—' }}</td>
                      <td>
                        {{ \Illuminate\Support\Str::limit(
                          $visit->answers['diagnosis']
                          ?? $visit->answers['chief_complaint']
                          ?? '-',
                          60
                        ) }}
                      </td>
                      <td class="text-center">
                        <a
                          href="{{ route('opd_submissions.show', $visit) }}"
                          class="btn btn-sm btn-secondary"
                          title="View Full Submission"
                        >
                          <i class="bi bi-eye"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-1"></i> No regular visits.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>

            {{-- ── High-Risk Records (OPD-F-09) ── --}}
            <h5 class="fw-bold mb-3">
              <i class="bi bi-exclamation-triangle me-1"></i> High-Risk Records (OPD-F-09)
            </h5>
            <div class="table-responsive">
              <table class="table table-bordered align-middle mb-0">
                <thead class="table-light text-center">
                  <tr>
                    <th>Date</th>
                    <th>Risk Factors</th>
                    <th class="text-center" style="width:140px;">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  @forelse($highRisks as $risk)
                    <tr>
                      <td class="text-center">{{ $risk->created_at->format('Y-m-d') }}</td>
                      <td>
                        @php
                          $risks = $risk->answers['risks'] ?? [];
                          $preview = collect($risks)->take(3)->join(', ');
                        @endphp
                        {{ $preview }}{{ count($risks) > 3 ? ' …' : '' }}
                      </td>
                      <td class="text-center">
                        <a
                          href="{{ route('high-risk-opd-forms.show', $risk) }}"
                          class="btn btn-sm btn-secondary"
                          title="View High-Risk Submission"
                        >
                          <i class="bi bi-eye"></i>
                        </a>
                      </td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="3" class="text-center text-muted py-4">
                        <i class="bi bi-inbox me-1"></i> No high-risk submissions.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection

{{-- Because we removed the BootstrapJS tab‐toggling, you no longer need bootstrap.bundle.js here. --}}
