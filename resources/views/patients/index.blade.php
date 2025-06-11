{{-- resources/views/patients/index.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)


@section('content')
<div class="container-fluid px-4">
  {{-- Page Header --}}
  <div class="page-header bg-gradient-primary rounded-3 p-4 mb-4 text-white shadow-sm">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="h2 mb-1 fw-bold">
          <i class="fas fa-user-md me-2"></i> OB OPD Patients
        </h1>
        <p class="mb-0 opacity-90">Manage obstetrics and gynecology outpatient records</p>
      </div>
    </div>
  </div>

  {{-- Search & Filter --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom-0 py-3">
      <h5 class="card-title mb-0">
        <i class="fas fa-search me-2 text-primary"></i> Search & Filter Patients
      </h5>
    </div>
    <div class="card-body">
      <form method="GET" class="row g-3">
        <div class="col-lg-5 col-md-6">
          <label for="search" class="form-label small text-muted fw-medium">Patient Name</label>
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
              <i class="fas fa-user text-muted"></i>
            </span>
            <input type="text"
                   id="search"
                   name="search"
                   value="{{ request('search') }}"
                   class="form-control border-start-0 ps-0"
                   placeholder="Search by patient name…">
          </div>
        </div>
        <div class="col-lg-3 col-md-4">
          <label for="sex" class="form-label small text-muted fw-medium">Gender</label>
          <select name="sex" id="sex" class="form-select">
            <option value="">All Genders</option>
            <option value="male"   @selected(request('sex')=='male')>Male</option>
            <option value="female" @selected(request('sex')=='female')>Female</option>
          </select>
        </div>
        <div class="col-lg-4 col-md-2">
          <label class="form-label small text-muted fw-medium">&nbsp;</label>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
              <i class="fas fa-filter me-1"></i> Apply Filters
            </button>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-undo me-1"></i> Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Results Table --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="card-title mb-0">
            <i class="fas fa-list me-2 text-success"></i>
            Patient Records
            <span class="badge bg-primary ms-2">{{ count($patients) }}</span>
          </h5>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      @if($patients->isNotEmpty())
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="patients-table">
            <thead class="table-light">
              <tr>
                <th class="py-3 ps-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label text-muted" for="selectAll">Patient Info</label>
                  </div>
                </th>
                <th class="py-3 text-center" style="width:100px">Visits</th>
                <th class="py-3 text-center" style="width:280px">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($patients as $patient)
                <tr class="border-bottom">
                  {{-- Info --}}
                  <td class="py-3 ps-4">
                    <div class="d-flex align-items-center">
                      <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" value="{{ $patient->id }}">
                      </div>
                      <div class="patient-avatar me-3">
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center text-white"
                             style="width:40px; height:40px; font-size:14px;">
                          {{ strtoupper(substr($patient->name,0,2)) }}
                        </div>
                      </div>
                      <div>
                        <h6 class="mb-1">{{ $patient->name }}</h6>
                        <div class="small text-muted">
                          @if($patient->profile->sex)
                            {{ ucfirst($patient->profile->sex) }}
                          @endif
                          @if($patient->profile->birth_date)
                            • {{ \Carbon\Carbon::parse($patient->profile->birth_date)->age }} yrs
                          @endif
                          @if($patient->phone)
                            • {{ $patient->phone }}
                          @endif
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- Visits --}}
                  <td class="py-3 text-center">
                    <span class="badge bg-info">{{ $patient->visits_count }}</span>
                  </td>

                  {{-- Actions --}}
                  <td class="py-3 text-center">
                    <div class="btn-group" role="group">
                      {{-- Queue button --}}
                      <button
                        class="btn btn-sm btn-outline-success openQueueModal"
                        data-patient-id="{{ $patient->id }}"
                        data-bs-toggle="modal"
                        data-bs-target="#queueModal"
                        title="Choose Queue & Print">
                        <i class="fas fa-list-check"></i>
                        <span class="d-none d-md-inline ms-1">Queue</span>
                      </button>
                     <a href="{{ route('patients.show', $patient) }}"
                         class="btn btn-sm btn-outline-info"
                         data-bs-toggle="tooltip" title="View Details">
                        <i class="fas fa-eye"></i>
                        <span class="d-none d-md-inline ms-1">View</span>
                      </a>


                      <a href="{{ route('patients.edit', $patient) }}"
                         class="btn btn-sm btn-outline-warning"
                         data-bs-toggle="tooltip" title="Edit Patient">
                        <i class="fas fa-edit"></i>
                        <span class="d-none d-md-inline ms-1">Edit</span>
                      </a>
                      <button type="button"
                              class="btn btn-sm btn-outline-danger"
                              data-bs-toggle="tooltip" title="Delete Patient"
                              onclick="deletePatient({{ $patient->id }}, '{{ $patient->name }}')">
                        <i class="fas fa-trash"></i>
                        <span class="d-none d-md-inline ms-1">Delete</span>
                      </button>
                    </div>
                      
                    </div>
                    {{-- Hidden delete form --}}
                    <form id="delete-form-{{ $patient->id }}"
                          action="{{ route('patients.destroy',$patient) }}"
                          method="POST" class="d-none">
                      @csrf @method('DELETE')
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        {{-- Empty state --}}
        <div class="text-center py-5">
          <i class="fas fa-user-friends text-muted" style="font-size:4rem"></i>
          <h5 class="mt-3">No Patients Found</h5>
          <p class="text-muted">Adjust your filters or add new patients.</p>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Queue-Picker Modal --}}
<div class="modal fade" id="queueModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
<form
  id="queueForm"
  method="POST"
  action=""   {{-- we’ll overwrite via JS --}}
  class="modal-content"
>
  @csrf
      <div class="modal-header">
        <h5 class="modal-title">Select Department / Queue</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <label for="queueSelect" class="form-label">Queue</label>
        <select id="queueSelect" name="queue_id" class="form-select" required>
          <option value="" disabled selected>— Choose —</option>
          @foreach($queues as $q)
            <option value="{{ $q->id }}">{{ $q->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Issue & Print</button>
      </div>
    </form>
  </div>
</div>



<style>
/* Custom CSS for enhanced design */
.bg-gradient-primary {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card {
  transition: all 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.table-hover tbody tr:hover {
  background-color: rgba(0, 123, 255, 0.05);
}

.btn-group .btn {
  transition: all 0.2s ease-in-out;
}

.btn-group .btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.patient-avatar .rounded-circle {
  transition: all 0.2s ease-in-out;
}

.patient-avatar .rounded-circle:hover {
  transform: scale(1.1);
}

.form-control:focus,
.form-select:focus {
  border-color: #667eea;
  box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.input-group-text {
  transition: all 0.2s ease-in-out;
}

.input-group:focus-within .input-group-text {
  border-color: #667eea;
  background-color: #f8f9ff;
}

@media (max-width: 768px) {
  .btn-group .btn span {
    display: none !important;
  }
  
  .page-header .col-auto {
    margin-top: 1rem;
  }
  
  .page-header .btn-lg {
    width: 100%;
  }
}
</style>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  // 1) Initialize all Bootstrap tooltips
  Array.from(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
       .forEach(el => new bootstrap.Tooltip(el));

  // 2) “Select All” checkbox logic (if you still want it)
  const selectAll = document.getElementById('selectAll');
  const checks    = document.querySelectorAll('tbody input[type="checkbox"]');
  if (selectAll) {
    selectAll.addEventListener('change', () => {
      checks.forEach(cb => cb.checked = selectAll.checked);
    });
    checks.forEach(cb => cb.addEventListener('change', () => {
      const checkedCount = document.querySelectorAll('tbody input[type="checkbox"]:checked').length;
      selectAll.checked = checkedCount === checks.length;
      selectAll.indeterminate = checkedCount > 0 && checkedCount < checks.length;
    }));
  }

  // 3) Single “Queue”-button handler
  document.querySelectorAll('.openQueueModal').forEach(btn => {
    btn.addEventListener('click', () => {
      const patientId = btn.dataset.patientId;
      // point the form at your new patientStore route:
      document
        .getElementById('queueForm')
        .setAttribute('action', `/patients/${patientId}/queue`);
      // clear out any previous selection
      document.getElementById('queueSelect').value = '';
    });
  });

  // 4) Delete-modal logic (if you still need it)
  window.deletePatient = (id, name) => {
    document.getElementById('patientName').textContent = name;
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
    document.getElementById('confirmDelete').onclick = () =>
      document.getElementById(`delete-form-${id}`).submit();
  };

  // 5) Optional auto-hide for success alerts
  setTimeout(() => {
    document.querySelectorAll('.alert-success').forEach(a => {
      a.style.transition = 'opacity .5s';
      a.style.opacity = 0;
      setTimeout(() => a.remove(), 500);
    });
  }, 5000);
});
</script>
@endpush

@endsection
