{{-- resources/views/queue/history.blade.php --}}
@extends('layouts.admin')

@push('styles')
<style>
:root {
  --primary: #00b467;
  --primary-dark: #008a4f;
  --primary-light: #00d477;
  --accent: #00ff7f;
  --surface: #f8f9fa;
  --surface-dark: #e9ecef;
  --text: #212529;
  --text-muted: #6c757d;
  --border: #dee2e6;
  --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  --shadow-lg: 0 8px 25px rgba(0, 0, 0, 0.15);
  --border-radius: 12px;
  --transition: all 0.3s ease;
}

/* Enhanced Page Header */
.page-header {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  padding: 2rem;
  border-radius: var(--border-radius);
  margin-bottom: 2rem;
  box-shadow: var(--shadow-lg);
  position: relative;
  overflow: hidden;
}

.page-header::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
  animation: shimmer 3s infinite;
}

@keyframes shimmer {
  0% { left: -100%; }
  100% { left: 100%; }
}

.page-header h2 {
  font-size: 2.5rem;
  font-weight: 700;
  margin: 0;
  text-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.page-header .subtitle {
  opacity: 0.9;
  font-size: 1.1rem;
  margin-top: 0.5rem;
}

/* Enhanced Back Button */
.btn-back {
  background: rgba(255,255,255,0.15);
  color: white;
  border: 2px solid rgba(255,255,255,0.2);
  padding: 0.75rem 1.5rem;
  border-radius: 50px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  transition: var(--transition);
  backdrop-filter: blur(10px);
}

.btn-back:hover {
  background: rgba(255,255,255,0.25);
  border-color: rgba(255,255,255,0.4);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

/* Enhanced Filter Card */
.filter-card {
  background: white;
  border-radius: var(--border-radius);
  padding: 2rem;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
  margin-bottom: 2rem;
  position: relative;
}

.filter-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
  border-radius: var(--border-radius) var(--border-radius) 0 0;
}

.filter-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: var(--text);
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.form-label {
  font-weight: 600;
  color: var(--text);
  margin-bottom: 0.5rem;
}

.form-select, .form-control {
  border: 2px solid var(--border);
  border-radius: 8px;
  padding: 0.75rem 1rem;
  transition: var(--transition);
  background: white;
}

.form-select:focus, .form-control:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 3px rgba(0, 180, 103, 0.1);
}

.btn-primary {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  border: none;
  padding: 0.75rem 2rem;
  border-radius: 50px;
  font-weight: 600;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.btn-primary:hover {
  background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary) 100%);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
}

.btn-link {
  color: var(--text-muted);
  text-decoration: none;
  padding: 0.75rem 1.5rem;
  border-radius: 50px;
  transition: var(--transition);
}

.btn-link:hover {
  color: var(--primary);
  background: var(--surface);
}

/* Enhanced Table */
.table-card {
  background: white;
  border-radius: var(--border-radius);
  overflow: hidden;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
}

.table {
  margin: 0;
  border-collapse: separate;
  border-spacing: 0;
}

.table thead th {
  background: linear-gradient(135deg, var(--surface) 0%, var(--surface-dark) 100%);
  color: var(--text);
  font-weight: 700;
  padding: 1.25rem 1rem;
  border: none;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  font-size: 0.85rem;
  position: sticky;
  top: 0;
  z-index: 10;
}

.table tbody tr {
  transition: var(--transition);
  border-bottom: 1px solid var(--border);
}

.table tbody tr:hover {
  background: rgba(0, 180, 103, 0.05);
  transform: scale(1.01);
}

.table tbody tr:last-child {
  border-bottom: none;
}

.table tbody td {
  padding: 1.25rem 1rem;
  vertical-align: middle;
  border: none;
}

/* Enhanced Badges */
.badge {
  padding: 0.5rem 1rem;
  border-radius: 50px;
  font-weight: 600;
  font-size: 0.85rem;
  letter-spacing: 0.5px;
  text-transform: uppercase;
}

.badge.bg-success {
  background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
  box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.badge.bg-warning {
  background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
  color: #000 !important;
  box-shadow: 0 2px 4px rgba(255, 193, 7, 0.3);
}

/* Enhanced Token Code */
.token-code {
  font-family: 'JetBrains Mono', 'Courier New', monospace;
  font-weight: 700;
  font-size: 1.1rem;
  color: var(--primary);
  background: rgba(0, 180, 103, 0.1);
  padding: 0.5rem 0.75rem;
  border-radius: 6px;
  border: 1px solid rgba(0, 180, 103, 0.2);
}

/* Enhanced Pagination */
.custom-pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 0.5rem;
  margin-top: 2rem;
  padding: 1.5rem;
}

.page-btn {
  min-width: 44px;
  height: 44px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  padding: 0.5rem 1rem;
  border: 2px solid var(--border);
  background: white;
  color: var(--text);
  text-decoration: none;
  border-radius: 50px;
  font-weight: 600;
  transition: var(--transition);
  box-shadow: var(--shadow);
}

.page-btn:hover:not(.disabled) {
  background: var(--primary);
  color: white;
  border-color: var(--primary);
  transform: translateY(-2px);
  box-shadow: var(--shadow-lg);
  text-decoration: none;
}

.page-btn.active {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  color: white;
  border-color: var(--primary);
  box-shadow: var(--shadow-lg);
}

.page-btn.disabled {
  color: var(--text-muted);
  background: var(--surface);
  border-color: var(--border);
  cursor: not-allowed;
  opacity: 0.6;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 4rem 2rem;
  color: var(--text-muted);
}

.empty-state i {
  font-size: 4rem;
  margin-bottom: 1rem;
  opacity: 0.5;
}

.empty-state h4 {
  margin-bottom: 0.5rem;
  color: var(--text);
}

/* Stats Cards */
.stats-row {
  margin-bottom: 2rem;
}

.stat-card {
  background: white;
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--shadow);
  border: 1px solid var(--border);
  transition: var(--transition);
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 3px;
  background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
}

.stat-card:hover {
  transform: translateY(-4px);
  box-shadow: var(--shadow-lg);
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--primary);
  margin: 0;
}

.stat-label {
  color: var(--text-muted);
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin: 0;
}

/* Responsive Design */
@media (max-width: 768px) {
  .page-header {
    padding: 1.5rem;
    text-align: center;
  }
  
  .page-header h2 {
    font-size: 2rem;
  }
  
  .filter-card {
    padding: 1.5rem;
  }
  
  .table-responsive {
    border-radius: var(--border-radius);
    overflow: hidden;
  }
  
  .custom-pagination {
    flex-wrap: wrap;
    gap: 0.25rem;
  }
  
  .page-btn {
    min-width: 40px;
    height: 40px;
    font-size: 0.9rem;
  }
}

/* Animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.fade-in-up {
  animation: fadeInUp 0.6s ease-out;
}

.btn i {
  display: inline-block !important;
  width: 1em !important;
  height: 1em !important;
  font-size: 1em !important;
  line-height: 1 !important;
}
</style>
@endpush

@section('content')
  {{-- Enhanced Header --}}
  <div class="page-header fade-in-up">
    <div class="d-flex justify-content-between align-items-center">
      <div>
        <h2><i class="bi bi-clock-history me-3"></i>Queue History</h2>
        <div class="subtitle">Track and analyze your queue performance</div>
      </div>
      <a href="{{ route('queue.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i>
        Back to Queues
      </a>
    </div>
  </div>

  {{-- Stats Row --}}
  <div class="row stats-row fade-in-up">
    <div class="col-md-3 mb-3">
      <div class="stat-card">
        <div class="stat-number">{{ $tokens->total() }}</div>
        <div class="stat-label">Total Records</div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="stat-card">
        <div class="stat-number">{{ $tokens->where('served_at', '!=', null)->count() }}</div>
        <div class="stat-label">Served</div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="stat-card">
        <div class="stat-number">{{ $tokens->where('served_at', null)->count() }}</div>
        <div class="stat-label">Pending</div>
      </div>
    </div>
    <div class="col-md-3 mb-3">
      <div class="stat-card">
        <div class="stat-number">{{ $queues->count() }}</div>
        <div class="stat-label">Active Queues</div>
      </div>
    </div>
  </div>

  {{-- Enhanced Filter Form --}}
  <div class="filter-card fade-in-up">
    <div class="filter-title">
      <i class="bi bi-funnel"></i>
      Filter Results
    </div>
    <form class="row gy-3 gx-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Queue</label>
       <select class="form-select" name="queue_id">
  <option value="">All Queues</option>
  @foreach($queues as $q)
    <option value="{{ $q->id }}" 
            {{ request('queue_id') == $q->id ? 'selected' : '' }}>
      {{ $q->name }}
    </option>
  @endforeach
</select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="">All Status</option>
          <option value="pending" {{ request('status')=='pending' ? 'selected' : '' }}>
            Pending
          </option>
          <option value="served" {{ request('status')=='served' ? 'selected' : '' }}>
            Served
          </option>
        </select>
      </div>
      <div class="col-md-4">
        <div class="d-flex gap-2">
          <button type="submit" class="btn btn-primary flex-fill">
            <i class="bi bi-search me-2"></i>Filter
          </button>
          <a href="{{ route('queue.history') }}" class="btn btn-link">
            <i class="bi bi-arrow-clockwise"></i>
          </a>
        </div>
      </div>
    </form>
  </div>

  {{-- Enhanced Results Table --}}
  <div class="table-card fade-in-up">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th><i class="bi bi-ticket-perforated me-2"></i>Token</th>
            <th><i class="bi bi-list-ul me-2"></i>Queue</th>
            <th><i class="bi bi-clock me-2"></i>Requested At</th>
            <th><i class="bi bi-check-circle me-2"></i>Served At</th>
            <th><i class="bi bi-flag me-2"></i>Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tokens as $t)
            <tr>
              <td>
                <span class="token-code">{{ $t->code }}</span>
              </td>
              <td>
                <strong>{{ optional($t->queue)->name }}</strong>
              </td>
              <td>
                <div>{{ $t->created_at->format('M d, Y') }}</div>
                <small class="text-muted">{{ $t->created_at->format('H:i:s') }}</small>
              </td>
              <td>
                @if($t->served_at)
                  <div>{{ $t->served_at->format('M d, Y') }}</div>
                  <small class="text-muted">{{ $t->served_at->format('H:i:s') }}</small>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                <span class="badge {{ $t->served_at ? 'bg-success' : 'bg-warning text-dark' }}">
                  <i class="bi {{ $t->served_at ? 'bi-check-circle' : 'bi-clock' }} me-1"></i>
                  {{ $t->served_at ? 'Served' : 'Pending' }}
                </span>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5">
                <div class="empty-state">
                  <i class="bi bi-inbox"></i>
                  <h4>No Records Found</h4>
                  <p>Try adjusting your filters or check back later</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Enhanced Pagination --}}
    @if($tokens->hasPages())
      <div class="custom-pagination">
        {{-- Previous --}}
        @if($tokens->onFirstPage())
          <span class="page-btn disabled">
            <i class="bi bi-chevron-left"></i>
          </span>
        @else
          <a href="{{ $tokens->withQueryString()->previousPageUrl() }}" class="page-btn">
            <i class="bi bi-chevron-left"></i>
          </a>
        @endif

        {{-- Page Numbers --}}
        @php
          $start = max(1, $tokens->currentPage() - 2);
          $end   = min($tokens->lastPage(), $tokens->currentPage() + 2);
        @endphp

        @if($start > 1)
          <a href="{{ $tokens->withQueryString()->url(1) }}" class="page-btn">1</a>
          @if($start > 2)
            <span class="page-btn disabled">…</span>
          @endif
        @endif

        @for($i = $start; $i <= $end; $i++)
          @if($i == $tokens->currentPage())
            <span class="page-btn active">{{ $i }}</span>
          @else
            <a href="{{ $tokens->withQueryString()->url($i) }}" class="page-btn">{{ $i }}</a>
          @endif
        @endfor

        @if($end < $tokens->lastPage())
          @if($end < $tokens->lastPage() - 1)
            <span class="page-btn disabled">…</span>
          @endif
          <a href="{{ $tokens->withQueryString()->url($tokens->lastPage()) }}" class="page-btn">{{ $tokens->lastPage() }}</a>
        @endif

        {{-- Next --}}
        @if($tokens->hasMorePages())
          <a href="{{ $tokens->withQueryString()->nextPageUrl() }}" class="page-btn">
            <i class="bi bi-chevron-right"></i>
          </a>
        @else
          <span class="page-btn disabled">
            <i class="bi bi-chevron-right"></i>
          </span>
        @endif
      </div>

      {{-- Pagination Info --}}
      <div class="text-center text-muted mt-3 pb-3">
        <small>
          <i class="bi bi-info-circle me-1"></i>
          Showing {{ $tokens->firstItem() ?? 0 }} to {{ $tokens->lastItem() ?? 0 }} of {{ $tokens->total() }} results
        </small>
      </div>
    @endif
  </div>
@endsection