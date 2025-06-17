{{-- resources/views/opd_forms/teens_triage/_form.blade.php --}}

@php
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp

<form method="POST" action="{{ $postRoute ?? route('triage.teens.store') }}">
  @csrf

  <div class="text-center mb-4">
    <h2>🌱 WELL’COME TEENS</h2>
  </div>

  {{-- II. Chief Complaint --}}
  <h5>II. Chief Complaint</h5>
  <div class="mb-3">
    <label class="form-label">Chief Complaint</label>
    <input type="text"
           name="chief_complaint"
           value="{{ f('chief_complaint', $teensForm) }}"
           class="form-control">
  </div>

  {{-- III. Developmental History --}}
  <h5 class="mt-4">III. Developmental History</h5>
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <label class="form-label">Puberty onset</label>
      <input type="text"
             name="puberty_onset"
             value="{{ f('puberty_onset', $teensForm) }}"
             class="form-control">
    </div>
    <div class="col-md-4">
      <label class="form-label">Menarche</label>
      <input type="text"
             name="menarche"
             value="{{ f('menarche', $teensForm) }}"
             class="form-control">
    </div>
  </div>

  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <label class="form-label">Emotional/Behavioral concerns</label>
      <select name="emotional_concerns" class="form-select">
        <option value="">Select…</option>
        @foreach(['Yes','No'] as $opt)
          <option value="{{ $opt }}"
            {{ f('emotional_concerns',$teensForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-8">
      <label class="form-label">If Yes, specify</label>
      <input type="text"
             name="emotional_concerns_details"
             value="{{ f('emotional_concerns_details',$teensForm) }}"
             class="form-control">
    </div>
  </div>

  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <label class="form-label">Sexual activity</label>
      <select name="sexual_activity" class="form-select">
        <option value="">Select…</option>
        @foreach(['Yes','No'] as $opt)
          <option value="{{ $opt }}"
            {{ f('sexual_activity',$teensForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">Contraceptive use</label>
      <select name="contraceptive_use" class="form-select">
        <option value="">Select…</option>
        @foreach(['Yes','No'] as $opt)
          <option value="{{ $opt }}"
            {{ f('contraceptive_use',$teensForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-4">
      <label class="form-label">If Yes, Type</label>
      <input type="text"
             name="contraceptive_use_type"
             value="{{ f('contraceptive_use_type',$teensForm) }}"
             class="form-control">
    </div>
  </div>

  {{-- IV. Lifestyle --}}
  <h5 class="mt-4">IV. Lifestyle</h5>
  <div class="row g-3 mb-3">
    @foreach(['smoking','alcohol','drugs'] as $fld)
      <div class="col-md-2">
        <label class="form-label">{{ ucfirst($fld) }}</label>
        <select name="{{ $fld }}" class="form-select">
          <option value="">Select…</option>
          @foreach(['Yes','No'] as $opt)
            <option value="{{ $opt }}"
              {{ f($fld,$teensForm)==$opt?'selected':'' }}>
              {{ $opt }}
            </option>
          @endforeach
        </select>
      </div>
    @endforeach
    <div class="col-md-3">
      <label class="form-label">Sleeping habits</label>
      <select name="sleeping_habits" class="form-select">
        <option value="">Select…</option>
        @foreach(['Normal','Disrupted'] as $opt)
          <option value="{{ $opt }}"
            {{ f('sleeping_habits',$teensForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">Nutrition/Diet issues</label>
      <select name="nutrition_issues" class="form-select">
        <option value="">Select…</option>
        @foreach(['Yes','No'] as $opt)
          <option value="{{ $opt }}"
            {{ f('nutrition_issues',$teensForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
  </div>

  {{-- V. Vaccination Status --}}
  <h5 class="mt-4">V. Vaccination Status</h5>
  <div class="row g-3 mb-3">
    <div class="col-md-4">
      <label class="form-label">Status</label>
      <select name="vaccination_status" class="form-select">
        <option value="">Select…</option>
        @foreach(['Complete','Incomplete','Not known'] as $opt)
          <option value="{{ $opt }}"
            {{ f('vaccination_status',$teensForm)==$opt?'selected':'' }}>
            {{ $opt }}
          </option>
        @endforeach
      </select>
    </div>
    <div class="col-md-8">
      <label class="form-label">Last vaccines received</label>
      <input type="text"
             name="last_vaccines"
             value="{{ f('last_vaccines',$teensForm) }}"
             class="form-control">
    </div>
  </div>

  {{-- VI. Vital Signs --}}
  <h5 class="mt-4">VI. Vital Signs</h5>
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <label class="form-label">BP (mmHg)</label>
      <div class="input-group">
        <input type="number" name="bp_systolic" value="{{ f('bp_systolic',$teensForm) }}" class="form-control" placeholder="Systolic">
        <span class="input-group-text">/</span>
        <input type="number" name="bp_diastolic" value="{{ f('bp_diastolic',$teensForm) }}" class="form-control" placeholder="Diastolic">
      </div>
    </div>
    <div class="col-md-2">
      <label class="form-label">HR (bpm)</label>
      <input type="number"
             name="heart_rate"
             value="{{ f('heart_rate',$teensForm) }}"
             class="form-control">
    </div>
    <div class="col-md-2">
      <label class="form-label">RR (cpm)</label>
      <input type="number"
             name="resp_rate"
             value="{{ f('resp_rate',$teensForm) }}"
             class="form-control">
    </div>
    <div class="col-md-1">
      <label class="form-label">Temp (°C)</label>
      <input type="number" step="0.1"
             name="temperature"
             value="{{ f('temperature',$teensForm) }}"
             class="form-control">
    </div>
    <div class="col-md-1">
      <label class="form-label">Height (cm)</label>
      <input type="number"
             name="height"
             value="{{ f('height',$teensForm) }}"
             class="form-control">
    </div>
    <div class="col-md-1">
      <label class="form-label">Weight (kg)</label>
      <input type="number"
             name="weight"
             value="{{ f('weight',$teensForm) }}"
             class="form-control">
    </div>
    <div class="col-md-1">
      <label class="form-label">BMI</label>
      <input type="number" step="0.1"
             name="bmi"
             value="{{ f('bmi',$teensForm) }}"
             class="form-control">
    </div>
  </div>

  <button type="submit" class="btn btn-primary">
    {{ isset($teensForm) ? 'Update' : 'Save' }}
  </button>
</form>
