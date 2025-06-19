{{-- resources/views/opd_forms/follow_up/_form.blade.php --}}

@php
    $form = $opd_form ?? null;

    if (! function_exists('fv')) {
        function fv(string $key, $form) {
            return old($key, data_get($form, "answers.{$key}", ''));
        }
    }

    $rows = fv('followups', $form) ?: [[]];

    $selectedDeptId   = old('department_id', $prefillDept ?? null);
    $selectedDeptName = $departmentName 
                      ?? ($selectedDeptId 
                            ? \App\Models\Queue::find($selectedDeptId)?->name 
                            : ''
                         );

    $prefillPatientId = old('patient_id', $prefillPatient ?? null);
@endphp

{{-- ─── INLINE STYLES ONLY FOR THIS FORM ─── --}}
<style>
    /* ===== Simplified Follow-Up Form Styles ===== */
    :root {
        --primary-green: #00b467;
        --secondary-teal: #0e4749;
        --border-color: #e9ecef;
        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --border-radius: 0.75rem;
        --transition: all 0.3s ease;
    }

    .form-container {
        background: #f8f9fa;
        padding: 2rem;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .section-header {
        background: var(--secondary-teal);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--border-radius);
        margin-bottom: 1.5rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    .section-header i {
        font-size: 1.2rem;
    }

    .metadata-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        padding: 1.5rem;
        margin-bottom: 2rem;
        border-left: 4px solid var(--primary-green);
    }
    .metadata-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }

    .form-group-enhanced {
        margin-bottom: 1.5rem;
    }
    .form-label-enhanced {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .form-label-enhanced i {
        color: var(--primary-green);
    }
    .form-control-enhanced {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid var(--border-color);
        border-radius: 0.5rem;
        transition: var(--transition);
        font-size: 0.95rem;
    }
    .form-control-enhanced:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.2rem rgba(0, 180, 103, 0.25);
        outline: none;
    }
    .form-control-enhanced[readonly] {
        background-color: #f8f9fa;
        border-color: #dee2e6;
    }

    .patient-selection {
        background: white;
        border-radius: var(--border-radius);
        padding: 2.5rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 2.5rem;
        border: 1px solid var(--border-color);
        min-height: 200px;
    }
    .patient-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .followup-section {
        background: white;
        border-radius: var(--border-radius);
        padding: 2rem;
        box-shadow: var(--shadow-sm);
        margin-bottom: 2rem;
    }
    .followup-table-container {
        border-radius: var(--border-radius);
        overflow: hidden;
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }
    .table-enhanced {
        margin-bottom: 0;
        font-size: 0.9rem;
    }
    .table-enhanced thead {
        background: var(--secondary-teal);
        color: white;
    }
    .table-enhanced thead th {
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.8rem;
        padding: 1rem 0.75rem;
        border: none;
    }
    .table-enhanced tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.01);
        box-shadow: var(--shadow-sm);
    }
    .table-enhanced tbody td {
        padding: 1rem 0.75rem;
        vertical-align: middle;
        border-color: var(--border-color);
    }
    .table-input {
        width: 100%;
        padding: 0.5rem 0.75rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        transition: var(--transition);
    }
    .table-input:focus {
        border-color: var(--primary-green);
        box-shadow: 0 0 0 0.1rem rgba(0, 180, 103, 0.25);
        outline: none;
    }

    .btn-remove {
        background: #dc3545;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        border: none;
        font-size: 1.2rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .btn-remove:hover {
        background: #c82333;
    }

    .btn-add-row {
        background: var(--primary-green);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 2rem;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
    }
    .btn-add-row:hover {
        background: #008a52;
    }

    .stats-row {
        background: #e8f5e8;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: 600;
        color: #155724;
    }
    .stats-row i {
        margin-right: 0.5rem;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }
    .error-message i {
        font-size: 0.8rem;
    }

    @media (max-width: 768px) {
        .metadata-grid,
        .patient-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="form-container">
    {{-- ─── Form Header ─── --}}
    <div class="section-header">
        <i class="fas fa-file-medical-alt"></i>
        OPD Follow-Up Form
    </div>

    {{-- ─── Form Metadata ─── --}}
    <div class="metadata-card">
        <div class="metadata-grid">
            <div class="form-group-enhanced">
                <label class="form-label-enhanced">
                    <i class="fas fa-tag"></i>
                    Form Name
                </label>
                <input type="text" class="form-control-enhanced" value="OPD-OB FORM" readonly>
            </div>

            <div class="form-group-enhanced">
                <label class="form-label-enhanced">
                    <i class="fas fa-hashtag"></i>
                    Form Number
                </label>
                <input type="text" class="form-control-enhanced" value="OPD-F-08" readonly>
            </div>

        </div>
    </div>

    {{-- ─── Patient Selection ─── --}}
    <div class="patient-selection">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <i class="fas fa-user-plus"></i>
            Patient Information
        </div>

        {{-- 1) Visible input + datalist --}}
        <div class="form-group-enhanced">
            <label class="form-label-enhanced">
                <i class="fas fa-search"></i>
                Select Patient
            </label>
            <input
                type="text"
                id="patient_name_input"
                class="form-control-enhanced @error('patient_id') is-invalid @enderror"
                placeholder="Type patient’s name…"
                list="patient_list"
                autocomplete="off"
                value="{{ old('patient_name') }}"
            >
            @error('patient_id')
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ $message }}
                </div>
            @enderror

            {{-- Hidden field to store selected patient’s ID --}}
            <input type="hidden" name="patient_id" id="patient_id" value="{{ $prefillPatientId }}">
        </div>

       <div class="form-group-enhanced">
  <label class="form-label-enhanced">
    <i class="fas fa-building"></i>
    Department
  </label>
  <select name="department_id" class="form-control-enhanced">
    <option value="">— Select department —</option>
    @foreach($queues as $q)
      <option value="{{ $q->id }}"
         {{ (old('department_id',$selectedDeptId)==$q->id) ? 'selected':'' }}>
        {{ $q->name }}
      </option>
    @endforeach
  </select>
  @error('department_id')
    <div class="error-message"><i class="fas fa-exclamation-triangle"></i>{{ $message }}</div>
  @enderror
</div>

        

        {{-- 2) The <datalist> with each patient’s “name (id)” --}}
        <datalist id="patient_list">
            @foreach($patients as $p)
                <option
                    value="{{ $p->name }} ({{ $p->id }})"
                    data-pid="{{ $p->id }}"
                    data-name="{{ $p->name }}"
                ></option>
            @endforeach
        </datalist>

        {{-- 3) A single read‐only “Patient Name” field that gets filled --}}
        <div class="patient-info-grid">
            <div class="form-group-enhanced">
                <label class="form-label-enhanced">
                    <i class="fas fa-user"></i>
                    Patient Name
                </label>
                <input
                    type="text"
                    id="patient_name_display"
                    class="form-control-enhanced"
                    readonly
                    value="{{ fv('patient_name', $form) }}"
                >
            </div>
        </div>
    </div>

    {{-- ─── Follow-Up Records ─── --}}
    <div class="followup-section">
        <div class="section-header" style="margin-bottom: 1.5rem;">
            <i class="fas fa-clipboard-list"></i>
            Follow-Up Records
        </div>

        {{-- Stats Row --}}
        <div class="stats-row">
            <span>
                <i class="fas fa-chart-line"></i>
                Total Follow-Up Records
            </span>
            <span id="record-count">{{ count($rows) }}</span>
        </div>

        <div id="followup-table">
            <div class="followup-table-container">
                <table class="table table-enhanced">
                    <thead>
                        <tr>
                            <th style="width:130px">
                                <i class="fas fa-calendar me-2"></i>
                                Date
                            </th>
                            <th style="width:130px">
                                <i class="fas fa-baby me-2"></i>
                                Gest. Weeks
                            </th>
                            <th style="width:130px">
                                <i class="fas fa-weight me-2"></i>
                                Weight (kg)
                            </th>
                            <th style="width:130px">
                                <i class="fas fa-heartbeat me-2"></i>
                                Blood Pressure
                            </th>
                            <th style="width:150px">
                                <i class="fas fa-building me-2"></i>
                                Department
                            </th>
                            <th>
                                <i class="fas fa-notes-medical me-2"></i>
                                Remarks
                            </th>
                            <th style="width:60px">
                                <i class="fas fa-cog"></i>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="fu-tbody">
                        @foreach($rows as $i => $row)
                            <tr class="fu-row">
                                <td>
                                    <input
                                        type="date"
                                        name="followups[{{ $i }}][date]"
                                        class="table-input"
                                        value="{{ $row['date'] ?? '' }}"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        name="followups[{{ $i }}][gest_weeks]"
                                        class="table-input"
                                        placeholder="0"
                                        value="{{ $row['gest_weeks'] ?? '' }}"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="number"
                                        step="0.01"
                                        name="followups[{{ $i }}][weight]"
                                        class="table-input"
                                        placeholder="0.00"
                                        value="{{ $row['weight'] ?? '' }}"
                                    >
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        name="followups[{{ $i }}][bp]"
                                        class="table-input"
                                        placeholder="120/80"
                                        value="{{ $row['bp'] ?? '' }}"
                                    >
                                </td>
                            
                                <td>
                                    <input
                                        type="text"
                                        name="followups[{{ $i }}][remarks]"
                                        class="table-input"
                                        placeholder="Enter remarks…"
                                        value="{{ $row['remarks'] ?? '' }}"
                                    >
                                </td>
                                <td class="text-center">
                                    <button
                                        type="button"
                                        class="btn-remove remove-row"
                                        title="Remove this record"
                                    >&times;</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="add-row-container">
                <button id="add-followup" type="button" class="btn-add-row">
                    <i class="fas fa-plus"></i>
                    Add Follow-Up Record
                </button>
            </div>
        </div>
    </div>
</div>

{{-- ─── INLINE SCRIPT ONLY FOR THIS FORM ─── --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1) Build a lookup table from the datalist <option> elements
    const options = document.querySelectorAll('#patient_list option');
    const lookup  = {};
    options.forEach(opt => {
        lookup[opt.value] = {
            id:   opt.dataset.pid,
            name: opt.dataset.name
        };
    });

    // 2) When user picks something from the datalist, auto-fill the hidden ID + display name
    const nameInput        = document.getElementById('patient_name_input');
    const hiddenIdInput    = document.getElementById('patient_id');
    const displayNameField = document.getElementById('patient_name_display');

    nameInput.addEventListener('input', function(e) {
        const chosen = e.target.value;
        const data   = lookup[chosen] || null;

        if (data) {
            hiddenIdInput.value      = data.id;
            displayNameField.value   = data.name;
        } else {
            // If text does not exactly match one option, clear everything
            hiddenIdInput.value      = '';
            displayNameField.value   = '';
        }
    });

    // 3) If the controller prefilled a patient_id, set that on page load
    const prefillId = "{{ $prefillPatientId }}";
    if (prefillId) {
        let chosenText = null;
        options.forEach(opt => {
            if (opt.dataset.pid === prefillId) {
                chosenText = opt.value;
            }
        });
        if (chosenText) {
            nameInput.value = chosenText;
            nameInput.dispatchEvent(new Event('input'));
        }
    }

    // 4) Build a JS string of <option> tags for “Department”
    const departmentOptions = `
        @foreach($queues as $q)
            <option value="{{ $q->id }}">{{ addslashes($q->name) }}</option>
        @endforeach
    `;

    // 5) “Add Row” / “Remove Row” logic
    let idx = {{ count($rows) }};
    document.getElementById('add-followup').addEventListener('click', function() {
        const tbody = document.getElementById('fu-tbody');
        const row   = document.createElement('tr');
        row.innerHTML = `
          <td><input type="date"   name="followups[${idx}][date]"       class="table-input"></td>
          <td><input type="number" name="followups[${idx}][gest_weeks]" class="table-input" placeholder="0"></td>
          <td><input type="number" step="0.01" name="followups[${idx}][weight]" class="table-input" placeholder="0.00"></td>
          <td><input type="text"   name="followups[${idx}][bp]"         class="table-input" placeholder="120/80"></td>
          <td>
            <select
                name="followups[${idx}][department_id]"
                class="form-control-enhanced table-input"
            >
                ${departmentOptions}
            </select>
          </td>
          <td><input type="text"   name="followups[${idx}][remarks]"    class="table-input" placeholder="Enter remarks…"></td>
          <td class="text-center">
            <button type="button" class="btn-remove remove-row">&times;</button>
          </td>`;
        tbody.appendChild(row);
        idx++;
        document.getElementById('record-count').innerText = tbody.querySelectorAll('tr').length;
    });

    document.getElementById('fu-tbody').addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            document.getElementById('record-count').innerText =
              document.getElementById('fu-tbody').querySelectorAll('tr').length;
        }
    });
});
</script>
