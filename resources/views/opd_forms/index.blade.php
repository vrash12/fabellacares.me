{{-- resources/views/opd_forms/index.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<style>
    /* ===== Enhanced OPD Forms Index Styles ===== */
    :root {
        --primary-green: #00b467;
        --secondary-teal: #0e4749;
        --accent-blue: #1e90ff;
        --success-green: #28a745;
        --danger-red: #dc3545;
        --light-gray: #f8f9fa;
        --border-color: #e9ecef;
        --text-muted: #6c757d;
        --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        --border-radius: 0.75rem;
        --transition: all 0.3s ease;
    }

    .page-container {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        padding: 1.5rem 0;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary-green) 0%, #008a52 100%);
        color: white;
        padding: 2rem;
        border-radius: var(--border-radius);
        margin-bottom: 2rem;
        box-shadow: var(--shadow-md);
        position: relative;
        overflow: hidden;
    }
    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 100%;
        background: rgba(255, 255, 255, 0.1);
        transform: skewX(-15deg);
        transform-origin: top;
    }
    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 2;
    }
    .header-title {
        margin: 0;
        font-size: 2.25rem;
        font-weight: 700;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    .header-subtitle {
        margin: 0.5rem 0 0 0;
        font-size: 1.1rem;
        opacity: 0.9;
        font-weight: 400;
    }
    .header-logo {
        background: white;
        padding: 0.75rem;
        border-radius: 50%;
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        width: 90px;
        height: 90px;
    }

    /* Enhanced Table Styles */
    .forms-table-container {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-sm);
        overflow: hidden;
        margin-top: 1rem;
    }
    .table-header {
        background: linear-gradient(135deg, var(--secondary-teal) 0%, #0a3d3f 100%);
        color: white;
        padding: 1.25rem 1.5rem;
        display: grid;
        grid-template-columns: 3fr 1fr 2fr 2fr;
        gap: 1rem;
        align-items: center;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .table-row {
        display: grid;
        grid-template-columns: 3fr 1fr 2fr 2fr;
        gap: 1rem;
        padding: 1.25rem 1.5rem;
        align-items: center;
        border-bottom: 1px solid var(--border-color);
        transition: var(--transition);
        position: relative;
    }
    .table-row:hover {
        background: #f8f9fa;
        transform: translateX(5px);
        box-shadow: inset 4px 0 0 var(--primary-green);
    }
    .table-row:last-child { border-bottom: none; }
    .form-name {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.05rem;
    }
    .form-number {
        background: var(--primary-green);
        color: white;
        padding: 0.4rem 0.8rem;
        border-radius: 2rem;
        font-weight: 600;
        font-size: 0.875rem;
        text-align: center;
    }
    .department-badge {
        background: #e9ecef;
        color: #495057;
        padding: 0.5rem 1rem;
        border-radius: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
    }
    .table-actions {
        display: flex;
        gap: 0.5rem;
        justify-content: flex-start;
    }
    .btn-action {
        padding: 0.4rem 0.8rem;
        border: none;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }
    .btn-view { background: var(--success-green); color: white; }
    .btn-new  { background: var(--accent-blue);  color: white; }
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-sm);
        color: white;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: var(--text-muted);
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }
    .empty-state h3 { margin-bottom: 0.5rem; color: #6c757d; }

    @media (max-width: 768px) {
        .header-content { flex-direction: column; gap: 1rem; text-align: center; }
        .header-title   { font-size: 1.75rem; }
        .table-header,
        .table-row { grid-template-columns: 1fr; text-align: center; }
    }
</style>
<div class="page-container">
  <div class="container-fluid">

    {{-- HEADER --}}
    <div class="page-header">
      <div class="header-content">
        <div>
          <h1 class="header-title">OPD Forms Management</h1>
          <p class="header-subtitle">Manage all OPD &amp; triage records efficiently</p>
        </div>
        <img src="{{ asset('images/fabella-logo.png') }}"
             alt="Fabella Logo"
             class="header-logo">
      </div>
    </div>

    {{-- TABLE --}}
    <div class="forms-table-container">
      <div class="table-header">
        <div><i class="fas fa-file-medical-alt me-2"></i>Form Name</div>
        <div><i class="fas fa-hashtag me-2"></i>Form No.</div>
        <div><i class="fas fa-building me-2"></i>Department</div>
        <div><i class="fas fa-cogs me-2"></i>Actions</div>
      </div>

      @forelse($forms as $form)
        @php $n = strtolower($form->name); @endphp
        <div class="table-row">
          <div class="form-name">{{ $form->name }}</div>
          <div><span class="form-number">{{ $form->form_no }}</span></div>
      <div>
  <span class="department-badge">
    {{ optional($form->queue)->name ?? $form->department }}
  </span>
</div>

          <div class="table-actions">

            @if(str_contains($n,'opd-ob'))
              <a href="{{ route('patients.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View OPD-OB
              </a>
              <a href="{{ route('ob-opd-forms.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New OPD-OB
              </a>

            @elseif(str_contains($n,'high risk'))
              <a href="{{ route('high-risk-opd-forms.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View High-Risk
              </a>
              <a href="{{ route('high-risk-opd-forms.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New High-Risk
              </a>

            @elseif(str_contains($n,'follow up') || str_contains($n,'follow-up'))
              <a href="{{ route('follow-up-opd-forms.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View Follow-Ups
              </a>
              <a href="{{ route('follow-up-opd-forms.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New Follow-Up
              </a>

            @elseif(str_contains($n,'consultation'))
              <a href="{{ route('consult.internal.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View Consultation
              </a>
              <a href="{{ route('consult.internal.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New Consultation
              </a>

            @elseif(str_contains($n,'internal medicine'))
              <a href="{{ route('triage.internal.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View IM Triage
              </a>
              <a href="{{ route('triage.internal.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New IM Triage
              </a>

            @elseif(str_contains($n,'ob-gyn') || str_contains($n,'ob gyn'))
              <a href="{{ route('triage.obgyn.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View OB-GYN
              </a>
              <a href="{{ route('triage.obgyn.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New OB-GYN
              </a>

            @elseif(str_contains($n,'teen'))
              <a href="{{ route('triage.teens.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View Teens
              </a>
              <a href="{{ route('triage.teens.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New Teen Record
              </a>

            @elseif(str_contains($n,'pedia'))
              <a href="{{ route('triage.pedia.index') }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View Pedia
              </a>
              <a href="{{ route('triage.pedia.create') }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> New Pedia
              </a>

            @else
              {{-- fallback to generic --}}
              <a href="{{ route('opd_forms.show', $form) }}" class="btn-action btn-view">
                <i class="fas fa-eye"></i> View Template
              </a>
              <a href="{{ route('opd_forms.create', $form) }}" class="btn-action btn-new">
                <i class="fas fa-plus"></i> Fill &amp; Submit
              </a>
            @endif

          </div>
        </div>
      @empty
        <div class="empty-state">
          <i class="fas fa-file-medical"></i>
          <h3>No Forms Available</h3>
          <p>Add a template in the OPD Forms module to get started.</p>
        </div>
      @endforelse

    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.table-row').forEach((row, i) => {
      row.style.opacity   = '0';
      row.style.transform = 'translateY(20px)';
      setTimeout(() => {
        row.style.transition = 'all .5s ease';
        row.style.opacity    = '1';
        row.style.transform  = 'translateY(0)';
      }, i * 100);
    });
  });
</script>
@endpush
