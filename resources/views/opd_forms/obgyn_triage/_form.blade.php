{{-- resources/views/opd_forms/obgyn_triage/_form.blade.php --}}

@php
    // helper to pull old input or existing model data
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp

<form method="POST" action="{{ $postRoute ?? route('triage.obgyn.store') }}">
    @csrf

    <div class="text-center mb-4">
        <h2>🩺 OB-GYN TRIAGE FORM</h2>
    </div>
{{-- 0. Patient --}}
<div class="mb-4">
  <h5>🩺 Patient</h5>
  <label class="form-label">Search patient (Last, Given)</label>
  <select id="patient_id"
          name="patient_id"
          class="form-select"
          style="width: 100%">
      @isset($patient)
        <option value="{{ $patient->id }}" selected>
          {{ $patient->name }}
        </option>
      @endisset
  </select>
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

    {{-- II. Present Illness History --}}
    <h5 class="mt-4">II. Present Illness History</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Onset of symptoms</label>
            <input type="text"
                   name="onset"
                   value="{{ f('onset', $triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Duration</label>
            <input type="text"
                   name="duration"
                   value="{{ f('duration', $triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Pain scale (0–10)</label>
            <input type="number" min="0" max="10"
                   name="pain_scale"
                   value="{{ f('pain_scale', $triageForm) }}"
                   class="form-control">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Description</label>
            <select name="description" class="form-select">
                <option value="">Select…</option>
                @foreach(['Sharp','Dull','Cramping','Burning'] as $opt)
                    <option value="{{ $opt }}"
                      {{ f('description',$triageForm)==$opt?'selected':'' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <label class="form-label">Associated symptoms</label>
    <div class="row mb-3">
        @php
            $symps = [
              'Vaginal bleeding','Discharge','Itching','Painful urination',
              'Pelvic pain','Missed period'
            ];
            $checked = f('associated_symptoms',$triageForm) ?: [];
        @endphp
        @foreach($symps as $s)
            <div class="col-md-4 form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="associated_symptoms[]"
                       value="{{ $s }}"
                       {{ in_array($s,$checked)?'checked':'' }}>
                <label class="form-check-label">{{ $s }}</label>
            </div>
        @endforeach
        <div class="col-md-6 mt-2">
            <label class="form-label">Others</label>
            <input type="text"
                   name="associated_symptoms_other"
                   value="{{ f('associated_symptoms_other',$triageForm) }}"
                   class="form-control">
        </div>
    </div>

    {{-- III. Menstrual History --}}
    <h5 class="mt-4">III. Menstrual History</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Age of menarche</label>
            <input type="number" min="0"
                   name="menarche_age"
                   value="{{ f('menarche_age',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Cycle (days)</label>
            <input type="number" min="0"
                   name="cycle_length"
                   value="{{ f('cycle_length',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-3">
            <label class="form-label">Flow</label>
            <select name="flow" class="form-select">
                <option value="">Select…</option>
                @foreach(['Light','Moderate','Heavy'] as $opt)
                    <option value="{{ $opt }}"
                      {{ f('flow',$triageForm)==$opt?'selected':'' }}>
                        {{ $opt }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">LMP</label>
            <input type="date"
                   name="lmp"
                   value="{{ f('lmp',$triageForm) }}"
                   class="form-control">
        </div>
    </div>

    <label class="form-label">Menstrual concerns</label>
    <div class="mb-3 row">
        @php
           $concerns = ['Dysmenorrhea','Irregular cycle','Amenorrhea'];
        @endphp
        @foreach($concerns as $c)
            <div class="col-md-4 form-check">
                <input class="form-check-input"
                       type="checkbox"
                       name="menstrual_concerns[]"
                       value="{{ $c }}"
                       {{ in_array($c, f('menstrual_concerns',$triageForm)?:[]) ? 'checked':'' }}>
                <label class="form-check-label">{{ $c }}</label>
            </div>
        @endforeach
    </div>

    {{-- IV. Obstetric History --}}
    <h5 class="mt-4">IV. Obstetric History</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-2">
            <label class="form-label">Gravida</label>
            <input type="number" min="0"
                   name="gravida"
                   value="{{ f('gravida',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Para</label>
            <input type="number" min="0"
                   name="para"
                   value="{{ f('para',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Full-term</label>
            <input type="number" min="0"
                   name="full_term"
                   value="{{ f('full_term',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Preterm</label>
            <input type="number" min="0"
                   name="preterm"
                   value="{{ f('preterm',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Abortion</label>
            <input type="number" min="0"
                   name="abortion"
                   value="{{ f('abortion',$triageForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label">Living</label>
            <input type="number" min="0"
                   name="living"
                   value="{{ f('living',$triageForm) }}"
                   class="form-control">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Previous pregnancies</label>
            <select name="prev_pregnancy_type" class="form-select">
                <option value="">Select…</option>
                @foreach(['Normal','Cesarean','Complicated'] as $opt)
                  <option value="{{ $opt }}"
                    {{ f('prev_pregnancy_type',$triageForm)==$opt?'selected':'' }}>
                    {{ $opt }}
                  </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Current pregnancy?</label>
            <select name="current_pregnancy" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}"
                    {{ f('current_pregnancy',$triageForm)==$opt?'selected':'' }}>
                    {{ $opt }}
                  </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">If yes, weeks gestation</label>
            <input type="number" min="0"
                   name="gestation_weeks"
                   value="{{ f('gestation_weeks',$triageForm) }}"
                   class="form-control">
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Prenatal checkups done?</label>
            <select name="prenatal_done" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}"
                    {{ f('prenatal_done',$triageForm)==$opt?'selected':'' }}>
                    {{ $opt }}
                  </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Danger signs?</label>
            <select name="danger_signs_present" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}"
                    {{ f('danger_signs_present',$triageForm)==$opt?'selected':'' }}>
                    {{ $opt }}
                  </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">If yes, specify</label>
            <input type="text"
                   name="danger_signs_details"
                   value="{{ f('danger_signs_details',$triageForm) }}"
                   class="form-control">
        </div>
    </div>

    {{-- V. Gynecologic History --}}
    <h5 class="mt-4">V. Gynecologic History</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Pap smear history</label>
            <div class="input-group">
              <select name="pap_smear_done" class="form-select">
                  <option value="">Select…</option>
                  @foreach(['Yes','No'] as $opt)
                    <option value="{{ $opt }}"
                      {{ f('pap_smear_done',$triageForm)==$opt?'selected':'' }}>
                      {{ $opt }}
                    </option>
                  @endforeach
              </select>
              <input type="date"
                     name="pap_smear_date"
                     value="{{ f('pap_smear_date',$triageForm) }}"
                     class="form-control">
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label">History of STIs</label>
            <select name="sti_history" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}"
                    {{ f('sti_history',$triageForm)==$opt?'selected':'' }}>
                    {{ $opt }}
                  </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Contraceptive use</label>
        <select name="contraceptive_use" class="form-select">
            <option value="">Select…</option>
            @foreach(['Pills','IUD','Injectables','Condom','None'] as $opt)
              <option value="{{ $opt }}"
                {{ f('contraceptive_use',$triageForm)==$opt?'selected':'' }}>
                {{ $opt }}
              </option>
            @endforeach
        </select>
    </div>

    {{-- VI. Vital Signs --}}
    <h5 class="mt-4">VI. Vital Signs</h5>
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <label class="form-label">BP (mmHg)</label>
        <div class="input-group">
          <input type="number" name="bp_systolic" value="{{ f('bp_systolic',$triageForm) }}" class="form-control" placeholder="Systolic">
          <span class="input-group-text">/</span>
          <input type="number" name="bp_diastolic" value="{{ f('bp_diastolic',$triageForm) }}" class="form-control" placeholder="Diastolic">
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label">HR (bpm)</label>
        <input type="number" name="heart_rate" value="{{ f('heart_rate',$triageForm) }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">RR (cpm)</label>
        <input type="number" name="resp_rate" value="{{ f('resp_rate',$triageForm) }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Temp (°C)</label>
        <input type="number" step="0.1" name="temperature" value="{{ f('temperature',$triageForm) }}" class="form-control">
      </div>
      <div class="col-md-1">
        <label class="form-label">Height (cm)</label>
        <input type="number" name="height" value="{{ f('height',$triageForm) }}" class="form-control">
      </div>
      <div class="col-md-1">
        <label class="form-label">Weight (kg)</label>
        <input type="number" name="weight" value="{{ f('weight',$triageForm) }}" class="form-control">
      </div>
    </div>

    <button type="submit" class="btn btn-primary">
      {{ isset($triageForm) ? 'Update Form' : 'Save Form' }}
    </button>
</form>
@once
  @push('scripts')
  <script>
    $(function(){
      $('#patient_id').select2({
        placeholder: 'Type at least 2 letters…',
        minimumInputLength: 2,
        allowClear: true,
        ajax: {
          url: @json(route('patients.search')),
          data: params => ({ q: params.term }),
          processResults: data => ({ results: data.results })
        },
        templateResult: item  => item.text || item.id,
        templateSelection: item => item.text || item.id,
        width: '100%'
      });
    });
  </script>
  @endpush
@endonce
