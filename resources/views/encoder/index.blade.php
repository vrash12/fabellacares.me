@extends('layouts.encoder')
@section('content')
<div class="container-fluid py-4">

  {{-- 1. KPI CARDS --}}
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-white bg-primary">
        <div class="card-body">
          <h6>Total Queues</h6>
          <h2>{{ $totalQueues }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-warning">
        <div class="card-body">
          <h6>Pending (Filtered)</h6>
          <h2>{{ $totalPending }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-danger">
        <div class="card-body">
          <h6>All Pending</h6>
          <h2>{{ $totalUnfiltered }}</h2>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card text-white bg-success">
        <div class="card-body">
          <h6>Total Tokens (All)</h6>
          <h2>{{ $totalQueues > 0 ? $queues->pluck('pending_count')->sum() : 0 }}</h2>
        </div>
      </div>
    </div>
  </div>

  <div class="row">
    {{-- Sidebar --}}
    <aside class="col-lg-3 mb-4">
      {{-- Quick Stats Card --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
          <h6 class="card-title text-primary mb-3">
            <i class="fas fa-chart-bar me-2"></i>Quick Stats
          </h6>
          <div class="row text-center">
            <div class="col-6">
              <div class="border-end">
                <h4 class="text-success mb-1">{{ $pending->total() }}</h4>
                <small class="text-muted">Total Pending</small>
              </div>
            </div>
            <div class="col-6">
              <h4 class="text-info mb-1">{{ $queues->count() }}</h4>
              <small class="text-muted">Departments</small>
            </div>
          </div>
        </div>
      </div>

      {{-- Departments Card --}}
      <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-transparent border-0 pb-0">
          <h6 class="text-primary mb-0">
            <i class="fas fa-building me-2"></i>Departments
          </h6>
        </div>
        <div class="card-body pt-2">
          <div class="list-group list-group-flush">
            <a href="{{ route('encoder.index') }}" 
               class="list-group-item list-group-item-action border-0 px-0 {{ !request('queue_id') ? 'active' : '' }}">
              <div class="d-flex justify-content-between align-items-center">
                <span><i class="fas fa-home me-2"></i>All Departments</span>
                <span class="badge bg-secondary rounded-pill">{{ $queues->sum('pending_count') }}</span>
              </div>
            </a>
            @foreach($queues as $q)
              <a href="{{ route('encoder.index', ['queue_id'=>$q->id]) }}"
                 class="list-group-item list-group-item-action border-0 px-0 {{ request('queue_id') == $q->id ? 'active' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                  <span>
                    <i class="fas fa-circle me-2" style="font-size: 8px; color: {{ $q->pending_count ? '#28a745' : '#6c757d' }}"></i>
                    {{ $q->name }}
                  </span>
                  @if($q->pending_count)
                    <span class="badge bg-primary rounded-pill">{{ $q->pending_count }}</span>
                  @endif
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      {{-- Enhanced Filters Card --}}
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-0 pb-0">
          <h6 class="text-primary mb-0">
            <i class="fas fa-filter me-2"></i>Filters
          </h6>
        </div>
        <div class="card-body pt-2">
          <form method="GET" action="{{ route('encoder.index') }}">
            <input type="hidden" name="queue_id" value="{{ request('queue_id') }}">

            <div class="mb-3">
              <label class="form-label text-sm">From Date</label>
              <input type="date" name="date_from" class="form-control form-control-sm"
                     value="{{ request('date_from') }}">
            </div>
            
            <div class="mb-3">
              <label class="form-label text-sm">To Date</label>
              <input type="date" name="date_to" class="form-control form-control-sm"
                     value="{{ request('date_to') }}">
            </div>
            
            <div class="d-grid gap-2">
              <button type="submit" class="btn btn-primary btn-sm">
                <i class="fas fa-search me-1"></i>Apply Filters
              </button>
              <a href="{{ route('encoder.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-times me-1"></i>Clear All
              </a>
            </div>
          </form>
        </div>
      </div>
    </aside>


    {{-- Main Content --}}
    <section class="col-lg-9">

      {{-- 2. CHARTS ROW --}}
      <div class="row mb-4">
        <div class="col-md-6">
          <div class="card p-3">
            <h6>Top 10 Queues by Pending</h6>
            <canvas id="deptChart" height="250"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card p-3">
            <h6>New Tokens (Last 7 Days)</h6>
            <canvas id="dailyChart" height="250"></canvas>
          </div>
        </div>
      </div>

      {{-- 3. SEARCH + TABLE --}}
      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Pending Tokens</h5>
          <input type="search" id="search-table" class="form-control w-auto"
                 placeholder="Search code…">
        </div>
        <div class="table-responsive">
          <table class="table mb-0">
            <thead class="thead-light">
              <tr>
                <th>Token Code</th>
                <th>Department</th>
                <th>Created At</th>
              </tr>
            </thead>
            <tbody>
              @forelse($pending as $token)
                <tr>
                  <td>{{ $token->code }}</td>
                  <td>{{ $token->queue->name }}</td>
                  <td>{{ $token->created_at->format('Y-m-d H:i') }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="3" class="text-center text-muted py-4">
                    No pending tokens found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="card-footer">
          {{ $pending
              ->appends(request()->query())
              ->links('pagination::bootstrap-5') }}
        </div>
      </div>

    </section>
  </div>
</div>

@push('scripts')
  {{-- client‐side table search --}}
  <script>
    document.getElementById('search-table').addEventListener('input', function(e){
      const term = e.target.value.toLowerCase();
      document.querySelectorAll('table tbody tr').forEach(row => {
        row.style.display = row.textContent.toLowerCase().includes(term)
          ? '' : 'none';
      });
    });
  </script>

  {{-- Chart.js --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      // Top 10 Queues Bar Chart
      new Chart(document.getElementById('deptChart'), {
        type: 'bar',
        data: {
          labels: @json($deptPending->pluck('name')),
          datasets: [{
            label: 'Pending',
            data: @json($deptPending->pluck('count')),
          }]
        },
        options: {
          indexAxis: 'y',
          responsive: true,
          scales: {
            x: { beginAtZero: true }
          }
        }
      });

      // Daily New Tokens Line Chart
      new Chart(document.getElementById('dailyChart'), {
        type: 'line',
        data: {
          labels: @json($dailyTokens->pluck('date')),
          datasets: [{
            label: 'New Tokens',
            data: @json($dailyTokens->pluck('count')),
            fill: false,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          scales: {
            y: { beginAtZero: true }
          }
        }
      });
    });
  </script>
@endpush
@endsection
