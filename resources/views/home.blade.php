@extends('layouts.admin')

@section('content')

{{-- ─── Include Chart.js & DataLabels plugin ───────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

{{-- === KPI CARDS ======================================================= --}}
<div class="row g-4 mb-5">
  {{-- 1. Today’s Visits --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-primary h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">Today's Visits</h6>
        <h2 class="mb-0">{{ $todayVisits }}</h2>
      </div>
    </div>
  </div>

  {{-- 2. New Patients Today --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-success h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">New Patients Today</h6>
        <h2 class="mb-0">{{ $newPatients }}</h2>
      </div>
    </div>
  </div>

  {{-- 3. Patients Served --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-info h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">Patients Served</h6>
        <h2 class="mb-0">{{ $patientsServed }}</h2>
      </div>
    </div>
  </div>

  {{-- 4. Current Queue --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-warning h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">Current Queue</h6>
        <h2 class="mb-0">{{ $currentQueue }}</h2>
      </div>
    </div>
  </div>
</div>

{{-- === VISITS-BY-DEPARTMENT (Today + 2 previous days) =============== --}}
<div class="row g-4 mb-5">
  {{-- Today --}}
  <div class="col-lg-4 col-md-6">
    <div class="card p-4 h-100">
      <h5 class="card-title mb-3">Visits by Department – Today</h5>
      <div style="position:relative;height:300px;">
        <canvas id="deptChartToday"></canvas>
      </div>
    </div>
  </div>

  {{-- Yesterday --}}
  <div class="col-lg-4 col-md-6">
    <div class="card p-4 h-100">
      <h5 class="card-title mb-3">Visits by Department – Yesterday</h5>
      <div style="position:relative;height:300px;">
        <canvas id="deptChartYest"></canvas>
      </div>
    </div>
  </div>

  {{-- Day-before-yesterday --}}
  <div class="col-lg-4 col-md-6">
    <div class="card p-4 h-100">
      <h5 class="card-title mb-3">Visits by Department – 2 Days Ago</h5>
      <div style="position:relative;height:300px;">
        <canvas id="deptChart2"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- === Chart Initialization =========================================== --}}
<script>
(() => {
  // Data from controller
  const todayData   = @json($deptStats);
  const yestData    = @json($deptYesterday);
  const twoDaysData = @json($deptDayBefore);

  // Factory for a vertical bar chart with always-visible labels
  function makeBarConfig(stats) {
    return {
      type: 'bar',
      data: {
        labels: stats.map(s => s.name),
        datasets: [{
          data: stats.map(s => s.count),
          backgroundColor: 'rgba(54,162,235,0.7)',
          borderRadius: 4
        }]
      },
      plugins: [ChartDataLabels],
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: { precision: 0 }
          }
        },
        plugins: {
          legend: { display: false },
          tooltip: { enabled: true },
          datalabels: {
            anchor: 'end',
            align : 'end',
            formatter: value => value,
            font: { weight: 'bold', size: 12 }
          }
        }
      }
    };
  }

  // Render all three charts
  new Chart(document.getElementById('deptChartToday'), makeBarConfig(todayData));
  new Chart(document.getElementById('deptChartYest'),  makeBarConfig(yestData));
  new Chart(document.getElementById('deptChart2'),     makeBarConfig(twoDaysData));
})();
</script>

@endsection
