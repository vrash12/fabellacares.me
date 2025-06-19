{{-- resources/views/opd_forms/teens_triage/_form.blade.php --}}
@php
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp
<style>
.medical-form {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    padding: 2rem 0;
}

.form-container {
    max-width: 1000px;
    margin: 0 auto;
    background: #ffffff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-header {
    background: linear-gradient(135deg, #2c5aa0 0%, #1e3c72 100%);
    color: white;
    padding: 2rem;
    text-align: center;
    position: relative;
    overflow: hidden;
}

.form-header::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
    animation: float 20s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translate(-25%, -25%) rotate(0deg); }
    50% { transform: translate(-25%, -25%) rotate(5deg); }
}

.form-header h1 {
    position: relative;
    z-index: 2;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.form-header .subtitle {
    position: relative;
    z-index: 2;
    font-size: 1.1rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

.form-body {
    padding: 3rem;
}

.section-card {
    background: #ffffff;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    border: 1px solid #e8ecf4;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.section-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.section-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
}

.section-title {
    color: #2c5aa0;
    font-size: 1.4rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-number {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin-right: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e8ecf4;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fafbfc;
}

.form-control:focus, .form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    background: #ffffff;
    transform: translateY(-1px);
}

.form-label {
    font-weight: 600;
    color: #2c3e50;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.bp-group {
    display: flex;
    gap: 10px;
    align-items: center;
}

.bp-separator {
    font-size: 1.5rem;
    font-weight: bold;
    color: #667eea;
}

.submit-section {
    background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
    padding: 2rem;
    border-radius: 15px;
    text-align: center;
    margin-top: 2rem;
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50px;
    padding: 1rem 3rem;
    font-weight: 600;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
}

.btn-secondary {
    background: #6c757d;
    border: none;
    border-radius: 50px;
    padding: 1rem 2rem;
    font-weight: 600;
    margin-left: 1rem;
    transition: all 0.3s ease;
}

.btn-secondary:hover {
    background: #5a6268;
    transform: translateY(-1px);
}

.input-group-text {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    font-weight: bold;
}

@media (max-width: 768px) {
    .form-body {
        padding: 1.5rem;
    }

    .form-header h1 {
        font-size: 1.8rem;
    }
}
</style>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.4.0/dist/select2-bootstrap-5.min.css" rel="stylesheet"/>
@endpush


<div class="medical-form">
  <div class="form-container">
    <div class="form-header">
      <h1>🌱 WELL’COME TEENS</h1>
      <div class="subtitle">Adolescent Triage Form</div>
    </div>

    <div class="form-body">
      <form method="POST" action="{{ $postRoute ?? route('triage.teens.store') }}">
        @csrf
        @if(isset($teensForm)) @method('PUT') @endif
     {{-- I. Patient Selection --}}
        <div class="section-card">
          <h5 class="section-title">
            <span class="section-number">I</span>
            Patient
          </h5>
          <div class="mb-3">
            <label class="form-label">Search &amp; Select Patient</label>
            <select id="patientSelect"
                    name="patient_id"
                    class="form-select"
                    style="width:100%;"
                    required>
              <option value="">‒ Type to search ‒</option>
            </select>
            @error('patient_id')
              <div class="text-danger small">{{ $message }}</div>
            @enderror
          </div>
        </div>
        {{-- II. Chief Complaint --}}
        <div class="section-card">
          <h5 class="section-title">
            <span class="section-number">II</span>
            Chief Complaint
          </h5>
          <div class="mb-3">
            <label class="form-label">Chief Complaint</label>
            <input type="text"
                   name="chief_complaint"
                   value="{{ f('chief_complaint', $teensForm) }}"
                   class="form-control"
                   placeholder="Describe main concern">
          </div>
        </div>

        {{-- III. Developmental History --}}
        <div class="section-card">
          <h5 class="section-title">
            <span class="section-number">III</span>
            Developmental History
          </h5>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Puberty Onset</label>
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
              <label class="form-label">Emotional/Behavioral Concerns</label>
              <select name="emotional_concerns" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}" {{ f('emotional_concerns',$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">If Yes, Specify</label>
              <input type="text"
                     name="emotional_concerns_details"
                     value="{{ f('emotional_concerns_details',$teensForm) }}"
                     class="form-control"
                     placeholder="Details">
            </div>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Sexual Activity</label>
              <select name="sexual_activity" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}" {{ f('sexual_activity',$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Contraceptive Use</label>
              <select name="contraceptive_use" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}" {{ f('contraceptive_use',$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">If Yes, Type</label>
              <input type="text"
                     name="contraceptive_use_type"
                     value="{{ f('contraceptive_use_type',$teensForm) }}"
                     class="form-control"
                     placeholder="Type">
            </div>
          </div>
        </div>

        {{-- IV. Lifestyle --}}
        <div class="section-card">
          <h5 class="section-title">
            <span class="section-number">IV</span>
            Lifestyle
          </h5>
          <div class="row g-3 mb-3">
            @foreach(['smoking','alcohol','drugs'] as $fld)
              <div class="col-md-2">
                <label class="form-label">{{ ucfirst($fld) }}</label>
                <select name="{{ $fld }}" class="form-select">
                  <option value="">Select…</option>
                  @foreach(['Yes','No'] as $opt)
                    <option value="{{ $opt }}" {{ f($fld,$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                  @endforeach
                </select>
              </div>
            @endforeach
            <div class="col-md-3">
              <label class="form-label">Sleeping Habits</label>
              <select name="sleeping_habits" class="form-select">
                <option value="">Select…</option>
                @foreach(['Normal','Disrupted'] as $opt)
                  <option value="{{ $opt }}" {{ f('sleeping_habits',$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Nutrition/Diet Issues</label>
              <select name="nutrition_issues" class="form-select">
                <option value="">Select…</option>
                @foreach(['Yes','No'] as $opt)
                  <option value="{{ $opt }}" {{ f('nutrition_issues',$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- V. Vaccination Status --}}
        <div class="section-card">
          <h5 class="section-title">
            <span class="section-number">V</span>
            Vaccination Status
          </h5>
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">Status</label>
              <select name="vaccination_status" class="form-select">
                <option value="">Select…</option>
                @foreach(['Complete','Incomplete','Not known'] as $opt)
                  <option value="{{ $opt }}" {{ f('vaccination_status',$teensForm)==$opt?'selected':'' }}>{{ $opt }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Last Vaccines Received</label>
              <input type="text"
                     name="last_vaccines"
                     value="{{ f('last_vaccines',$teensForm) }}"
                     class="form-control"
                     placeholder="E.g. MMR, Tdap">
            </div>
          </div>
        </div>

        {{-- VI. Vital Signs --}}
        <div class="section-card">
          <h5 class="section-title">
            <span class="section-number">VI</span>
            Vital Signs
          </h5>
          <div class="row g-3 mb-4">
            <div class="col-md-3">
              <label class="vital-label">Blood Pressure</label>
              <div class="bp-group">
                <input type="number" name="bp_systolic" value="{{ f('bp_systolic',$teensForm) }}" class="form-control" placeholder="Systolic">
                <span class="bp-separator">/</span>
                <input type="number" name="bp_diastolic" value="{{ f('bp_diastolic',$teensForm) }}" class="form-control" placeholder="Diastolic">
              </div>
            </div>
            <div class="col-md-2">
              <label class="form-label">HR (bpm)</label>
              <input type="number" name="heart_rate" value="{{ f('heart_rate',$teensForm) }}" class="form-control">
            </div>
            <div class="col-md-2">
              <label class="form-label">RR (cpm)</label>
              <input type="number" name="resp_rate" value="{{ f('resp_rate',$teensForm) }}" class="form-control">
            </div>
            <div class="col-md-1">
              <label class="form-label">Temp (°C)</label>
              <input type="number" step="0.1" name="temperature" value="{{ f('temperature',$teensForm) }}" class="form-control">
            </div>
            <div class="col-md-1">
              <label class="form-label">Height (cm)</label>
              <input type="number" name="height" value="{{ f('height',$teensForm) }}" class="form-control">
            </div>
            <div class="col-md-1">
              <label class="form-label">Weight (kg)</label>
              <input type="number" name="weight" value="{{ f('weight',$teensForm) }}" class="form-control">
            </div>
            <div class="col-md-1">
              <label class="form-label">BMI</label>
              <input type="number" step="0.1" name="bmi" value="{{ f('bmi',$teensForm) }}" class="form-control">
            </div>
          </div>
        </div>

        <div class="submit-section">
          <button type="submit" class="btn btn-primary">{{ isset($teensForm) ? '💾 Update' : '💾 Save' }}</button>
          <a href="{{ route('triage.teens.index') }}" class="btn btn-secondary">❌ Cancel</a>
        </div>

      </form>
    </div>
  </div>
</div>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
  $(function() {
    $('#patientSelect').select2({
      theme: 'bootstrap-5',
      placeholder: '‒ Search patient ‒',
      allowClear: true,
      minimumInputLength: 1,
      ajax: {
        url: '{{ route("patients.search") }}',
        dataType: 'json',
        delay: 250,
        data: params => ({ q: params.term }),
        processResults: data => ({ results: data.results }),
        cache: true
      }
    });

    @if(old('patient_id', data_get($teensForm, 'patient_id')))
    // Preload when editing
    (function(){
      const pid   = '{{ old("patient_id", data_get($teensForm, "patient_id")) }}';
      const pname = '{{ optional(\App\Models\Patient::find(old("patient_id", data_get($teensForm, "patient_id"))))->name }}';
      if (pid && pname) {
        const option = new Option(pname, pid, true, true);
        $('#patientSelect').append(option).trigger('change');
      }
    })();
    @endif
  });
</script>
@endpush