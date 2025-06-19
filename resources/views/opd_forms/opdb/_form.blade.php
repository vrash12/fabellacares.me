{{-- resources/views/opd_forms/opdb/_form.blade.php --}}

@php
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, "data.{$key}", ''));
        }
    }

    $action = $postRoute
        ?? ($opd_form
            ? route('opd_forms.update', $opd_form)
            : route('opd_forms.store'));

    $needPut = !isset($postRoute) && isset($opd_form);
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if($needPut)
        @method('PUT')
    @endif

    <div class="text-center bg-info text-white py-2 rounded mb-4">
        <h2 class="m-0">General OPD Registration Form</h2>
    </div>

    {{-- Core Metadata --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Form Name</label>
            <input value="GENERAL OPD FORM" class="form-control" readonly>
        </div>
        <div class="col-md-4">
            <label class="form-label">Form #</label>
            <input value="OPD-F-GEN" class="form-control" readonly>
        </div>
        <div class="col-md-4">
            <label class="form-label">Department</label>
            <input value="General" class="form-control" readonly>
        </div>
    </div>

    {{-- Date • Time • Record No. --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Date <span class="text-danger">*</span></label>
            <input type="date" name="date" value="{{ f('date',$opd_form) }}" class="form-control @error('date') is-invalid @enderror" required>
            @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-2">
            <label class="form-label">Time</label>
            <input type="time" name="time" value="{{ f('time',$opd_form) }}" class="form-control @error('time') is-invalid @enderror">
            @error('time')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-md-4">
            <label class="form-label">Health Record No.</label>
            <input type="text" name="record_no" value="{{ f('record_no',$opd_form) }}" class="form-control @error('record_no') is-invalid @enderror">
            @error('record_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>

    {{-- Personal Identifiers --}}
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <label class="form-label">Last Name <span class="text-danger">*</span></label>
            <input type="text" name="last_name" value="{{ f('last_name',$opd_form) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Given Name <span class="text-danger">*</span></label>
            <input type="text" name="given_name" value="{{ f('given_name',$opd_form) }}" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Middle Name</label>
            <input type="text" name="middle_name" value="{{ f('middle_name',$opd_form) }}" class="form-control">
        </div>
        <div class="col-md-1">
            <label class="form-label">Age</label>
            <input type="number" min="0" max="120" name="age" value="{{ f('age',$opd_form) }}" class="form-control" id="age-input">
        </div>
        <div class="col-md-2">
            <label class="form-label">Sex <span class="text-danger">*</span></label>
            <select name="sex" class="form-select" id="sex-select" required>
                <option value="">Select…</option>
                <option value="male"   {{ f('sex',$opd_form)=='male'   ? 'selected':'' }}>Male</option>
                <option value="female" {{ f('sex',$opd_form)=='female' ? 'selected':'' }}>Female</option>
            </select>
        </div>
    </div>

    {{-- Birth & Civil --}}
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Date of Birth</label>
            <input type="date" name="birth_date" value="{{ f('birth_date',$opd_form) }}" class="form-control" id="birth-date">
        </div>
        <div class="col-md-4">
            <label class="form-label">Civil Status <span class="text-danger">*</span></label>
            <select name="civil_status" class="form-select" id="civil-status" required>
                <option value="">Select…</option>
                @foreach(['single','married','widowed','separated','divorced'] as $stat)
                    <option value="{{ $stat }}" {{ f('civil_status',$opd_form)==$stat ? 'selected':'' }}>{{ ucfirst($stat) }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Occupation</label>
            <input type="text" name="occupation" value="{{ f('occupation',$opd_form) }}" class="form-control">
        </div>
    </div>

    {{-- Contact & Religion --}}
    <div class="row g-3 mb-3">
        <div class="col-md-6">
            <label class="form-label">Religion</label>
            <input type="text" name="religion" value="{{ f('religion',$opd_form) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Contact Number</label>
            <input type="text" name="contact_no" value="{{ f('contact_no',$opd_form) }}" class="form-control">
        </div>
    </div>

    {{-- Address (Philippine Address Selector) --}}
    <div class="row g-3 mb-3">
      <div class="col-md-6">
        <label class="form-label">Region</label>
        <select id="region" name="region_code" class="form-select"></select>
        <input type="hidden" name="region_text" id="region-text" value="{{ f('region_text',$opd_form) }}" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Province</label>
        <select id="province" name="province_code" class="form-select" disabled></select>
        <input type="hidden" name="province_text" id="province-text" value="{{ f('province_text',$opd_form) }}" />
      </div>
    </div>
    <div class="row g-3 mb-4">
      <div class="col-md-6">
        <label class="form-label">City / Municipality</label>
        <select id="city" name="city_code" class="form-select" disabled></select>
        <input type="hidden" name="city_text" id="city-text" value="{{ f('city_text',$opd_form) }}" />
      </div>
      <div class="col-md-6">
        <label class="form-label">Barangay</label>
        <select id="barangay" name="barangay_code" class="form-select" disabled></select>
        <input type="hidden" name="barangay_text" id="barangay-text" value="{{ f('barangay_text',$opd_form) }}" />
      </div>
    </div>

    {{-- Emergency Contact (auto‐shown for minors/elderly) --}}
    <div id="emergency-contact" style="display:none;">
        <div class="bg-warning bg-opacity-10 p-3 rounded mb-3">
            <h5 class="text-warning-emphasis mb-3">Emergency Contact Information</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Contact Person Name</label>
                    <input type="text" name="emergency_contact_name" value="{{ f('emergency_contact_name',$opd_form) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Relationship</label>
                    <input type="text" name="emergency_contact_relation" value="{{ f('emergency_contact_relation',$opd_form) }}" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Contact Number</label>
                    <input type="text" name="emergency_contact_phone" value="{{ f('emergency_contact_phone',$opd_form) }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Required?</label>
                    <div class="form-check mt-2">
                        <input type="checkbox" name="emergency_contact_required" value="1" class="form-check-input" id="emergency-required" {{ f('emergency_contact_required',$opd_form) ? 'checked':'' }}>
                        <label class="form-check-label" for="emergency-required">Required</label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Additional Notes --}}
    <div class="mb-4">
        <label class="form-label">Additional Notes / Medical History</label>
        <textarea name="medical_notes" class="form-control" rows="3">{{ f('medical_notes',$opd_form) }}</textarea>
    </div>

    @if(isset($showButtons) && $showButtons)
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">{{ isset($opd_form) ? '💾 Save' : '💾 Register' }}</button>
            <a href="{{ route('ob-opd-forms.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="reset" class="btn btn-outline-secondary">Clear</button>
        </div>
    @endif
</form>

{{-- emergency‐contact toggle script (unchanged) --}}
<script>
(function(){
  const ageInput=document.getElementById('age-input'),
        birthDate=document.getElementById('birth-date'),
        emergency=document.getElementById('emergency-contact'),
        requiredBox=document.getElementById('emergency-required');

  function calcAge(d){return Math.floor((new Date()-new Date(d))/31557600000);}
  function toggleEmergency(){
      const age=+ageInput.value||0;
      if(age<18||age>=65){
        emergency.style.display='block';
        if(age<18){requiredBox.checked=true;requiredBox.disabled=true;}
        else {requiredBox.disabled=false;}
      } else {
        emergency.style.display='none'; requiredBox.disabled=false;
      }
  }
  birthDate?.addEventListener('change',()=>{ageInput.value=calcAge(birthDate.value);toggleEmergency();});
  ageInput.addEventListener('input',toggleEmergency);
  document.addEventListener('DOMContentLoaded',toggleEmergency);
})();
</script>
