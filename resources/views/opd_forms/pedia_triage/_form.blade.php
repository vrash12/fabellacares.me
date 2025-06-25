{{-- resources/views/opd_forms/pedia_triage/_form.blade.php --}}

@php
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp

<form method="POST" action="{{ $postRoute ?? route('triage.pedia.store') }}">
 @csrf
  @isset($pediaForm)
    @method('PUT')
  @endisset
   <div class="section-card">
    <h5 class="section-title">
      <span class="section-number">I</span>
      Patient
    </h5>
    <div class="mb-3">
      <label class="form-label">Select Patient</label>
      <select name="patient_id" class="form-select" required>
        <option value="">– Choose a patient –</option>
        @foreach($patients as $p)
          <option value="{{ $p->id }}"
            {{ f('patient_id', $pediaForm)==$p->id ? 'selected' : '' }}>
            {{ $p->name }}
          </option>
        @endforeach
      </select>
      @error('patient_id')
        <div class="text-danger small">{{ $message }}</div>
      @enderror
    </div>
  </div>
  <div class="text-center mb-4">
    <h2>🧸 PEDIA FORM</h2>
  </div>

  {{-- I. Chief Complaint --}}
  <h5>I. Chief Complaint</h5>
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <label class="form-label">Main Concern</label>
      <input type="text"
             name="main_concern"
             value="{{ f('main_concern',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Date Started</label>
      <input type="date"
             name="date_started"
             value="{{ f('date_started',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">Progression</label>
      <select name="progression" class="form-select">
        <option value="">Select…</option>
        @foreach(['Improving','Worsening','Same'] as $opt)
          <option value="{{ $opt }}"
            {{ f('progression',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="mb-4">
    <label class="form-label d-block">Associated Symptoms</label>
    @foreach([
        'Fever','Cough','Colds','Vomiting','Diarrhea',
        'Rash','Seizures','Ear Pain','Eye Discharge','Breathing Difficulty'
      ] as $sym)
      <div class="form-check form-check-inline">
        <input class="form-check-input" type="checkbox"
               name="assoc_symptoms[]" value="{{ $sym }}"
               {{ in_array($sym, f('assoc_symptoms',$pediaForm)?:[]) ? 'checked':'' }}>
        <label class="form-check-label">{{ $sym }}</label>
      </div>
    @endforeach
    <input type="text"
           name="assoc_symptoms_other"
           value="{{ f('assoc_symptoms_other',$pediaForm) }}"
           class="form-control mt-2"
           placeholder="Others">
  </div>

  {{-- II. Birth and Neonatal History --}}
  <h5 class="mt-4">II. Birth & Neonatal History</h5>
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <label class="form-label">Type of Delivery</label>
      <select name="delivery_type" class="form-select">
        <option value="">Select…</option>
        @foreach(['Normal Spontaneous','CS','Instrumental'] as $opt)
          <option value="{{ $opt }}"
            {{ f('delivery_type',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Place of Delivery</label>
      <input type="text"
             name="delivery_place"
             value="{{ f('delivery_place',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">Birth Weight (kg)</label>
      <input type="number" step="0.01"
             name="birth_weight"
             value="{{ f('birth_weight',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">Term</label>
      <select name="term" class="form-select">
        <option value="">Select…</option>
        @foreach(['Full Term','Preterm'] as $opt)
          <option value="{{ $opt }}"
            {{ f('term',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">NICU Admission</label>
      <select name="nicu_admission" class="form-select">
        <option value="">Select…</option>
        @foreach(['Yes','No'] as $opt)
          <option value="{{ $opt }}"
            {{ f('nicu_admission',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-8">
      <label class="form-label">If Yes, Reason</label>
      <input type="text"
             name="nicu_reason"
             value="{{ f('nicu_reason',$pediaForm) }}"
             class="form-control">
    </div>
  </div>

  {{-- III. Immunization History --}}
  <h5 class="mt-4">III. Immunization History</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="immunization_status" class="form-select">
        <option value="">Select…</option>
        @foreach(['Fully Immunized','Incomplete','Unknown'] as $opt)
          <option value="{{ $opt }}"
            {{ f('immunization_status',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Missed Vaccines</label>
      <input type="text"
             name="missed_vaccines"
             value="{{ f('missed_vaccines',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-4">
      <label class="form-label">Last Vaccine & Date</label>
      <div class="input-group">
        <input type="text"
               name="last_vaccine"
               value="{{ f('last_vaccine',$pediaForm) }}"
               class="form-control"
               placeholder="Name">
        <input type="date"
               name="last_vaccine_date"
               value="{{ f('last_vaccine_date',$pediaForm) }}"
               class="form-control">
      </div>
    </div>
  </div>

  {{-- IV. Nutritional Status --}}
  <h5 class="mt-4">IV. Nutritional Status</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">Current Feeding</label>
      <select name="feeding_type" class="form-select">
        <option value="">Select…</option>
        @foreach(['Breastfed','Formula','Mixed'] as $opt)
          <option value="{{ $opt }}"
            {{ f('feeding_type',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Solid Foods Introduced</label>
      <select name="solids_introduced" class="form-select">
        <option value="">Select…</option>
        @foreach(['Yes','No'] as $opt)
          <option value="{{ $opt }}"
            {{ f('solids_introduced',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">At Age (mo)</label>
      <input type="number"
             name="solids_age"
             value="{{ f('solids_age',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-1">
      <label class="form-label">Appetite</label>
      <select name="appetite" class="form-select">
        <option value="">Select…</option>
        @foreach(['Good','Poor'] as $opt)
          <option value="{{ $opt }}"
            {{ f('appetite',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-1">
      <label class="form-label">Weight Gain</label>
      <select name="weight_gain" class="form-select">
        <option value="">Select…</option>
        @foreach(['Normal','Not gaining'] as $opt)
          <option value="{{ $opt }}"
            {{ f('weight_gain',$pediaForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="mb-4">
    <label class="form-label">24-hour Diet Recall</label>
    <input type="text"
           name="diet_recall"
           value="{{ f('diet_recall',$pediaForm) }}"
           class="form-control">
  </div>

  {{-- V. Vital Signs --}}
  <h5 class="mt-4">V. Vital Signs</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <label class="form-label">Temperature (°C)</label>
      <input type="number" step="0.1"
             name="temp"
             value="{{ f('temp',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">HR (bpm)</label>
      <input type="number"
             name="hr"
             value="{{ f('hr',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">RR (cpm)</label>
      <input type="number"
             name="rr"
             value="{{ f('rr',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-3">
      <label class="form-label">BP (mmHg)</label>
      <div class="input-group">
        <input type="number" name="bp_systolic" value="{{ f('bp_systolic',$pediaForm) }}" class="form-control" placeholder="Sys">
        <span class="input-group-text">/</span>
        <input type="number" name="bp_diastolic" value="{{ f('bp_diastolic',$pediaForm) }}" class="form-control" placeholder="Dia">
      </div>
    </div>
    <div class="col-md-2">
      <label class="form-label">O₂ Sat (%)</label>
      <input type="number" step="1"
             name="o2_sat"
             value="{{ f('o2_sat',$pediaForm) }}"
             class="form-control">
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">Weight (kg)</label>
      <input type="number" step="0.01"
             name="weight"
             value="{{ f('weight',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-4">
      <label class="form-label">Height/Length (cm)</label>
      <input type="number" step="0.1"
             name="height"
             value="{{ f('height',$pediaForm) }}"
             class="form-control">
    </div>
    <div class="col-md-4">
      <label class="form-label">MUAC (cm)</label>
      <input type="number" step="0.1"
             name="muac"
             value="{{ f('muac',$pediaForm) }}"
             class="form-control">
    </div>
  </div>

  <button type="submit" class="btn btn-primary">
    {{ isset($pediaForm) ? 'Update' : 'Save' }}
  </button>
</form>
