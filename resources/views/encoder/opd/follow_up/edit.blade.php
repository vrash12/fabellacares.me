{{-- resources/views/opd_forms/follow_up/edit.blade.php --}}
@extends('layouts.encoder')

@section('content')
  <div class="container py-4">
    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
      <h1 class="h3 mb-0">
        <i class="bi bi-pencil-square me-2"></i>
        Edit Follow-Up #{{ $submission->id }}
      </h1>
      <div>
        {{-- View (show) --}}
        <a href="{{ route('follow-up-opd-forms.show', [
                      'follow_up_opd_form' => $submission->id
                    ]) }}"
           class="btn btn-outline-secondary">
          <i class="bi bi-eye me-1"></i> View
        </a>
      </div>
    </div>

    <div class="card shadow-sm">
      <div class="card-header bg-info text-white">
        Modify Answers
      </div>
      <div class="card-body">
        <form action="{{ route('follow-up-opd-forms.update', [
                              'follow_up_opd_form' => $submission->id
                          ]) }}"
              method="POST">
          @csrf
          @method('PUT')

          {{-- Example field: followups JSON array --}}
          <div class="mb-3">
            <label class="form-label">Follow-Up Entries (JSON Array)</label>
            <textarea name="followups"
                      class="form-control @error('followups') is-invalid @enderror"
                      rows="6"
                      placeholder='[{"date":"2025-05-01","gest_weeks":12,"weight":60,"bp":"120/80","remarks":"..."}]'>{{ old('followups', json_encode($submission->answers['followups'] ?? [], JSON_UNESCAPED_SLASHES)) }}</textarea>
            @error('followups')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          {{-- You can add any other follow-up fields or patient_id selects here… --}}

          <div class="d-flex justify-content-end">
            <button type="submit" class="btn btn-success me-2">
              <i class="bi bi-check-circle me-1"></i> Save Changes
            </button>
            <a href="{{ route('follow-up-opd-forms.show', [
                          'follow_up_opd_form' => $submission->id
                        ]) }}"
               class="btn btn-secondary">
              Cancel
            </a>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection
