{{-- resources/views/visits/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">
      <i class="bi bi-clipboard-data me-2"></i>
      Visits • {{ $patient->name }}
    </h1>
    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Patients
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="table-responsive p-3">
      <table class="table table-striped align-middle">
        <thead class="table-light">
          <tr class="text-center">
            <th>Date/Time</th>
            <th>Department</th>
            <th style="width:160px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse ($visits as $v)
            <tr class="text-center">
              <td>{{ \Carbon\Carbon::parse($v->visited_at)->format('Y-m-d H:i') }}</td>
              <td>{{ optional($v->department)->name ?? '—' }}</td>
              <td>
                <a href="{{ route('patients.visits.show', ['patient' => $patient, 'visit' => $v->id]) }}"
                   class="btn btn-sm btn-primary">
                  <i class="bi bi-eye"></i> View
                </a>
                <a href="{{ route('follow-up-opd-forms.create', [
                           'patient_id'    => $patient->id,
                           'department_id' => $v->department_id
                        ]) }}"
                   class="btn btn-sm btn-success">
                  <i class="bi bi-journal-medical"></i> Follow-Up
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="3" class="text-center text-muted">No visits recorded</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if ($visits->hasPages())
      <div class="card-footer py-2">
        {{ $visits->links() }}
      </div>
    @endif
  </div>
</div>
@endsection
