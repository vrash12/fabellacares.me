{{-- resources/views/reports/index.blade.php --}}
@extends('layouts.admin')

@push('styles')
  <style>
    :root {
      --primary-blue: #2563eb;
      --secondary-blue: #3b82f6;
      --light-blue: #dbeafe;
      --dark-blue: #1e40af;
      --accent-blue: #60a5fa;
      --gradient-blue: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      --gradient-light: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    }

    .page-header {
      background: var(--gradient-blue);
      color: white;
      padding: 2rem 0;
      margin: -1.5rem -1.5rem 2rem -1.5rem;
      border-radius: 0 0 20px 20px;
    }

    .filter-card {
      background: var(--gradient-light);
      border: 2px solid var(--light-blue);
      border-radius: 15px;
      backdrop-filter: blur(10px);
      box-shadow: 0 8px 32px rgba(37, 99, 235, 0.1);
    }

    .btn-primary-custom {
      background: var(--primary-blue);
      border: none;
      border-radius: 10px;
      padding: 0.6rem 1.5rem;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
      transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
      background: var(--dark-blue);
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(37, 99, 235, 0.4);
    }

    .btn-verify {
      background: linear-gradient(45deg, #f59e0b, #f97316);
      border: none;
      border-radius: 10px;
      color: white;
      font-weight: 600;
      box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
      transition: all 0.3s ease;
    }

    .btn-verify:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(245, 158, 11, 0.4);
    }

    .main-report-card {
      border: none;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(37, 99, 235, 0.1);
      overflow: hidden;
      background: white;
    }

    .main-report-card .card-header {
      background: var(--gradient-blue);
      color: white;
      padding: 1.5rem;
      border: none;
    }

    .chart-card {
      border: none;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(37, 99, 235, 0.08);
      transition: all 0.3s ease;
      background: white;
      overflow: hidden;
    }

    .chart-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 15px 35px rgba(37, 99, 235, 0.15);
    }

    .chart-card .card-header {
      background: var(--light-blue);
      color: var(--dark-blue);
      font-weight: 600;
      border: none;
      padding: 1.2rem 1.5rem;
    }

    .form-control-custom {
      border: 2px solid var(--light-blue);
      border-radius: 10px;
      padding: 0.6rem 1rem;
      transition: all 0.3s ease;
    }

    .form-control-custom:focus {
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
    }

    .table-custom {
      border-radius: 10px;
      overflow: hidden;
    }

    .table-custom thead th {
      background: var(--primary-blue);
      color: white;
      border: none;
      font-weight: 600;
      padding: 1rem;
    }

    .table-custom tbody tr {
      transition: all 0.2s ease;
    }

    .table-custom tbody tr:hover {
      background: var(--light-blue);
      transform: scale(1.01);
    }

    .export-buttons .btn {
      border-radius: 10px;
      font-weight: 600;
      padding: 0.6rem 1.2rem;
      margin: 0 0.2rem;
      transition: all 0.3s ease;
    }

    .export-buttons .btn:hover {
      transform: translateY(-2px);
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
    }

    .icon-wrapper {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 10px;
      margin-right: 0.5rem;
    }

    .pulse-animation {
      animation: pulse 2s infinite;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.05); }
      100% { transform: scale(1); }
    }

    .fade-in {
      animation: fadeIn 0.6s ease-in;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
@endpush

@section('content')
  <div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
      <div class="container">
        <div class="d-flex align-items-center">
          <div class="icon-wrapper">
            <i class="bi bi-graph-up fs-4"></i>
          </div>
          <div>
            <h1 class="mb-1">Healthcare Analytics Dashboard</h1>
            <p class="mb-0 opacity-75">Comprehensive patient visit & schedule reports</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card card mb-4 fade-in">
      <div class="card-body">
        <form class="row g-3 align-items-end" method="GET" action="{{ route('reports.index') }}">
          <div class="col-md-3">
            <label class="form-label text-primary fw-semibold mb-2">
              <i class="bi bi-calendar-event me-1"></i>From Date
            </label>
            <input
              type="date"
              name="from"
              value="{{ $dateFrom }}"
              class="form-control form-control-custom"
            >
          </div>
          <div class="col-md-3">
            <label class="form-label text-primary fw-semibold mb-2">
              <i class="bi bi-calendar-check me-1"></i>To Date
            </label>
            <input
              type="date"
              name="to"
              value="{{ $dateTo }}"
              class="form-control form-control-custom"
            >
          </div>
          <div class="col-md-3">
            <button type="submit" class="btn btn-primary-custom w-100">
              <i class="bi bi-funnel me-2"></i>Apply Filter
            </button>
          </div>
          <div class="col-md-3">
            <button id="verifyBtn" type="button" class="btn btn-verify w-100 pulse-animation">
              <i class="bi bi-shield-check me-2"></i>Verify Data
            </button>
          </div>
        </form>
      </div>
    </div>

<div class="main-report-card card mb-5 fade-in">
  <div class="card-header">
    <div class="d-flex align-items-center">
      <i class="bi bi-check-circle fs-4 me-3"></i>
      <div>
        <h4 class="mb-1">Finished Queues by Department</h4>
        <small class="opacity-75">Total served tokens per department</small>
      </div>
    </div>
  </div>
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-custom table-striped mb-0">
        <thead>
          <tr>
            <th><i class="bi bi-building me-2"></i>Department</th>
            <th><i class="bi bi-check2-all me-2"></i>Served Tokens</th>
          </tr>
        </thead>
        <tbody>
          @foreach(\App\Models\Queue::whereNotNull('parent_id')->orderBy('name')->get() as $department)
            <tr>
              <td class="fw-medium">{{ $department->name }}</td>
              <td>
                <span class="badge bg-success fs-6 px-3 py-2">
                  {{ $department->tokens()->whereNotNull('served_at')->count() }}
                </span>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  <a href="{{ route('reports.tokens.excel',['from'=>$dateFrom,'to'=>$dateTo]) }}"
   class="btn btn-success">
   <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Excel
</a>
<a href="{{ route('reports.tokens.pdf',['from'=>$dateFrom,'to'=>$dateTo]) }}"
   class="btn btn-danger">
   <i class="bi bi-file-earmark-pdf me-2"></i>Export PDF
</a>

</div>

    <!-- 2) NEW: Main Report: Daily Staff Schedules Overview -->
    <div class="main-report-card card mb-5 fade-in">
      <div class="card-header">
        <div class="d-flex align-items-center">
          <i class="bi bi-calendar-fill fs-4 me-3"></i>
          <div>
            <h4 class="mb-1">Daily Staff Schedules Overview</h4>
            <small class="opacity-75">Number of schedules assigned per day</small>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-custom table-striped mb-0">
            <thead>
              <tr>
                <th><i class="bi bi-calendar me-2"></i>Date</th>
                <th><i class="bi bi-clock me-2"></i>Total Schedules</th>
              </tr>
            </thead>
            <tbody>
              @forelse($scheduleStats as $s)
                <tr>
                  <td class="fw-medium">{{ \Carbon\Carbon::parse($s->day)->format('M d, Y') }}</td>
                  <td>
                    <span class="badge bg-secondary fs-6 px-3 py-2">{{ $s->total }}</span>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="2" class="text-center text-muted">
                    No schedules found in this date range.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
      <div class="card-footer bg-light d-flex justify-content-start">
        <div class="export-buttons">
          {{-- 
            If you also want to export schedules data separately,
            you could add new routes like reports.schedules.excel or PDF. 
            For now, we keep these buttons disabled or link them back to Visits export. 
          --}}
          <button class="btn btn-outline-secondary me-2" disabled>
            <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Schedules (Excel)
          </button>
          <button class="btn btn-outline-secondary" disabled>
            <i class="bi bi-file-earmark-pdf me-2"></i>Export Schedules (PDF)
          </button>
        </div>
      </div>
    </div>

    <!-- 3) Analytics Charts Section -->
    <div class="mb-4">
      <h3 class="text-primary mb-3">
        <i class="bi bi-pie-chart me-2"></i>Patient Demographics & Analytics
      </h3>
      <div class="stats-grid">
        <!-- Age Range Chart -->
        <div class="chart-card card fade-in">
          <div class="card-header">
            <i class="bi bi-person-lines-fill me-2"></i>Age Distribution
          </div>
          <div class="card-body">
            <canvas id="ageChart" style="height:300px"></canvas>
          </div>
        </div>

        <!-- Gender Chart -->
        <div class="chart-card card fade-in">
          <div class="card-header">
            <i class="bi bi-gender-ambiguous me-2"></i>Gender Distribution
          </div>
          <div class="card-body">
            <canvas id="genderChart" style="height:300px"></canvas>
          </div>
        </div>

        <!-- Blood Type Chart -->
        <div class="chart-card card fade-in">
          <div class="card-header">
            <i class="bi bi-droplet me-2"></i>Blood Type Analysis
          </div>
          <div class="card-body">
            <canvas id="bloodChart" style="height:300px"></canvas>
          </div>
        </div>

        <!-- Delivery Type Chart -->
        <div class="chart-card card fade-in">
          <div class="card-header">
            <i class="bi bi-heart-pulse me-2"></i>Delivery Methods
          </div>
          <div class="card-body">
            <canvas id="deliveryChart" style="height:300px"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
  <script>
    // 1) Reuse the same “blueTheme” from before
    const blueTheme = {
      primary: '#2563eb',
      secondary: '#3b82f6',
      accent: '#60a5fa',
      light: '#dbeafe',
      colors: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#1e40af', '#1d4ed8', '#2952e3']
    };

    // 2) Simple helper to turn a collection into labels/data arrays
    const extract = (coll, labelKey, dataKey) => ({
      labels: coll.map(r => r[labelKey]),
      data:   coll.map(r => r[dataKey])
    });

    // 3) “Verify Data” button logic (unchanged)
    document.getElementById('verifyBtn').addEventListener('click', function() {
      const btn = this;
      const originalText = btn.innerHTML;
      btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Verifying...';
      btn.disabled = true;
      btn.classList.remove('pulse-animation');

      fetch("{{ route('reports.verify') }}")
        .then(res => res.json())
        .then(data => {
          if (data.ok) {
            const toast = document.createElement('div');
            toast.className = 'toast show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
              <div class="toast-header bg-success text-white">
                <i class="bi bi-check-circle me-2"></i>
                <strong class="me-auto">Verification Complete</strong>
              </div>
              <div class="toast-body">
                All patient visits have complete notes. ✅
              </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
          } else {
            const toast = document.createElement('div');
            toast.className = 'toast show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
              <div class="toast-header bg-warning text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong class="me-auto">Missing Data Found</strong>
              </div>
              <div class="toast-body">
                ${data.missing} records are missing notes. Please review.
              </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 7000);
          }
        })
        .catch(() => {
          const toast = document.createElement('div');
          toast.className = 'toast show position-fixed top-0 end-0 m-3';
          toast.style.zIndex = '9999';
          toast.innerHTML = `
            <div class="toast-header bg-danger text-white">
              <i class="bi bi-x-circle me-2"></i>
              <strong class="me-auto">Verification Failed</strong>
            </div>
            <div class="toast-body">
              Unable to verify data. Please try again.
            </div>
          `;
          document.body.appendChild(toast);
          setTimeout(() => toast.remove(), 5000);
        })
        .finally(() => {
          btn.innerHTML = originalText;
          btn.disabled = false;
          btn.classList.add('pulse-animation');
        });
    });

    // 4) Chart.js → Age Distribution (bar chart)
    (() => {
      const stats = @json($ageStats);
      const { labels, data } = extract(stats, 'age_range', 'total');
      new Chart(document.getElementById('ageChart'), {
        type: 'bar',
        data: {
          labels,
          datasets: [{
            label: 'Patients',
            data,
            backgroundColor: blueTheme.colors,
            borderColor: blueTheme.primary,
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            y: {
              beginAtZero: true,
              ticks: { precision: 0 },
              grid: { color: 'rgba(37, 99, 235, 0.1)' }
            },
            x: { grid: { display: false } }
          }
        }
      });
    })();

    // 5) Chart.js → Gender (doughnut)
    (() => {
      const stats = @json($genderStats);
      const { labels, data } = extract(stats, 'sex', 'total');
      new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data,
            backgroundColor: blueTheme.colors,
            borderWidth: 3,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { padding: 20, usePointStyle: true }
            }
          }
        }
      });
    })();

    // 6) Chart.js → Blood Type (doughnut)
    (() => {
      const stats = @json($bloodStats);
      const { labels, data } = extract(stats, 'blood_type', 'total');
      new Chart(document.getElementById('bloodChart'), {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data,
            backgroundColor: blueTheme.colors,
            borderWidth: 3,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { padding: 20, usePointStyle: true }
            }
          }
        }
      });
    })();

    // 7) Chart.js → Delivery Type (doughnut)
    (() => {
      const stats = @json($deliveryStats);
      const { labels, data } = extract(stats, 'delivery_type', 'total');
      new Chart(document.getElementById('deliveryChart'), {
        type: 'doughnut',
        data: {
          labels,
          datasets: [{
            data,
            backgroundColor: blueTheme.colors,
            borderWidth: 3,
            borderColor: '#fff'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
              labels: { padding: 20, usePointStyle: true }
            }
          }
        }
      });
    })();

    // 8) Optional: Animate schedule‐chart or fade‐in on scroll
    const observerOptions = {
      threshold: 0.1,
      rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('fade-in');
        }
      });
    }, observerOptions);

    document.querySelectorAll('.chart-card').forEach(card => {
      observer.observe(card);
    });
  </script>
@endpush
