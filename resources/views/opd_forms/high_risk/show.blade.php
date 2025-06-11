{{-- resources/views/opd_forms/high_risk/show.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
@php
    /* ------------------------------------------------------------------
       Helper: flatten the same label map used in the _form partial
    ------------------------------------------------------------------ */
    $riskMap = collect([
        // Left‐column labels
        '# SCARRED UTERUS / PREVIOUS CESAREAN' => null,
        'scarred_uterus'       => 'Scarred Uterus / Previous CS',
        '# DIABETES MELLITUS'  => null,
        'gest_dm'              => 'Gestational Diabetes Mellitus',
        'overt_dm'             => 'Pre‐gestational / Overt Diabetes',
        '# HYPERTENSIVE DISORDERS' => null,
        'pre_eclampsia'        => 'Pre‐eclampsia',
        'severe_pre_eclampsia' => 'Pre‐eclampsia (severe)',
        'chronic_htn'          => 'Chronic Hypertension',
        'chronic_htn_pre_eclamp'=> 'Chronic Hypertension with Pre‐eclampsia',
        '# OTHER MEDICAL/SURG COMPLICATIONS' => null,
        'anemia'                => 'Anemia',
        'other_hematologic'     => 'Other Hematologic Disorder',
        'bronchial_asthma'      => 'Bronchial Asthma',
        'cong_hd'               => 'Congenital Heart Disease',
        'acquired_hd'           => 'Acquired Heart Disease',
        'goiter'                => 'Goiter',
        'hypothyroidism'        => 'Hypothyroidism',
        'hyperthyroidism'       => 'Hyperthyroidism',
        'chronic_renal'         => 'Chronic Renal Disease',
        'copd'                  => 'Chronic Obstructive Pulmonary Disease',
        'hypokalemia'           => 'Hypokalemia',
        'extra_genital_ca'      => 'Extra‐Genital Cancer',
        'epilepsy'              => 'Epilepsy / Seizure Disorder',
        'other_neuro'           => 'Other Neurologic Disease',
        'musculo_skeletal'      => 'Musculo‐skeletal Disorder',
        'psychiatric'           => 'Psychiatric Disorder',
        'others_med_surg'       => 'Others (specify)',
        '# GENERATIVE TRACT DISORDERS' => null,
        'cong_ut_anomaly'       => 'Congenital Uterine Anomalies',
        'incompetent_cervix'    => 'Incompetent Cervix',
        'myoma'                 => 'Myoma',
        'ovarian_cyst'          => 'Ovarian Cyst / New growth',
        'pcos'                  => 'Polycystic Ovaries',
        'genital_tract_ca'      => 'Genital Tract Cancer',
        'others_generative'     => 'Others (specify)',
        '# HISTORY OF INFERTILITY' => null,
        'history_infertility'   => 'History of Infertility',
        '# HISTORY OF H-MOLE (within 1 year)' => null,
        'history_h_mole'        => 'History of H‐Mole',
        '# RH NEGATIVE BLOOD TYPE' => null,
        'rh_negative'           => 'Rh Negative Blood Type',
        '# FETAL CONGENITAL ANOMALY' => null,
        'fetal_congenital_anomaly' => 'Fetal Congenital Anomaly',
        // Right‐column labels...
        'poor_ob_history'       => 'Poor Obstetric History',
        'others_generative'     => 'Others (specify)',
        'young_primigravida'    => 'Young Primigravida (≤ 18 yrs)',
        'elderly_primigravida'  => 'Elderly Primigravida (≥ 35 yrs)',
        'underweight'           => 'Underweight (BMI ≤ 19)',
        'obese'                 => 'Obese (BMI ≥ 30)',
        'grandmultiparity'      => 'Grandmultiparity (≥ Para 5)',
        'nearing_postdatism'    => 'Nearing Postdatism (≥ 41 wks)',
        'postdatism'            => 'Postdatism (≥ 42 wks)',
        'twins'                 => 'Twins',
        'triplets'              => 'Triplets',
        'threatened_abortion'   => 'Threatened Abortion',
        'placenta_previa'       => 'Placenta Previa',
        'placenta_accreta'      => 'Placenta Accreta',
        'abruption_placenta'    => 'Abruption Placenta',
        'iugr'                  => 'Intrauterine Growth Restriction',
        'fetal_macrosomia'      => 'Fetal Macrosomia (> 4 kg)',
        'oligohydramnios'       => 'Oligohydramnios / Anhydramnios',
        'polyhydramnios'        => 'Polyhydramnios / Hydramnios',
        'preterm_labor'         => 'Preterm Labor',
        'preterm_prom'          => 'Preterm PROM',
        'term_prom'             => 'Term PROM',
        'uti'                   => 'Urinary Tract Infection',
        'uri'                   => 'Upper Respiratory Tract Infection',
        'pneumonia'             => 'Pneumonia',
        'tuberculosis'          => 'Tuberculosis',
        'bacterial_vaginosis'   => 'Bacterial Vaginosis',
        'trichomoniasis'        => 'Trichomoniasis',
        'hepatitis_b'           => 'Hepatitis B',
        'syphilis'              => 'Syphilis',
        'hpv'                   => 'Human Papillomavirus',
        'hiv'                   => 'HIV',
        'others_infection'      => 'Others (specify)',
    ]);

    /* ------------------------------------------------------------------
       Extract the stored answers
    ------------------------------------------------------------------ */
    $answers  = (object) $submission->answers;
    $selected = collect($answers->risks ?? []);
    $specify  = collect($answers)
                 ->filter(fn($v,$k)=>str_ends_with($k,'_specify') && $v);
@endphp

<div class="page-header mb-4">
  <h1 class="h3">Submission #{{ $submission->id }} – Identification of High Risk</h1>
  <small class="text-muted">
    Filed {{ $submission->created_at->format('M d, Y h:i A') }}
    by <strong>{{ $submission->user->name }}</strong>
  </small>
</div>

<div class="card shadow-sm mb-4">
  <div class="card-body">

    {{-- Patient Info --}}
    <h5 class="mb-3"><i class="bi bi-person-fill me-2"></i>Patient Information</h5>
    <div class="row g-3 mb-4">
      <div class="col-md-4">
        <label class="form-label text-secondary small">Name</label>
        <div class="fw-semibold">
          {{ trim(($answers->last_name ?? '').', '.($answers->given_name ?? '')) ?: '—' }}
        </div>
      </div>
      <div class="col-md-2">
        <label class="form-label text-secondary small">Age</label>
        <div class="fw-semibold">{{ $answers->age ?? '—' }}</div>
      </div>
      <div class="col-md-2">
        <label class="form-label text-secondary small">Sex</label>
        <div class="fw-semibold text-uppercase">{{ $answers->sex ?? '—' }}</div>
      </div>
      <div class="col-md-4">
        <label class="form-label text-secondary small">Patient ID</label>
        <div class="fw-semibold">{{ $answers->patient_id ? '#'.$answers->patient_id : '—' }}</div>
      </div>
    </div>

    {{-- Risk Factors --}}
    <h5 class="mb-3"><i class="bi bi-exclamation-triangle-fill me-2"></i>Risk Factors</h5>
    @if($selected->isEmpty())
      <p class="fst-italic text-muted">No risk factors were selected.</p>
    @else
      <div class="row">
        <div class="col-md-6">
          <ul class="list-group list-group-flush mb-3">
            @foreach($selected->slice(0, ceil($selected->count()/2)) as $key)
              <li class="list-group-item">
                {{ $riskMap[$key] ?? ucfirst(str_replace('_',' ',$key)) }}
                @if($specify->has("{$key}_specify"))
                  — <em>{{ $specify["{$key}_specify"] }}</em>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
        <div class="col-md-6">
          <ul class="list-group list-group-flush mb-3">
            @foreach($selected->slice(ceil($selected->count()/2)) as $key)
              <li class="list-group-item">
                {{ $riskMap[$key] ?? ucfirst(str_replace('_',' ',$key)) }}
                @if($specify->has("{$key}_specify"))
                  — <em>{{ $specify["{$key}_specify"] }}</em>
                @endif
              </li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

  </div>

  <div class="card-footer bg-white text-end">
    <a href="{{ route('high-risk-opd-forms.index') }}" class="btn btn-outline-secondary me-2">
      <i class="bi bi-chevron-left"></i> Back to list
    </a>
    <a href="{{ route('high-risk-opd-forms.edit', $submission) }}" class="btn btn-primary">
      <i class="bi bi-pencil-square"></i> Edit
    </a>
  </div>
</div>
@endsection
