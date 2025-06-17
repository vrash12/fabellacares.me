// resources/views/opd_forms/internal_consultation/_form.blade.php
@extends('layouts.admin')

@php
    if (! function_exists('f')) {
        function f(string $key, $form) {
            return old($key, data_get($form, $key, ''));
        }
    }
@endphp

<form method="POST" action="{{ $postRoute ?? route('triage.internal.store') }}">
    @csrf

    <div class="text-center mb-4">
        <h2>🏥 OPD CONSULTATION FORM – INTERNAL MEDICINE</h2>
    </div>

    {{-- I. Reason for Consultation --}}
    <h5>I. Reason for Consultation</h5>
    <div class="mb-3">
        <label class="form-label">Main Complaint</label>
        <input type="text"
               name="chief_complaint"
               value="{{ f('chief_complaint', $consultForm) }}"
               class="form-control">
    </div>

    {{-- II. Present Illness --}}
    <h5 class="mt-4">II. Present Illness</h5>
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Started when</label>
            <input type="date"
                   name="started_when"
                   value="{{ f('started_when', $consultForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">How long</label>
            <input type="text"
                   name="duration"
                   value="{{ f('duration', $consultForm) }}"
                   class="form-control">
        </div>
        <div class="col-md-4">
            <label class="form-label">Getting</label>
            <select name="progression" class="form-select">
                <option value="">Select…</option>
                @foreach(['Better','Worse','Same'] as $opt)
                    <option value="{{ $opt }}" {{ f('progression',$consultForm)==$opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="mb-3">
        <label class="form-label d-block">Other symptoms</label>
        @php
            $syms = ['Fever','Cough','Chest Pain','Headache','Numbness','Weakness','Stomach Pain','Diarrhea','Vomiting','Palpitations'];
            $checked = f('other_symptoms', $consultForm) ?: [];
        @endphp
        @foreach($syms as $s)
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="other_symptoms[]" value="{{ $s }}" {{ in_array($s, $checked) ? 'checked':'' }}>
                <label class="form-check-label">{{ $s }}</label>
            </div>
        @endforeach
n        <input type="text" name="other_symptoms_other" value="{{ f('other_symptoms_other',$consultForm) }}" class="form-control mt-2" placeholder="Others">
    </div>

    {{-- III. Past Illnesses --}}
    <h5 class="mt-4">III. Past Illnesses</h5>
    @php $conds = ['High Blood Pressure','Diabetes','Asthma / COPD','Heart Problem','TB','Stroke / Seizures']; @endphp
    <div class="row mb-3">
        @foreach($conds as $c)
            <div class="col-md-4 form-check">
                <input class="form-check-input" type="checkbox" name="past_illnesses[]" value="{{ $c }}" {{ in_array($c, f('past_illnesses',$consultForm)?:[]) ? 'checked':'' }}>
                <label class="form-check-label">{{ $c }}</label>
            </div>
        @endforeach
        <div class="col-md-6 mt-2">
            <input type="text" name="past_illnesses_other" value="{{ f('past_illnesses_other',$consultForm) }}" class="form-control" placeholder="Others">
        </div>
    </div>

    {{-- IV. Medicines & Allergies --}}
    <h5 class="mt-4">IV. Medicines & Allergies</h5>
    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Current Medicines</label>
            <input type="text" name="current_medicines" value="{{ f('current_medicines',$consultForm) }}" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">Allergies</label>
            <input type="text" name="allergies" value="{{ f('allergies',$consultForm) }}" class="form-control">
        </div>
    </div>

    {{-- V. Vitals --}}
    <h5 class="mt-4">V. Vitals</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <label class="form-label">BP</label>
            <div class="input-group">
                <input type="number" name="bp_systolic" value="{{ f('bp_systolic',$consultForm) }}" class="form-control" placeholder="Sys">
                <span class="input-group-text">/</span>
                <input type="number" name="bp_diastolic" value="{{ f('bp_diastolic',$consultForm) }}" class="form-control" placeholder="Dia">
            </div>
        </div>
        <div class="col-md-2">
            <input type="number" name="hr" value="{{ f('hr',$consultForm) }}" class="form-control" placeholder="HR bpm">
        </div>
        <div class="col-md-2">
            <input type="number" name="rr" value="{{ f('rr',$consultForm) }}" class="form-control" placeholder="RR cpm">
        </div>
        <div class="col-md-2">
            <input type="number" step="0.1" name="temp" value="{{ f('temp',$consultForm) }}" class="form-control" placeholder="°C">
        </div>
        <div class="col-md-1">
            <input type="number" name="height" value="{{ f('height',$consultForm) }}" class="form-control" placeholder="cm">
        </div>
        <div class="col-md-1">
            <input type="number" name="weight" value="{{ f('weight',$consultForm) }}" class="form-control" placeholder="kg">
        </div>
        <div class="col-md-3">
            <input type="number" step="0.1" name="blood_sugar" value="{{ f('blood_sugar',$consultForm) }}" class="form-control" placeholder="Blood Sugar mg/dL">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">
        {{ isset($consultForm) ? 'Update' : 'Save' }}
    </button>
</form>
