{{-- resources/views/patients/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4">
  {{-- Enhanced Page Header --}}
  <div class="page-header bg-gradient-primary rounded-3 p-4 mb-4 text-white shadow-sm">
    <div class="row align-items-center">
      <div class="col">
        <h1 class="h2 mb-1 fw-bold">
          <i class="fas fa-user-md me-2"></i>
          OB OPD Patients
        </h1>
        <p class="mb-0 opacity-90">Manage obstetrics and gynecology outpatient records</p>
      </div>
      <div class="col-auto">
        {{-- Place for any additional header controls if needed --}}
      </div>
    </div>
  </div>

  {{-- Enhanced Search & Filter Card --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-bottom-0 py-3">
      <h5 class="card-title mb-0">
        <i class="fas fa-search me-2 text-primary"></i>
        Search & Filter Patients
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
            <input
              type="text"
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
            <option value="male" @selected(request('sex') == 'male')>
              <i class="fas fa-mars"></i> Male
            </option>
            <option value="female" @selected(request('sex') == 'female')>
              <i class="fas fa-venus"></i> Female
            </option>
          </select>
        </div>
        <div class="col-lg-4 col-md-2">
          <label class="form-label small text-muted fw-medium">&nbsp;</label>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill">
              <i class="fas fa-filter me-1"></i>
              Apply Filters
            </button>
            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
              <i class="fas fa-undo me-1"></i>
              Reset
            </a>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- Enhanced Results Card --}}
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-bottom py-3">
      <div class="row align-items-center">
        <div class="col">
          <h5 class="card-title mb-0">
            <i class="fas fa-list me-2 text-success"></i>
            Patient Records
            <span class="badge bg-primary ms-2">
              {{ count($patients) }}
            </span>
          </h5>
        </div>
        <div class="col-auto">
          <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-download me-1"></i>
              Export
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm">
              <i class="fas fa-print me-1"></i>
              Print
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      @if($patients->isNotEmpty())
        <div class="table-responsive">
          <table class="table table-hover mb-0" id="patients-table">
            <thead class="table-light">
              <tr>
                {{-- 1) Checkbox + Patient Info --}}
                <th class="border-0 py-3 ps-4">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label fw-medium text-muted" for="selectAll">
                      Patient Information
                    </label>
                  </div>
                </th>

                {{-- 2) Visits Column --}}
                <th class="border-0 py-3 text-center" style="width: 100px;">
                  <span class="fw-medium text-muted">Visits</span>
                </th>

                {{-- 3) Actions --}}
                <th class="border-0 py-3 text-center" style="width: 280px;">
                  <span class="fw-medium text-muted">Actions</span>
                </th>
              </tr>
            </thead>
            <tbody>
              @foreach($patients as $patient)
                <tr class="border-bottom">
                  {{-- Patient Info --}}
                  <td class="py-3 ps-4">
                    <div class="d-flex align-items-center">
                      <div class="form-check me-3">
                        <input class="form-check-input" type="checkbox" value="{{ $patient->id }}">
                      </div>
                      <div class="patient-avatar me-3">
                        <div class="rounded-circle bg-primary bg-gradient d-flex align-items-center justify-content-center text-white fw-bold"
                             style="width: 40px; height: 40px; font-size: 14px;">
                          {{ strtoupper(substr($patient->name, 0, 2)) }}
                        </div>
                      </div>
                      <div>
                        <h6 class="mb-1 fw-medium text-dark">{{ $patient->name }}</h6>
                        <div class="small text-muted">
                          @if($patient->profile->sex)
                            <i class="fas fa-{{ $patient->profile->sex == 'male' ? 'mars text-info' : 'venus text-danger' }} me-1"></i>
                            {{ ucfirst($patient->profile->sex) }}
                          @endif
                          @if($patient->profile->birth_date)
                            <span class="mx-2">•</span>
                            <i class="fas fa-birthday-cake me-1"></i>
                            {{ \Carbon\Carbon::parse($patient->profile->birth_date)->age }} years
                          @endif
                          @if($patient->phone)
                            <span class="mx-2">•</span>
                            <i class="fas fa-phone me-1"></i>
                            {{ $patient->phone }}
                          @endif
                        </div>
                      </div>
                    </div>
                  </td>

                  {{-- Visits Count --}}
                  <td class="py-3 text-center align-middle">
                    <span class="badge bg-info text-dark">
                      {{ $patient->visits_count }}
                    </span>
                  </td>

                  {{-- Actions --}}
                  <td class="py-3 text-center">
                    <div class="btn-group" role="group">
                      <a href="{{ route('patients.show', $patient) }}"
                         class="btn btn-sm btn-outline-info"
                         data-bs-toggle="tooltip" title="View Details">
                        <i class="fas fa-eye"></i>
                        <span class="d-none d-md-inline ms-1">View</span>
                      </a>

                      {{-- Link to patient→visits page --}}
                      <a href="{{ route('patients.visits.index', $patient) }}"
                         class="btn btn-sm btn-outline-primary"
                         data-bs-toggle="tooltip" title="View Visits">
                        <i class="fas fa-notes-medical"></i>
                        <span class="d-none d-md-inline ms-1">Visits</span>
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

                    {{-- Hidden delete form --}}
                    <form id="delete-form-{{ $patient->id }}"
                          action="{{ route('patients.destroy', $patient) }}"
                          method="POST"
                          class="d-none">
                      @csrf
                      @method('DELETE')
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>

        {{-- No pagination here since we used ->get() in the controller --}}
      @else
        {{-- Empty State --}}
        <div class="text-center py-5">
          <div class="mb-4">
            <i class="fas fa-user-friends text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
          </div>
          <h5 class="text-muted mb-2">No Patients Found</h5>
          <p class="text-muted mb-4">
            @if(request()->hasAny(['search', 'sex']))
              No patients match your current search criteria.
            @else
              You haven't added any patients yet.
            @endif
          </p>
          @if(request()->hasAny(['search', 'sex']))
            <a href="{{ route('patients.index') }}" class="btn btn-outline-primary me-2">
              <i class="fas fa-undo me-1"></i>
              Clear Filters
            </a>
          @endif
          <a href="{{ route('patients.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i>
            Add First Patient
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

{{-- Enhanced Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header border-bottom-0 bg-danger bg-gradient text-white">
        <h5 class="modal-title">
          <i class="fas fa-exclamation-triangle me-2"></i>
          Confirm Deletion
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <div class="mb-3">
          <i class="fas fa-user-times text-danger" style="font-size: 3rem; opacity: 0.7;"></i>
        </div>
        <h5 class="mb-2">Delete Patient Record?</h5>
        <p class="text-muted mb-0">
          Are you sure you want to delete <strong id="patientName"></strong>?
          This action cannot be undone.
        </p>
      </div>
      <div class="modal-footer border-top-0 justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
          <i class="fas fa-times me-1"></i>
          Cancel
        </button>
        <button type="button" class="btn btn-danger" id="confirmDelete">
          <i class="fas fa-trash me-1"></i>
          Delete Patient
        </button>
      </div>
    </div>
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

<script>
// Enhanced JavaScript functionality
document.addEventListener('DOMContentLoaded', function() {
  // Initialize tooltips
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.map(function (el) {
    return new bootstrap.Tooltip(el);
  });

  // “Select All” checkbox logic
  const selectAllCheckbox = document.getElementById('selectAll');
  const individualCheckboxes = document.querySelectorAll('tbody input[type="checkbox"]');
  
  if (selectAllCheckbox) {
    selectAllCheckbox.addEventListener('change', function() {
      individualCheckboxes.forEach(cb => cb.checked = this.checked);
    });
  }

  individualCheckboxes.forEach(cb => {
    cb.addEventListener('change', function() {
      const checkedBoxes = document.querySelectorAll('tbody input[type="checkbox"]:checked');
      if (selectAllCheckbox) {
        selectAllCheckbox.checked = checkedBoxes.length === individualCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < individualCheckboxes.length;
      }
    });
  });
});

// Enhanced delete function with modal
function deletePatient(patientId, patientName) {
  document.getElementById('patientName').textContent = patientName;
  const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
  deleteModal.show();
  
  document.getElementById('confirmDelete').onclick = function() {
    document.getElementById('delete-form-' + patientId).submit();
  };
}

// Auto-hide success alerts after 5 seconds
setTimeout(function() {
  const alerts = document.querySelectorAll('.alert');
  alerts.forEach(alert => {
    if (alert.classList.contains('alert-success')) {
      alert.style.transition = 'opacity 0.5s ease-in-out';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }
  });
}, 5000);
</script>
@endsection
