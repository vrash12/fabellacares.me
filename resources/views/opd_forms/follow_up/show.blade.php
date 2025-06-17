@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';

    /** Convenience shortcuts */
    $a     = $submission->answers ?? [];
    $meta  = [
        'Sex'        => ucfirst($a['sex']    ?? '—'),
        'Age'        => $a['age']           ?? '—',
        'Department' => $departmentName     ?: '—',
    ];
    $rows  = $a['followups'] ?? [];
@endphp

@extends($layout)

@section('content')
<div class="container py-4">
  {{-- Header bar --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
      <i class="bi bi-journal-text me-2"></i>
      Follow-Up #{{ $submission->id }}
    </h1>
    <div class="btn-group">
      <a href="{{ route('follow-up-opd-forms.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left-circle me-1"></i> Back
      </a>
      <a href="{{ route('follow-up-opd-forms.edit', $submission) }}" class="btn btn-primary">
        <i class="bi bi-pencil-square me-1"></i> Edit
      </a>
    </div>
  </div>

  {{-- Patient & metadata card --}}
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">Patient Information</div>
    <div class="card-body">
      <div class="row mb-2">
        <div class="col-sm-3 fw-semibold">Name</div>
        <div class="col-sm-9">
          {{ optional($submission->patient)->name ?? '— Unassigned' }}
        </div>
      </div>
      @foreach($meta as $label => $value)
        <div class="row mb-2">
          <div class="col-sm-3 fw-semibold">{{ $label }}</div>
          <div class="col-sm-9">{{ $value }}</div>
        </div>
      @endforeach
    </div>
    <div class="card-footer text-end small text-muted">
      Created: {{ $submission->created_at->format('M d, Y H:i') }} &nbsp;|&nbsp;
      Updated: {{ $submission->updated_at->format('M d, Y H:i') }}
    </div>
  </div>

  {{-- Follow-up table --}}
  <div class="card shadow-sm mb-4">
    <div class="card-header bg-success text-white">Follow-Up Visits</div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Date</th>
              <th class="text-center">Gest. Weeks</th>
              <th class="text-center">Weight (kg)</th>
              <th class="text-center">BP</th>
              <th>Remarks</th>
            </tr>
          </thead>
          <tbody>
            @forelse($rows as $i => $r)
              <tr>
                <td>{{ $i+1 }}</td>
                <td>{{ \Carbon\Carbon::parse($r['date'] ?? null)->format('Y-m-d') ?: '—' }}</td>
                <td class="text-center">{{ $r['gest_weeks'] ?? '—' }}</td>
                <td class="text-center">{{ $r['weight']     ?? '—' }}</td>
                <td class="text-center">{{ $r['bp']         ?? '—' }}</td>
                <td>{{ $r['remarks'] ?? '—' }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center py-4 text-muted">No follow-up entries.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- Optional raw JSON toggle --}}
  <p>
    <button class="btn btn-outline-dark btn-sm" data-bs-toggle="collapse" data-bs-target="#rawJson">
      <i class="bi bi-code-slash me-1"></i> Toggle Raw JSON
    </button>
  </p>
  <div id="rawJson" class="collapse">
    <pre class="p-3 bg-light border rounded">
{{ json_encode($a, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
    </pre>
  </div>
</div>
@endsection
