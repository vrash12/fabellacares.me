{{-- resources/views/opd_forms/follow_up/show.blade.php --}}
@extends('layouts.encoder')

@section('content')
  <div class="container py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0">
        <i class="bi bi-journal-text me-2"></i>
        Follow‐Up Submission #{{ $submission->id }}
      </h1>
      <div>
        {{-- Back to Index --}}
        <a href="{{ route('follow-up-opd-forms.index') }}"
           class="btn btn-outline-secondary me-2">
          <i class="bi bi-arrow-left-circle me-1"></i>
          Back to List
        </a>

        {{-- Edit --}}
        <a href="{{ route('follow-up-opd-forms.edit', [
                      'follow_up_opd_form' => $submission->id
                    ]) }}"
           class="btn btn-primary">
          <i class="bi bi-pencil-square me-1"></i>
          Edit
        </a>
      </div>
    </div>

    {{-- Main Card --}}
    <div class="card shadow-sm mb-5">
      <div class="card-header bg-primary text-white">
        Submission Details
      </div>
      <div class="card-body">
        {{-- Patient Info --}}
        <h5 class="card-title">Patient</h5>
        <p class="mb-4">
          <strong>Name:</strong>
          {{ optional($submission->patient)->name ?? '— Unassigned' }}
        </p>

        {{-- Raw JSON Answers --}}
        <h5 class="card-title">Answers (Raw JSON)</h5>
        <pre class="p-3 bg-light border rounded">
{{ json_encode($submission->answers, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}
        </pre>
      </div>
      <div class="card-footer text-end bg-white">
        <small class="text-muted">
          Created on {{ $submission->created_at->format('F j, Y — g:i A') }}
        </small>
      </div>
    </div>
  </div>
@endsection
