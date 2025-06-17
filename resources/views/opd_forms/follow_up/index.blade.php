{{-- resources/views/opd_forms/follow_up/index.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="container-fluid px-4">
  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
      <i class="bi bi-journal-medical me-2"></i>
      OPD Follow-Up Submissions
    </h1>
    <a href="{{ route('follow-up-opd-forms.create') }}" class="btn btn-success">
      <i class="bi bi-plus-lg me-1"></i> New Follow-Up
    </a>
  </div>

  {{-- Success Flash --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped align-middle mb-0">
          <thead class="table-light">
            <tr class="text-center">
              <th style="width: 50px;">#</th>
              <th>Patient</th>
              <th>Department</th>
              <th>Date Created</th>
              <th style="width: 180px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($submissions as $idx => $submission)
              <tr class="text-center">
                <td>{{ $idx + 1 }}</td>
                <td class="text-start">
                  {{-- Patient → OpdSubmission has patient relation --}}
                  {{ optional($submission->patient)->name ?? '—' }}
                </td>
                <td>
                  {{-- Department name was stored on submission --}}
                  {{ optional(\App\Models\Queue::find($submission->department_id))->name ?? '—' }}
                </td>
                <td>
                  {{ $submission->created_at->format('Y-m-d H:i') }}
                </td>
                <td>
                  <div class="d-flex justify-content-center gap-1">
                    {{-- View --}}
                    <a href="{{ route('follow-up-opd-forms.show', $submission) }}"
                       class="btn btn-sm btn-primary"
                       title="View Details">
                      <i class="bi bi-eye"></i>
                    </a>

                

                    {{-- Delete --}}
                    <form action="{{ route('follow-up-opd-forms.destroy', $submission) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this record?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                        <i class="bi bi-trash"></i>
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center text-muted py-4">
                  No follow-up records found.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
