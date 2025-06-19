{{-- resources/views/opd_forms/triage/_form.blade.php --}}
@php
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp

<style>
/* you can pull in or adapt your existing “medical‐form” styles here */
.section-card { margin-bottom: 1.5rem; padding:1rem; border:1px solid #e0e0e0; border-radius:8px; }
.section-title { font-weight:600; margin-bottom:.5rem; }
</style>

<form method="POST" action="{{ $postRoute }}">
  @csrf
  @isset($triageForm) @method('PUT') @endisset

  {{-- ── Patient selector ─────────────────────────────────── --}}
  <div class="section-card">
  <label class="form-label">Patient</label>
  <select name="patient_id" class="form-select mb-3" required>
    <option value="">Select a patient…</option>
    @foreach($patients as $p)
      <option value="{{ $p->id }}"
        {{ f('patient_id', $triageForm)==$p->id ? 'selected' : '' }}>
        {{ $p->name }}
      </option>
    @endforeach
  </select>
</div>
  {{-- I. Tetanus Series --}}
  <div class="section-card">
    <h4 class="section-title">I. Tetanus Series</h4>
    <div class="row g-3">
      @for($i=1;$i<=5;$i++)
        <div class="col-md-4">
          <label class="form-label">T{{ $i }} Date</label>
          <input type="date"
                 name="tetanus_t{{ $i }}_date"
                 value="{{ f("tetanus_t{$i}_date",$triageForm) }}"
                 class="form-control">
        </div>
        <div class="col-md-8">
          <label class="form-label">T{{ $i }} Signature</label>
          <input type="text"
                 name="tetanus_t{{ $i }}_signature"
                 value="{{ f("tetanus_t{$i}_signature",$triageForm) }}"
                 class="form-control">
        </div>
      @endfor
    </div>
  </div>

  {{-- II. Present Problems --}}
  <div class="section-card">
    <h4 class="section-title">II. Present Health Problems</h4>
    <select name="present_health_problems[]" multiple class="form-select mb-2">
      @foreach(['Fever','Cough','Headache','Other'] as $opt)
        <option value="{{ $opt }}"
          {{ in_array($opt, old('present_health_problems', data_get($triageForm,'present_health_problems',[])))?'selected':'' }}>
          {{ $opt }}
        </option>
      @endforeach
    </select>
    <input type="text"
           name="present_problems_other"
           value="{{ f('present_problems_other',$triageForm) }}"
           class="form-control"
           placeholder="If other, specify…">
  </div>

  {{-- III. Danger Signs --}}
  <div class="section-card">
    <h4 class="section-title">III. Danger Signs</h4>
    <select name="danger_signs[]" multiple class="form-select mb-2">
      @foreach(['Chest Pain','Bleeding','Severe Pain','Other'] as $opt)
        <option value="{{ $opt }}"
          {{ in_array($opt, old('danger_signs', data_get($triageForm,'danger_signs',[])))?'selected':'' }}>
          {{ $opt }}
        </option>
      @endforeach
    </select>
    <input type="text"
           name="danger_signs_other"
           value="{{ f('danger_signs_other',$triageForm) }}"
           class="form-control"
           placeholder="If other, specify…">
  </div>

{{-- IV. OB History --}}
<div class="section-card">
  <h4 class="section-title">IV. OB History</h4>

  @php
    // grab old input or existing model array, default empty array
    $obHistory = old('ob_history', data_get($triageForm, 'ob_history', []));
  @endphp

  @foreach($obHistory as $idx => $entry)
    <textarea
      name="ob_history[{{ $idx }}]"
      rows="3"
      class="form-control mb-2"
    >{{ $entry }}</textarea>
  @endforeach

  {{-- one empty row to add more --}}
  <textarea
    name="ob_history[]"
    rows="3"
    class="form-control"
    placeholder="Add another OB history entry…"
  ></textarea>
</div>

  {{-- V. Family Planning / PNC --}}
  <div class="section-card">
    <h4 class="section-title">V. Family Planning &amp; PNC</h4>
    <div class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Family Planning</label>
        <select name="family_planning" class="form-select">
          <option value="">—</option>
          @foreach(['Pills','IUD','Injectable','Withdrawal','Standard'] as $opt)
            <option value="{{ $opt }}" {{ f('family_planning',$triageForm)==$opt?'selected':'' }}>
              {{ $opt }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Prev. PNC</label>
        <select name="prev_pnc" class="form-select">
          <option value="">—</option>
          @foreach(['Private','MD','HC','TBA'] as $opt)
            <option value="{{ $opt }}" {{ f('prev_pnc',$triageForm)==$opt?'selected':'' }}>
              {{ $opt }}
            </option>
          @endforeach
        </select>
      </div>
    </div>
  </div>

  {{-- VI. Dates & Counts --}}
  <div class="section-card">
    <h4 class="section-title">VI. Dates &amp; Counts</h4>
    <div class="row g-3">
      <div class="col-md-4">
        <label class="form-label">LMP</label>
        <input type="date" name="lmp" value="{{ f('lmp',$triageForm) }}" class="form-control">
      </div>
      <div class="col-md-4">
        <label class="form-label">EDC</label>
        <input type="date" name="edc" value="{{ f('edc',$triageForm) }}" class="form-control">
      </div>
      @foreach(['gravida','parity_t','parity_p','parity_a','parity_l','aog_weeks'] as $fld)
        <div class="col-md-2">
          <label class="form-label">{{ ucwords(str_replace('_',' ',$fld)) }}</label>
          <input type="number"
                 name="{{ $fld }}"
                 value="{{ f($fld,$triageForm) }}"
                 class="form-control">
        </div>
      @endforeach
    </div>
  </div>

  {{-- VII. Chief Complaint & Exam --}}
  <div class="section-card">
    <h4 class="section-title">VII. Chief Complaint &amp; Exam</h4>
    <div class="mb-3">
      <label class="form-label">Chief Complaint</label>
      <input type="text"
             name="chief_complaint"
             value="{{ f('chief_complaint',$triageForm) }}"
             class="form-control">
    </div>
  {{-- VII. Physical Exam Log --}}
<div class="section-card">
  <h4 class="section-title">VII. Physical Exam Log</h4>

  @php
    $examLog = old('physical_exam_log', data_get($triageForm, 'physical_exam_log', []));
  @endphp

  @foreach($examLog as $idx => $entry)
    <textarea
      name="physical_exam_log[{{ $idx }}]"
      rows="2"
      class="form-control mb-2"
    >{{ $entry }}</textarea>
  @endforeach

  <textarea
    name="physical_exam_log[]"
    rows="2"
    class="form-control"
    placeholder="Add another exam log…"
  ></textarea>
</div>

  {{-- VIII. Delivery & Newborn --}}
  <div class="section-card">
    <h4 class="section-title">VIII. Delivery &amp; Newborn</h4>
    <div class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Prepared By</label>
        <input type="text" name="prepared_by" value="{{ f('prepared_by',$triageForm) }}" class="form-control">
      </div>
   <div class="col-md-2">
  <label class="form-label">Blood Type</label>
  <select name="blood_type" class="form-select">
    <option value="">—</option>
    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bt)
      <option value="{{ $bt }}"
        {{ f('blood_type',$triageForm)==$bt ? 'selected' : '' }}>
        {{ $bt }}
      </option>
    @endforeach
  </select>
</div>

  <div class="col-md-3">
  <label class="form-label">Delivery Type</label>
  <select name="delivery_type" class="form-select">
    <option value="">—</option>
    @foreach(['Vaginal','Cesarean','Forceps','Vacuum'] as $method)
      <option value="{{ $method }}"
        {{ f('delivery_type',$triageForm)==$method ? 'selected' : '' }}>
        {{ $method }}
      </option>
    @endforeach
  </select>
</div>

      <div class="col-md-2">
        <label class="form-label">Birth Wt (kg)</label>
        <input type="number" step="0.01" name="birth_weight" value="{{ f('birth_weight',$triageForm) }}" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Birth L (cm)</label>
        <input type="number" step="0.01" name="birth_length" value="{{ f('birth_length',$triageForm) }}" class="form-control">
      </div>
    </div>
    <div class="row g-3 mt-2">
      @foreach(['appearance','pulse','grimace','activity','respiration'] as $fld)
        <div class="col-md-2">
          <label class="form-label">Apgar {{ ucfirst($fld) }}</label>
          <input type="number" min="0" max="2" name="apgar_{{ $fld }}" value="{{ f("apgar_{$fld}",$triageForm) }}" class="form-control">
        </div>
      @endforeach
    </div>
  </div>

  <div class="d-flex justify-content-end mt-4">
    <button type="submit" class="btn btn-success me-2">
      {{ isset($triageForm) ? 'Update Triage' : 'Save Triage' }}
    </button>
    <a href="{{ route('opd_forms.triage.index') }}" class="btn btn-secondary">
      <i class="bi bi-x-lg"></i> Cancel
    </a>
  </div>
</form>
