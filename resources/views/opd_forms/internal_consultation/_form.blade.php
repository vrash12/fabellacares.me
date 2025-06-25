{{-- resources/views/opd_forms/internal_consultation/_form.blade.php --}}
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

.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.form-check {
    background: #f8f9fb;
    border: 2px solid #e8ecf4;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    cursor: pointer;
}

.form-check:hover {
    background: #f0f2f5;
    border-color: #d1d9e6;
}

.form-check-input:checked + .form-check-label {
    color: #667eea;
    font-weight: 600;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.vitals-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1.5rem;
    margin-top: 1rem;
}

.vital-input {
    position: relative;
}

.vital-input .form-control {
    text-align: center;
    font-weight: 600;
    font-size: 1.1rem;
}

.vital-label {
    position: absolute;
    top: -8px;
    left: 15px;
    background: white;
    padding: 0 8px;
    font-size: 0.75rem;
    color: #667eea;
    font-weight: 600;
    text-transform: uppercase;
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
    
    .checkbox-group {
        grid-template-columns: 1fr;
    }
    
    .vitals-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .form-header h1 {
        font-size: 1.8rem;
    }
}
</style>

<div class="medical-form">
    <div class="form-container">
        <div class="form-header">
            <h1>🏥 OPD CONSULTATION FORM</h1>
            <div class="subtitle">Internal Medicine Department</div>
        </div>

        <div class="form-body">
            <form method="POST" action="{{ $postRoute ?? route('triage.internal.store') }}">
                @csrf
                @if(isset($consultForm))
                    @method('PUT')
                @endif
{{-- 0. Patient --}}
<div class="section-card">
    <h5 class="section-title">
        <span class="section-number">🩺</span>
        Patient
    </h5>

    <label class="form-label">Search patient (Last, Given)</label>
    <select id="patient_id"
            name="patient_id"
            class="form-select"
            style="width:100%">
        {{-- ✨ When editing, pre-select the saved patient --}}
        @isset($patient)
            <option value="{{ $patient->id }}" selected>
                {{ $patient->name }}
            </option>
        @endisset
    </select>
</div>

                {{-- I. Reason for Consultation --}}
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-number">I</span>
                        Reason for Consultation
                    </h5>
                    <div class="mb-3">
                        <label class="form-label">Main Complaint</label>
                        <input type="text"
                               name="chief_complaint"
                               value="{{ f('chief_complaint', $consultForm) }}"
                               class="form-control"
                               placeholder="Describe the main reason for today's visit">
                    </div>
                </div>

                {{-- II. Present Illness --}}
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-number">II</span>
                        Present Illness
                    </h5>
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Started When</label>
                            <input type="date"
                                   name="started_when"
                                   value="{{ f('started_when', $consultForm) }}"
                                   class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Duration</label>
                            <input type="text"
                                   name="duration"
                                   value="{{ f('duration', $consultForm) }}"
                                   class="form-control"
                                   placeholder="e.g., 3 days, 2 weeks">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Progression</label>
                            <select name="progression" class="form-select">
                                <option value="">Select progression…</option>
                                @foreach(['Better','Worse','Same'] as $opt)
                                    <option value="{{ $opt }}" {{ f('progression',$consultForm)==$opt ? 'selected' : '' }}>
                                        Getting {{ $opt }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Associated Symptoms</label>
                        @php
                            $syms = ['Fever','Cough','Chest Pain','Headache','Numbness','Weakness','Stomach Pain','Diarrhea','Vomiting','Palpitations'];
                            $checked = f('other_symptoms', $consultForm) ?: [];
                        @endphp
                        <div class="checkbox-group">
                            @foreach($syms as $s)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="other_symptoms[]" value="{{ $s }}" 
                                           id="symptom_{{ $loop->index }}" {{ in_array($s, $checked) ? 'checked':'' }}>
                                    <label class="form-check-label" for="symptom_{{ $loop->index }}">{{ $s }}</label>
                                </div>
                            @endforeach
                        </div>
                        <input type="text" name="other_symptoms_other" 
                               value="{{ f('other_symptoms_other',$consultForm) }}" 
                               class="form-control mt-3" 
                               placeholder="Other symptoms not listed above">
                    </div>
                </div>

                {{-- III. Past Illnesses --}}
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-number">III</span>
                        Medical History
                    </h5>
                    @php $conds = ['High Blood Pressure','Diabetes','Asthma / COPD','Heart Problem','TB','Stroke / Seizures']; @endphp
                    <div class="checkbox-group mb-3">
                        @foreach($conds as $c)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="past_illnesses[]" value="{{ $c }}" 
                                       id="condition_{{ $loop->index }}" {{ in_array($c, f('past_illnesses',$consultForm)?:[]) ? 'checked':'' }}>
                                <label class="form-check-label" for="condition_{{ $loop->index }}">{{ $c }}</label>
                            </div>
                        @endforeach
                    </div>
                    <input type="text" name="past_illnesses_other" 
                           value="{{ f('past_illnesses_other',$consultForm) }}" 
                           class="form-control" 
                           placeholder="Other medical conditions">
                </div>

                {{-- IV. Medicines & Allergies --}}
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-number">IV</span>
                        Medications & Allergies
                    </h5>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Current Medications</label>
                            <textarea name="current_medicines" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="List all current medications, supplements, and dosages">{{ f('current_medicines',$consultForm) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Known Allergies</label>
                            <textarea name="allergies" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="List any drug allergies, food allergies, or other sensitivities">{{ f('allergies',$consultForm) }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- V. Vitals --}}
                <div class="section-card">
                    <h5 class="section-title">
                        <span class="section-number">V</span>
                        Vital Signs & Measurements
                    </h5>
                    <div class="vitals-grid">
                        <div class="vital-input">
                            <label class="vital-label">Blood Pressure</label>
                            <div class="bp-group">
                                <input type="number" name="bp_systolic" 
                                       value="{{ f('bp_systolic',$consultForm) }}" 
                                       class="form-control" 
                                       placeholder="120">
                                <span class="bp-separator">/</span>
                                <input type="number" name="bp_diastolic" 
                                       value="{{ f('bp_diastolic',$consultForm) }}" 
                                       class="form-control" 
                                       placeholder="80">
                            </div>
                        </div>
                        
                        <div class="vital-input">
                            <label class="vital-label">Heart Rate</label>
                            <input type="number" name="hr" 
                                   value="{{ f('hr',$consultForm) }}" 
                                   class="form-control" 
                                   placeholder="72">
                        </div>
                        
                        <div class="vital-input">
                            <label class="vital-label">Respiratory Rate</label>
                            <input type="number" name="rr" 
                                   value="{{ f('rr',$consultForm) }}" 
                                   class="form-control" 
                                   placeholder="16">
                        </div>
                        
                        <div class="vital-input">
                            <label class="vital-label">Temperature (°C)</label>
                            <input type="number" step="0.1" name="temp" 
                                   value="{{ f('temp',$consultForm) }}" 
                                   class="form-control" 
                                   placeholder="36.5">
                        </div>
                        
                        <div class="vital-input">
                            <label class="vital-label">Height (cm)</label>
                            <input type="number" name="height" 
                                   value="{{ f('height',$consultForm) }}" 
                                   class="form-control" 
                                   placeholder="170">
                        </div>
                        
                        <div class="vital-input">
                            <label class="vital-label">Weight (kg)</label>
                            <input type="number" name="weight" 
                                   value="{{ f('weight',$consultForm) }}" 
                                   class="form-control" 
                                   placeholder="65">
                        </div>
                        
                        <div class="vital-input">
                            <label class="vital-label">Blood Sugar</label>
                            <input type="number" step="0.1" name="blood_sugar" 
                                   value="{{ f('blood_sugar',$consultForm) }}" 
                                   class="form-control" 
                                   placeholder="90">
                        </div>
                    </div>
                </div>

                <div class="submit-section">
                    <button type="submit" class="btn btn-primary">
                        {{ isset($consultForm) ? '💾 Update Consultation' : '💾 Save Consultation' }}
                    </button>
                    <a href="{{ route('triage.internal.index') }}" class="btn btn-secondary">
                        ❌ Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>