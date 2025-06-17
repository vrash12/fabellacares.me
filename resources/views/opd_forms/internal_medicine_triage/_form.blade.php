{{-- resources/views/opd_forms/internal_medicine_triage/_form.blade.php --}}

@php
    // helper to pull old input or existing model data
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp

<form method="POST" action="{{ $postRoute ?? route('triage.internal.store') }}">
    @csrf

    <div class="text-center mb-4">
        <h2>🏥 INTERNAL MEDICINE TRIAGE FORM</h2>
    </div>

    {{-- I. Chief Complaint --}}
    <h5>I. Chief Complaint</h5>
    <div class="mb-3">
        <label class="form-label">Chief Complaint</label>
        <input type="text"
               name="chief_complaint"
               value="{{ f('chief_complaint', $triageForm) }}"
               class="form-control">
    </div>

    {{-- II. History of Present Illness --}}
    <h5 class="mt-4">II. History of Present Illness</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Onset</label>
            <input type="text"
                   name="onset"
                   value="{{ f('onset', $triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Duration</label>
            <input type="text"
                   name="duration"
                   value="{{ f('duration', $triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Progression</label>
            <select name="progression" class="form-select">
                <option value="">Select…</option>
                @foreach(['Improving','Worsening','Unchanged'] as $opt)
                    <option value="{{ $opt }}"
                        {{ f('progression', $triageForm)==$opt ? 'selected' : '' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <label class="form-label">Associated symptoms</label>
    <div class="row mb-3">
        @php
            $symptoms = [
              'Fever','Cough','Chest pain','Headache','Numbness',
              'Body weakness','Abdominal pain','Diarrhea','Vomiting','Palpitations'
            ];
            $checked = f('associated_symptoms', $triageForm) ?: [];
        @endphp
        @foreach($symptoms as $sym)
            <div class="col-md-3 form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="associated_symptoms[]"
                       value="{{ $sym }}"
                       {{ in_array($sym,$checked) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $sym }}</label>
            </div>
        @endforeach
        <div class="col-md-6 mt-2">
            <label class="form-label">Others</label>
            <input type="text"
                   name="associated_symptoms_other"
                   value="{{ f('associated_symptoms_other', $triageForm) }}"
                   class="form-control">
        </div>
    </div>

    {{-- III. Past Medical History --}}
    <h5 class="mt-4">III. Past Medical History</h5>
    @php
      $conditions = [
        'Hypertension','Diabetes Mellitus','Asthma / COPD',
        'Heart disease','Tuberculosis','Stroke / Seizures'
      ];
    @endphp
    <div class="row g-3 mb-3">
      @foreach($conditions as $cond)
        <div class="col-md-4">
          <label class="form-label">{{ $cond }}</label>
          <select name="past_history[{{ Str::slug($cond) }}]" class="form-select">
            <option value="">Select…</option>
            <option value="Yes" {{ f("past_history.".Str::slug($cond), $triageForm)=='Yes' ? 'selected':'' }}>Yes</option>
            <option value="No"  {{ f("past_history.".Str::slug($cond), $triageForm)=='No'  ? 'selected':'' }}>No</option>
          </select>
        </div>
      @endforeach
      <div class="col-md-6">
        <label class="form-label">Others</label>
        <input type="text"
               name="past_history[others]"
               value="{{ f('past_history.others', $triageForm) }}"
               class="form-control">
      </div>
    </div>

    {{-- IV. Medications and Allergies --}}
    <h5 class="mt-4">IV. Medications and Allergies</h5>
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Current Medications</label>
        <input type="text"
               name="current_medications"
               value="{{ f('current_medications', $triageForm) }}"
               class="form-control">
      </div>
      <div class="col-md-6">
        <label class="form-label">Allergies</label>
        <input type="text"
               name="allergies"
               value="{{ f('allergies', $triageForm) }}"
               class="form-control">
      </div>
    </div>

    {{-- V. Vital Signs --}}
    <h5 class="mt-4">V. Vital Signs</h5>
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <label class="form-label">BP (mmHg)</label>
        <div class="input-group">
          <input type="number" name="bp_systolic" value="{{ f('bp_systolic', $triageForm) }}" class="form-control" placeholder="Systolic">
          <span class="input-group-text">/</span>
          <input type="number" name="bp_diastolic" value="{{ f('bp_diastolic', $triageForm) }}" class="form-control" placeholder="Diastolic">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label">HR (bpm)</label>
        <input type="number"
               name="heart_rate"
               value="{{ f('heart_rate', $triageForm) }}"
               class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">RR (cpm)</label>
        <input type="number"
               name="resp_rate"
               value="{{ f('resp_rate', $triageForm) }}"
               class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Temp (°C)</label>
        <input type="number" step="0.1"
               name="temperature"
               value="{{ f('temperature', $triageForm) }}"
               class="form-control">
      </div>
      <div class="col-md-1">
        <label class="form-label">Height (cm)</label>
        <input type="number"
               name="height"
               value="{{ f('height', $triageForm) }}"
               class="form-control">
      </div>
      <div class="col-md-1">
        <label class="form-label">Weight (kg)</label>
        <input type="number"
               name="weight"
               value="{{ f('weight', $triageForm) }}"
               class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Blood Sugar (mg/dL)</label>
        <input type="number" step="0.1"
               name="blood_sugar"
               value="{{ f('blood_sugar', $triageForm) }}"
               class="form-control">
      </div>
    </div>

    {{-- Submit button --}}
    <button type="submit" class="btn btn-primary">
      {{ isset($triageForm) ? 'Update Triage' : 'Save Triage' }}
    </button>
</form>
