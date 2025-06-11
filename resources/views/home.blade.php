@extends('layouts.admin')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

  {{-- 3. Average Wait Time (min) --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-info h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">Avg&nbsp;Wait&nbsp;(min)</h6>
        <h2 class="mb-0">{{ $avgWait }}</h2>
      </div>
    </div>
  </div>

  {{-- 4. Current Queue Length --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-warning h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">Current Queue</h6>
        <h2 class="mb-0">{{ $currentQueue }}</h2>
      </div>
    </div>
  </div>

  {{-- 5. High-Risk OPD Forms Today --}}
  <div class="col-md-4 col-lg-3">
    <div class="card text-white bg-danger h-100">
      <div class="card-body">
        <h6 class="card-title mb-2">High-Risk Forms</h6>
        <h2 class="mb-0">{{ $highRiskToday }}</h2>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-lg-4 col-md-6 mx-auto">
    <div class="card p-4">
      <h5 class="card-title mb-3">Visits by Department (Today)</h5>
      {{-- fixed-height wrapper --}}
      <div style="position: relative; height: 300px; width: 100%;">
        <canvas id="deptChart"></canvas>
      </div>
    </div>
  </div>
</div>

<script>
(() => {
  const stats = @json($deptStats);
  const labels = stats.map(s => s.name);
  const data   = stats.map(s => s.count);
  const ids    = stats.map(s => s.id);

  new Chart(document.getElementById('deptChart'), {
    type: 'doughnut',
    data: {
      labels,
      datasets: [{
        data,
        backgroundColor: [
          'rgba(54,162,235,0.6)',
          'rgba(255,99,132,0.6)',
          'rgba(255,206,86,0.6)',
          'rgba(75,192,192,0.6)',
          'rgba(153,102,255,0.6)',
          'rgba(255,159,64,0.6)',
        ]
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,      // allow fixed-height container
      cutout: '60%',                   // bigger hole, thinner ring
      plugins: {
        legend: { position: 'bottom' },
        tooltip: { enabled: true }
      },
      onClick: (evt, items) => {
        if (!items.length) return;
        const idx = items[0].index;
        window.location.href = `/queue/${ids[idx]}`;
      }
    }
  });
})();
</script>
@endsection
