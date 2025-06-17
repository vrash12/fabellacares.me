{{-- resources/views/trends/index.blade.php --}}
@extends('layouts.admin')

@section('content')
<div class="container-xl">

  {{-- ───────────────────────────────────────── Context Ribbon --}}
  <div class="alert alert-info d-flex align-items-center mb-4 shadow-sm">
    <i class="bi bi-graph-up-arrow me-2 fs-4"></i>
    <div class="flex-grow-1">
      <strong>Patient Trend Analysis</strong> helps you anticipate OPD load,
      optimise staffing, and minimise patient waiting time.
      Choose a date-range, department, and forecasting model – then click
      <em>Generate New</em> for an updated prediction.
    </div>
  </div>

  {{-- ───────────────────────────────────────── Filter Bar --}}
  {{-- (same as before – fully inlined) --}}
  @php
    /* keep { $from, $to, $department, $model, $steps, $windowSize, $queues } from
       your controller exactly as before */
  @endphp
  <form class="row gy-2 gx-3 align-items-end mb-4" method="GET" action="{{ route('trends.index') }}">
    <div class="col-lg-2 col-md-3">
      <label class="form-label mb-0">From
        <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
           title="Earliest date of historical data to include"></i>
      </label>
      <input type="date" name="from" class="form-control"
             value="{{ $from }}" max="{{ now()->format('Y-m-d') }}">
    </div>

    <div class="col-lg-2 col-md-3">
      <label class="form-label mb-0">To
        <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
           title="Latest date of historical data to include"></i>
      </label>
      <input type="date" name="to" class="form-control"
             value="{{ $to }}" max="{{ now()->format('Y-m-d') }}">
    </div>

    <div class="col-lg-3 col-md-4">
      <label class="form-label mb-0">Department
        <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
           title="Filter visits belonging to one department / queue"></i>
      </label>
      <select name="department" class="form-select">
        <option value="">All Departments</option>
        @foreach($queues as $q)
          <option value="{{ $q->id }}" {{ (string)$q->id===(string)$department?'selected':'' }}>
            {{ $q->name }}
          </option>
        @endforeach
      </select>
    </div>

    <div class="col-lg-2 col-md-4">
      <label class="form-label mb-0">Model
        <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
           title="ARIMA = statistical · LSTM = neural-net · Ensemble = average"></i>
      </label>
      <select name="model" class="form-select">
        <option value="ensemble" {{ $model==='ensemble'?'selected':'' }}>Ensemble</option>
        <option value="arima"    {{ $model==='arima'   ?'selected':'' }}>ARIMA</option>
        <option value="lstm"     {{ $model==='lstm'    ?'selected':'' }}>LSTM</option>
      </select>
    </div>

    <div class="col-lg-1 col-md-2">
      <label class="form-label mb-0">Days
        <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
           title="How many days into the future to predict"></i>
      </label>
      <select name="steps" class="form-select">
        <option value="7"  {{ $steps==7  ?'selected':'' }}>7</option>
        <option value="14" {{ $steps==14 ?'selected':'' }}>14</option>
        <option value="30" {{ $steps==30 ?'selected':'' }}>30</option>
      </select>
    </div>

    {{-- LSTM window selector --}}
    <div class="col-lg-1 col-md-2" id="lstm-window-col">
      <label class="form-label mb-0">Window
        <i class="bi bi-question-circle text-muted" data-bs-toggle="tooltip"
           title="Number of past days LSTM looks at as one sample"></i>
      </label>
      <select name="window_size" class="form-select">
        <option value="14" {{ $windowSize==14?'selected':'' }}>14</option>
        <option value="21" {{ $windowSize==21?'selected':'' }}>21</option>
        <option value="30" {{ $windowSize==30?'selected':'' }}>30</option>
      </select>
    </div>

    <div class="col-auto text-end mt-2 mt-md-0">
      <button class="btn btn-primary me-2"><i class="bi bi-funnel"></i> Apply</button>

      <button
        formaction="{{ route('trends.request') }}"
        formmethod="POST"
        class="btn btn-outline-secondary position-relative">
        @csrf
        <i class="bi bi-arrow-repeat"></i> Generate New
        @if(! empty($trend?->cached))
          <span class="position-absolute top-0 start-100 translate-middle badge bg-warning text-dark">
            cached
          </span>
        @endif
      </button>
    </div>
  </form>

  <div class="row">
    {{-- ───────────────────────────────── Main Column --}}
    <div class="col-lg-9">

      {{-- Summary cards --}}
      @php
        $hist = $trend['historical_mean'] ?? null;
        $next = $trend[$model]['values'][0] ?? null;
        $delta = ($hist && $next) ? $next - $hist : null;
      @endphp
      <div class="row g-3 mb-4">
        <div class="col-md-6">
          <div class="card text-center shadow-sm h-100">
            <div class="card-body"><h6 class="text-muted mb-1">Historical Mean</h6>
              <h2 class="fw-bold mb-0">{{ $hist ?? '—' }}</h2></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card text-center shadow-sm h-100">
            <div class="card-body"><h6 class="text-muted mb-1">{{ strtoupper($model) }} Next Day</h6>
              <h2 class="fw-bold mb-0">
                {{ $next ?? '—' }}
                @if($delta!==null)
                  <span class="badge {{ $delta>=0?'bg-danger':'bg-success' }} ms-2">
                    {{ $delta>=0?'+':'' }}{{ number_format($delta,0) }}
                  </span>
                @endif
              </h2>
            </div>
          </div>
        </div>
      </div>

      {{-- ------------------------------ Guidance rules --}}
      @php
        $dept = optional($queues->firstWhere('id',$department))->name ?? null;

        /** ARIMA rules **/
        $rulesArima = [
          'all' => [
            [20,'gte','Increase staffing (2 extra doctors)','bg-danger text-white'],
            [ 5,'gte','Monitor queue & prepare relief staff','bg-warning'],
            [-5,'lte','Normal staffing adequate','bg-success text-white'],
          ],
          'OB' => [
            [15,'gte','Add 1 OB resident to OPD','bg-danger text-white'],
            [ 5,'gte','Prepare standby staff','bg-warning'],
            [-5,'lte','Current staffing OK','bg-success text-white'],
          ],
        ];

        /** LSTM rules (percentage-based) **/
        $rulesLstm = [
          'all' => [
            [0.30,'abs','High volatility – double-check resources','bg-danger text-white'],
            [0.15,'abs','Moderate change – flag for review','bg-warning'],
            [0.00,'abs','Stable – proceed with usual roster','bg-success text-white'],
          ],
        ];

        /** Ensemble rules – simply reuse ARIMA style (absolute delta) **/
        $rulesEns = [
          'all' => [
            [15,'abs','Large swing – review both ARIMA & LSTM','bg-danger text-white'],
            [ 8,'abs','Medium swing – watch tomorrow','bg-warning'],
            [ 0,'abs','Forecasts in agreement – normal ops','bg-success text-white'],
          ],
        ];
      @endphp

      {{-- ARIMA guidance (if model == arima or ensemble) --}}
      @if(in_array($model,['arima','ensemble']) && $hist && $next)
        <x-trends.guidance
          title="ARIMA Guidelines"
          :rules="$rulesArima[$dept] ?? $rulesArima['all']"
          :delta="$delta"
        />
      @endif

      {{-- LSTM guidance (if model == lstm or ensemble) --}}
      @if(in_array($model,['lstm','ensemble']) && $hist && $next)
        <x-trends.guidance
          title="LSTM Guidelines"
          subtitle="(based on % change vs historical mean)"
          :rules="$rulesLstm['all']"
          :delta="$delta / max(1,$hist)"
          percent="true"
        />
      @endif

      {{-- Ensemble guidance (only when model == ensemble) --}}
      @if($model==='ensemble' && $hist && $next)
        <x-trends.guidance
          title="Ensemble Guidelines"
          :rules="$rulesEns['all']"
          :delta="$delta"
        />
      @endif

      {{-- ------------------------------ Model-specific chart partials --}}
      @isset($trend)
        @switch($model)
          @case('arima')
            @includeWhen(isset($trend['arima']),
              'trends.partials.arima',
              ['from'=>$from,'to'=>$to,'steps'=>$steps,'trend'=>$trend])
            @break

          @case('lstm')
            @includeWhen(isset($trend['lstm']),
              'trends.partials.lstm',
              ['from'=>$from,'to'=>$to,'steps'=>$steps,'windowSize'=>$windowSize,'trend'=>$trend])
            @break

          @case('ensemble')
            @includeWhen(isset($trend['ensemble']),
              'trends.partials.ensemble',
              ['from'=>$from,'to'=>$to,'steps'=>$steps,'windowSize'=>$windowSize,'trend'=>$trend])
            @break
        @endswitch
      @endisset
    </div>

    {{-- ───────────────────────────────── Side Help Column --}}
    <div class="col-lg-3">
      <div class="card shadow-sm sticky-top" style="top:80px;">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
          <span><i class="bi bi-lightbulb me-1 text-warning"></i> Quick Guide</span>
          <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="collapse"
                  data-bs-target="#helpCollapse"><i class="bi bi-chevron-down"></i></button>
        </div>
        <div class="collapse show" id="helpCollapse">
          <div class="card-body small">
            <h6 class="fw-bold text-primary mb-1">ARIMA</h6>
            <p class="mb-1">Classic statistical model; great for seasonal trends.</p>
            <h6 class="fw-bold text-success mt-3 mb-1">LSTM</h6>
            <p class="mb-1">Neural network catches complex patterns & sudden spikes.</p>
            <h6 class="fw-bold text-info mt-3 mb-1">Ensemble</h6>
            <p class="mb-0">Averaging ARIMA & LSTM typically gives the safest forecast.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ───────────────────────────────── Export Buttons --}}
  <div class="d-flex justify-content-end mt-4">
    <div class="btn-group">
      <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown">
        <i class="bi bi-download me-1"></i> Export
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="{{ route('trends.excel', [
              'from'=>$from,'to'=>$to,'model'=>$model,'department'=>$department,
              'steps'=>$steps,'window_size'=>$windowSize ]) }}">
              <i class="bi bi-file-earmark-spreadsheet me-1 text-success"></i> Excel
            </a></li>
        <li><a class="dropdown-item" href="{{ route('trends.pdf', [
              'from'=>$from,'to'=>$to,'model'=>$model,'department'=>$department,
              'steps'=>$steps,'window_size'=>$windowSize ]) }}">
              <i class="bi bi-file-earmark-pdf me-1 text-danger"></i> PDF
            </a></li>
      </ul>
    </div>
  </div>

</div> {{-- /container --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function () {
  const modelSel = document.querySelector('select[name=model]');
  const winCol   = document.getElementById('lstm-window-col');
  const toggle   = () => winCol.classList.toggle('d-none', modelSel.value !== 'lstm');
  toggle(); modelSel.addEventListener('change', toggle);
  new bootstrap.Tooltip(document.body, { selector:'[data-bs-toggle="tooltip"]' });
})();
</script>
@endpush
