{{-- resources/views/schedules/_form.blade.php --}}
@php
  // If this partial is included by “edit”, then $schedule exists; otherwise $schedule is null.
  $sched = $schedule ?? null;
@endphp

<style>
  :root {
    --primary-blue: #2563eb;
    --primary-dark: #1e40af;
    --secondary-gray: #64748b;
    --light-blue: #eff6ff;
    --accent-blue: #dbeafe;
    --success-green: #059669;
    --warning-orange: #d97706;
    --danger-red: #dc2626;
    --light-gray: #f8fafc;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --hover-shadow: 0 10px 25px rgba(37, 99, 235, 0.15);
    --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  }

  /* ===== Enhanced Header ===== */
  .page-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 2.5rem 2rem;
    border-radius: 1rem;
    margin-bottom: 2rem;
    box-shadow: var(--card-shadow);
    position: relative;
    overflow: hidden;
  }
  
  .page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: float 6s ease-in-out infinite;
  }
  
  .page-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -10%;
    width: 150px;
    height: 150px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 50%;
    animation: float 8s ease-in-out infinite reverse;
  }
  
  @keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(10deg); }
  }
  
  .header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    z-index: 2;
  }
  
  .header-title {
    display: flex;
    align-items: center;
    gap: 1rem;
  }
  
  .header-title h2 {
    font-size: 2.5rem;
    font-weight: 800;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
  
  .header-icon {
    width: 3rem;
    height: 3rem;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    backdrop-filter: blur(10px);
  }
  
  .add-schedule-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    color: white;
    padding: 0.875rem 2rem;
    border-radius: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .add-schedule-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
  }

  /* ===== Enhanced Alert ===== */
  .alert-enhanced {
    border: none;
    border-radius: 0.75rem;
    padding: 1rem 1.5rem;
    box-shadow: var(--card-shadow);
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
  }
  
  .alert-enhanced.alert-success {
    background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
    color: #15803d;
    border-left: 4px solid var(--success-green);
  }
  
  .alert-enhanced .btn-close {
    background: none;
    opacity: 0.7;
  }

  /* ===== Enhanced Table Container ===== */
  .table-container {
    background: white;
    border-radius: 1rem;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 2rem;
  }
  
  .table-header {
    background: linear-gradient(135deg, var(--light-blue) 0%, var(--accent-blue) 100%);
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--border-color);
  }
  
  .table-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }

  /* ===== Enhanced Table ===== */
  .enhanced-table {
    margin: 0;
    font-size: 0.95rem;
  }
  
  .enhanced-table thead th {
    background: var(--light-gray);
    color: var(--text-primary);
    font-weight: 700;
    padding: 1.25rem 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-size: 0.85rem;
    border: none;
    position: relative;
  }
  
  .enhanced-table thead th::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark));
  }
  
  .enhanced-table tbody td {
    padding: 1.25rem 1.5rem;
    border-top: 1px solid var(--border-color);
    vertical-align: middle;
    transition: var(--transition);
  }
  
  .enhanced-table tbody tr {
    transition: var(--transition);
  }
  
  .enhanced-table tbody tr:hover {
    background: var(--light-blue);
    transform: scale(1.005);
  }

  /* ===== Staff Badge ===== */
  .staff-info {
    display: flex;
    align-items: center;
    gap: 0.75rem;
  }
  
  .staff-avatar {
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 0.9rem;
  }
  
  .staff-details h6 {
    margin: 0;
    font-weight: 600;
    color: var(--text-primary);
  }
  
  .staff-details small {
    color: var(--text-secondary);
    font-size: 0.8rem;
  }

  /* ===== Role Badge ===== */
  .role-badge {
    display: inline-block;
    padding: 0.375rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  
  .role-doctor {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
  }
  
  .role-nurse {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    color: #1e40af;
  }
  
  .role-admin {
    background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
    color: #7c3aed;
  }
  
  .role-default {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    color: var(--text-secondary);
  }

  /* ===== Date Badge ===== */
  .date-badge {
    background: linear-gradient(135deg, var(--light-blue), var(--accent-blue));
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    color: var(--primary-dark);
    display: inline-block;
  }

  /* ===== Time Display ===== */
  .time-display {
    background: var(--light-gray);
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-weight: 600;
    color: var(--text-primary);
    border-left: 3px solid var(--success-green);
  }
  
  .no-shift {
    color: var(--text-secondary);
    font-style: italic;
    padding: 0.5rem 1rem;
    background: var(--light-gray);
    border-radius: 0.5rem;
    border-left: 3px solid var(--warning-orange);
  }

  /* ===== Action Buttons ===== */
  .action-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .action-btn {
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: var(--transition);
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
  }
  
  .btn-view {
    background: linear-gradient(135deg, var(--light-blue), var(--accent-blue));
    color: var(--primary-blue);
  }
  
  .btn-view:hover {
    background: linear-gradient(135deg, var(--accent-blue), #bfdbfe);
    color: var(--primary-dark);
    transform: translateY(-1px);
  }
  
  .btn-edit {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: var(--warning-orange);
  }
  
  .btn-edit:hover {
    background: linear-gradient(135deg, #fde68a, #fcd34d);
    color: #92400e;
    transform: translateY(-1px);
  }
  
  .btn-delete {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: var(--danger-red);
  }
  
  .btn-delete:hover {
    background: linear-gradient(135deg, #fecaca, #fca5a5);
    color: #991b1b;
    transform: translateY(-1px);
  }

  /* ===== Enhanced Pagination ===== */
  .pagination-container {
    background: white;
    border-radius: 1rem;
    box-shadow: var(--card-shadow);
    padding: 2rem;
    margin-top: 2rem;
  }
  
  .pagination-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 1.5rem;
  }
  
  .enhanced-pagination {
    display: flex;
    gap: 0.5rem;
    align-items: center;
  }
  
  .enhanced-pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 2.5rem;
    height: 2.5rem;
    border: 2px solid var(--border-color);
    color: var(--text-primary);
    background: white;
    border-radius: 0.5rem;
    text-decoration: none;
    font-weight: 600;
    transition: var(--transition);
    font-size: 0.9rem;
  }
  
  .enhanced-pagination .page-link:hover {
    background: var(--light-blue);
    border-color: var(--accent-blue);
    color: var(--primary-blue);
    transform: translateY(-1px);
  }
  
  .enhanced-pagination .page-item.active .page-link {
    background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark));
    border-color: var(--primary-blue);
    color: white;
    box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
  }
  
  .enhanced-pagination .page-item.disabled .page-link {
    color: var(--text-secondary);
    background: var(--light-gray);
    border-color: var(--border-color);
    cursor: not-allowed;
    opacity: 0.6;
  }
  
  .enhanced-pagination .page-item.disabled .page-link:hover {
    background: var(--light-gray);
    border-color: var(--border-color);
    transform: none;
  }

  /* ===== Pagination Info ===== */
  .pagination-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border-color);
    font-size: 0.9rem;
    color: var(--text-secondary);
  }
  
  .pagination-info .info-badge {
    background: var(--light-blue);
    color: var(--primary-blue);
    padding: 0.375rem 0.75rem;
    border-radius: 1rem;
    font-weight: 600;
  }

  /* ===== Enhanced Modal ===== */
  .modal-content {
    border: none;
    border-radius: 1rem;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  }
  
  .modal-header {
    background: linear-gradient(135deg, var(--light-blue), var(--accent-blue));
    border-bottom: 1px solid var(--border-color);
    border-radius: 1rem 1rem 0 0;
    padding: 1.5rem 2rem;
  }
  
  .modal-title {
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }
  
  .modal-body {
    padding: 2rem;
  }

  /* ===== Loading Spinner ===== */
  .loading-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 3rem;
  }
  
  .custom-spinner {
    width: 3rem;
    height: 3rem;
    border: 3px solid var(--border-color);
    border-top: 3px solid var(--primary-blue);
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  /* ===== Responsive Design ===== */
  @media (max-width: 768px) {
    .header-content {
      flex-direction: column;
      gap: 1.5rem;
      text-align: center;
    }
    
    .header-title h2 {
      font-size: 2rem;
    }
    
    .action-group {
      flex-direction: column;
      gap: 0.25rem;
    }
    
    .action-btn {
      width: 100%;
      justify-content: center;
    }
    
    .enhanced-table {
      font-size: 0.85rem;
    }
    
    .enhanced-table thead th,
    .enhanced-table tbody td {
      padding: 0.75rem 1rem;
    }
    
    .staff-info {
      flex-direction: column;
      text-align: center;
    }
  }

  /* ===== Animation Enhancements ===== */
  .table-container,
  .pagination-container {
    animation: slideUp 0.6s ease-out;
  }
  
  @keyframes slideUp {
    from {
      opacity: 0;
      transform: translateY(30px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }
  
  .enhanced-table tbody tr {
    animation: fadeIn 0.4s ease-out;
    animation-fill-mode: both;
  }
  
  .enhanced-table tbody tr:nth-child(1) { animation-delay: 0.1s; }
  .enhanced-table tbody tr:nth-child(2) { animation-delay: 0.2s; }
  .enhanced-table tbody tr:nth-child(3) { animation-delay: 0.3s; }
  .enhanced-table tbody tr:nth-child(4) { animation-delay: 0.4s; }
  .enhanced-table tbody tr:nth-child(5) { animation-delay: 0.5s; }
  
  @keyframes fadeIn {
    from {
      opacity: 0;
      transform: translateX(-20px);
    }
    to {
      opacity: 1;
      transform: translateX(0);
    }
  }
</style>

<div class="row g-3">
  {{-- Staff Name --}}
  <div class="col-md-6">
    <label for="staff_name" class="form-label">Staff Name</label>
    <input
      type="text"
      name="staff_name"
      id="staff_name"
      class="form-control @error('staff_name') is-invalid @enderror"
      value="{{ old('staff_name', $sched->staff_name ?? '') }}"
      required
    >
    @error('staff_name')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Role --}}
  <div class="col-md-6">
    <label for="role" class="form-label">Role</label>
    <select
      name="role"
      id="role"
      class="form-select @error('role') is-invalid @enderror"
      required
    >
      <option value="">Select role…</option>
      @foreach(['doctor','nurse','admin','other'] as $r)
        <option
          value="{{ $r }}"
          @selected(old('role', $sched->role ?? '') == $r)
        >
          {{ ucfirst($r) }}
        </option>
      @endforeach
    </select>
    @error('role')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Date --}}
  <div class="col-md-4">
    <label for="date" class="form-label">Date</label>
    <input
      type="date"
      name="date"
      id="date"
      class="form-control @error('date') is-invalid @enderror"
      {{-- Use optional() so format() is never called on null --}}
      value="{{ old('date', optional($sched->date)->format('Y-m-d')) }}"
      required
    >
    @error('date')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Department --}}
  <div class="col-md-4">
    <label for="department" class="form-label">Department</label>
    <input
      type="text"
      name="department"
      id="department"
      class="form-control @error('department') is-invalid @enderror"
      value="{{ old('department', $sched->department ?? '') }}"
      required
    >
    @error('department')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Start Day --}}
  <div class="col-md-4">
    <label for="start_day" class="form-label">Start Day of Week</label>
    <select
      name="start_day"
      id="start_day"
      class="form-select @error('start_day') is-invalid @enderror"
      required
    >
      <option value="">Choose…</option>
      @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
        <option
          value="{{ $day }}"
          @selected(old('start_day', $sched->start_day ?? '') == $day)
        >
          {{ $day }}
        </option>
      @endforeach
    </select>
    @error('start_day')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Shift Length --}}
  <div class="col-md-4">
    <label for="shift_length" class="form-label">Shift Length (hours)</label>
    <input
      type="number"
      name="shift_length"
      id="shift_length"
      class="form-control @error('shift_length') is-invalid @enderror"
      value="{{ old('shift_length', $sched->shift_length ?? '') }}"
      min="1"
      required
    >
    @error('shift_length')
      <div class="invalid-feedback">{{ $message }}</div>
    @enderror
  </div>

  {{-- Per-Day Shifts --}}
  <div class="col-12">
    <label class="form-label fw-bold">Daily Shifts (check day, then set start/end time)</label>
    <div class="row gy-3">
      @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day)
        @php
          $lower = strtolower($day);
          // If old('include.Sunday') exists, use it; otherwise use $sched->include_sunday (boolean)
          $inc   = old("include.$day", $sched->{"include_$lower"} ?? false);
          // For start time: use old or the model’s value (e.g. $sched->shift_start_sunday)
          $start = old("shift_start.$day", isset($sched) ? $sched->{"shift_start_$lower"} : '');
          // For end time: same logic
          $end   = old("shift_end.$day",   isset($sched) ? $sched->{"shift_end_$lower"}   : '');
        @endphp

        {{-- Wrap each day’s checkbox + time inputs in a container with a “data-day” attribute --}}
        <div class="col-md-4 day-group" data-day="{{ $day }}">
          <div class="form-check">
            <input 
              class="form-check-input day-checkbox"
              type="checkbox"
              name="include[{{ $day }}]"
              id="include_{{ $lower }}"
              value="1"
              @checked($inc)
            >
            <label class="form-check-label" for="include_{{ $lower }}">
              {{ $day }}
            </label>
          </div>

          <div class="row mt-1 gx-2">
            <div class="col">
              <input
                type="time"
                name="shift_start[{{ $day }}]"
                class="form-control form-control-sm @error("shift_start.$day") is-invalid @enderror day-time-input"
                value="{{ $start }}"
                {{-- disabled if $inc is false; JS will toggle on page load and on change --}}
                {{ $inc ? '' : 'disabled' }}
              >
              @error("shift_start.$day")
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col">
              <input
                type="time"
                name="shift_end[{{ $day }}]"
                class="form-control form-control-sm @error("shift_end.$day") is-invalid @enderror day-time-input"
                value="{{ $end }}"
                {{ $inc ? '' : 'disabled' }}
              >
              @error("shift_end.$day")
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</div>

{{-- ===== JavaScript to toggle each day’s time inputs ===== --}}
@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // 1) Whenever any checkbox with class "day-checkbox" is toggled, enable/disable its two time inputs:
      document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
          // Find the parent .day-group container
          const dayGroup = this.closest('.day-group');
          if (!dayGroup) return;
          
          // Enable or disable any <input type="time"> inside that container
          dayGroup.querySelectorAll('.day-time-input').forEach(function(timeInput) {
            timeInput.disabled = !checkbox.checked;
          });
        });
      });

      // 2) On initial page load, trigger “change” on each checkbox so that existing old()/model values are reflected
      document.querySelectorAll('.day-checkbox').forEach(function(checkbox) {
        // If it’s already checked in old data or model, ensure the inputs are not disabled; otherwise disable them
        checkbox.dispatchEvent(new Event('change'));
      });
    });
  </script>
@endpush
