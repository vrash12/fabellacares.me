@extends('layouts.admin')

@section('content')
   <style>
    :root {
      --primary-green: #00d4aa;
      --primary-dark: #0a2e2a;
      --accent-emerald: #10b981;
      --accent-teal: #14b8a6;
      --light-bg: #f0fdfa;
      --glass-bg: rgba(255, 255, 255, 0.1);
      --glass-border: rgba(255, 255, 255, 0.2);
      --shadow-soft: 0 8px 32px rgba(0, 0, 0, 0.1);
      --shadow-hover: 0 20px 40px rgba(0, 212, 170, 0.25);
      --shadow-glow: 0 0 40px rgba(0, 212, 170, 0.3);
      --transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
      --blur: backdrop-filter: blur(20px);
    }

    /* ===== Base Styles ===== */
    body {
      background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
      min-height: 100vh;
      position: relative;
      overflow-x: hidden;
    }
    
    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: 
        radial-gradient(circle at 20% 80%, rgba(0, 212, 170, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(20, 184, 166, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(16, 185, 129, 0.05) 0%, transparent 50%);
      pointer-events: none;
      z-index: -1;
    }

    .container-fluid {
      padding: 2rem;
      position: relative;
      z-index: 1;
    }

    /* ===== Enhanced Header ===== */
    .q-header {
      background: linear-gradient(135deg, 
        rgba(0, 212, 170, 0.9) 0%, 
        rgba(20, 184, 166, 0.9) 50%, 
        rgba(16, 185, 129, 0.9) 100%);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border: 1px solid var(--glass-border);
      color: #fff;
      padding: 3rem 2.5rem;
      border-radius: 2rem;
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 3rem;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-soft);
    }

    .q-header::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 300px;
      height: 300px;
      background: conic-gradient(from 180deg, transparent, rgba(255,255,255,0.1), transparent);
      border-radius: 50%;
      animation: rotate 20s linear infinite;
    }

    .q-header::after {
      content: '';
      position: absolute;
      bottom: -30%;
      left: -30%;
      width: 200px;
      height: 200px;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      border-radius: 50%;
      animation: pulse 4s ease-in-out infinite;
    }

    .q-header h1 {
      font-size: 3rem;
      font-weight: 900;
      margin: 0;
      background: linear-gradient(45deg, #fff, #e6fffa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      text-shadow: 0 0 30px rgba(255, 255, 255, 0.5);
      position: relative;
      z-index: 2;
    }

    .header-controls {
      display: flex;
      align-items: center;
      gap: 1.5rem;
      position: relative;
      z-index: 2;
    }

    .header-controls .btn {
      background: rgba(255, 255, 255, 0.15);
      border: 1px solid rgba(255, 255, 255, 0.2);
      backdrop-filter: blur(10px);
      color: white;
      padding: 0.75rem 1.5rem;
      border-radius: 1rem;
      font-weight: 600;
      transition: var(--transition);
      text-decoration: none;
    }

    .header-controls .btn:hover {
      background: rgba(255, 255, 255, 0.25);
      transform: translateY(-2px);
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }

    .header-controls img {
      width: 60px;
      height: 60px;
      border: 3px solid rgba(255, 255, 255, 0.3);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      transition: var(--transition);
    }

    .header-controls img:hover {
      transform: scale(1.1) rotate(5deg);
      box-shadow: var(--shadow-glow);
    }

    /* ===== Category Headers ===== */
    .category-section {
      margin-bottom: 4rem;
    }

    .category-header {
      display: flex;
      align-items: center;
      margin-bottom: 2rem;
      padding: 2rem 2.5rem;
      border-radius: 1.5rem;
      position: relative;
      overflow: hidden;
      transition: var(--transition);
      cursor: pointer;
      text-decoration: none;
      color: inherit;
      backdrop-filter: blur(10px);
      border: 1px solid var(--glass-border);
    }

    .category-header.window-a {
      background: linear-gradient(135deg, 
        rgba(59, 130, 246, 0.9) 0%, 
        rgba(37, 99, 235, 0.9) 100%);
      box-shadow: 0 8px 32px rgba(59, 130, 246, 0.3);
    }

    .category-header.window-b {
      background: linear-gradient(135deg, 
        rgba(239, 68, 68, 0.9) 0%, 
        rgba(220, 38, 38, 0.9) 100%);
      box-shadow: 0 8px 32px rgba(239, 68, 68, 0.3);
    }

    .category-header::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: var(--transition);
    }

    .category-header:hover::before {
      left: 100%;
    }

    .category-header:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: var(--shadow-hover);
    }

    .category-header h3 {
      font-size: 2.2rem;
      font-weight: 800;
      margin: 0;
      display: flex;
      align-items: center;
      gap: 1rem;
      flex-grow: 1;
      color: white;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    }

    /* ===== Department Cards ===== */
    .departments-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
    }

    .dept-card {
      background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 1.5rem;
      color: #ffffff !important;
      padding: 2.5rem 2rem;
      font-weight: 700;
      text-align: center;
      transition: var(--transition);
      min-height: 250px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      text-decoration: none;
      position: relative;
      overflow: hidden;
      box-shadow: var(--shadow-soft);
    }

    .dept-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 2px;
      background: linear-gradient(90deg, var(--primary-green), var(--accent-teal));
      transform: scaleX(0);
      transition: var(--transition);
    }

    .dept-card::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: radial-gradient(circle, rgba(0, 212, 170, 0.2), transparent);
      border-radius: 50%;
      transform: translate(-50%, -50%);
      transition: var(--transition);
    }

    .dept-card:hover {
      transform: translateY(-12px) scale(1.05);
      box-shadow: var(--shadow-hover);
      background: linear-gradient(135deg, 
        rgba(0, 212, 170, 0.15) 0%, 
        rgba(15, 23, 42, 0.9) 50%, 
        rgba(30, 41, 59, 0.9) 100%);
    }

    .dept-card:hover::before {
      transform: scaleX(1);
    }

    .dept-card:hover::after {
      width: 200px;
      height: 200px;
    }

    .dept-name {
      font-size: 1.4rem;
      font-weight: 800;
      position: relative;
      z-index: 2;
      color: #ffffff !important;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.8);
    }

    /* ===== Statistics Panel ===== */
    .token-panel {
      background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.95) 100%);
      backdrop-filter: blur(20px);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 2rem;
      position: sticky;
      top: 2rem;
      overflow: hidden;
      box-shadow: var(--shadow-soft);
    }

    .token-box {
      padding: 2.5rem 2rem;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.1);
      transition: var(--transition);
      position: relative;
      overflow: hidden;
    }

    .token-box:last-child {
      border-bottom: none;
    }

    .token-box::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(0, 212, 170, 0.1), transparent);
      transition: var(--transition);
    }

    .token-box:hover::before {
      left: 100%;
    }

    .token-box:hover {
      background: linear-gradient(135deg, 
        rgba(0, 212, 170, 0.15) 0%, 
        rgba(15, 23, 42, 0.95) 100%);
      transform: scale(1.02);
    }

    .token-box h2 {
      font-size: 3rem;
      font-weight: 900;
      margin: 0 0 0.5rem;
      color: var(--primary-green) !important;
      text-shadow: 0 0 30px rgba(0, 212, 170, 0.6);
      position: relative;
      filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.3));
    }

    .token-box span {
      display: block;
      font-size: 1.1rem;
      font-weight: 600;
      color: #ffffff !important;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-top: 0.5rem;
      opacity: 0.9;
      text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    /* ===== Animations ===== */
    @keyframes rotate {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.3; transform: scale(1); }
      50% { opacity: 0.6; transform: scale(1.1); }
    }

    @keyframes fadeInUp {
      from { 
        opacity: 0; 
        transform: translateY(50px) scale(0.9); 
      }
      to { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
      }
    }

    @keyframes slideInLeft {
      from { 
        opacity: 0; 
        transform: translateX(-50px); 
      }
      to { 
        opacity: 1; 
        transform: translateX(0); 
      }
    }

    .dept-card, .token-box { 
      animation: fadeInUp 0.8s ease-out backwards;
    }

    .category-header {
      animation: slideInLeft 0.6s ease-out backwards;
    }

    .dept-card:nth-child(even) { animation-delay: 0.1s; }
    .dept-card:nth-child(3n) { animation-delay: 0.2s; }
    .token-box:nth-child(2) { animation-delay: 0.1s; }
    .token-box:nth-child(3) { animation-delay: 0.2s; }
    .token-box:nth-child(4) { animation-delay: 0.3s; }

    /* ===== Responsive Design ===== */
    @media (max-width: 1200px) {
      .departments-grid { 
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
        gap: 1.5rem;
      }
    }

    @media (max-width: 991px) {
      .departments-grid { 
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); 
      }
      .q-header { 
        flex-direction: column; 
        text-align: center; 
        gap: 1.5rem; 
        padding: 2rem 1.5rem;
      }
      .q-header h1 { 
        font-size: 2.5rem; 
      }
      .token-panel {
        margin-top: 2rem;
      }
    }

    @media (max-width: 768px) {
      .container-fluid { padding: 1rem; }
      .departments-grid { 
        grid-template-columns: 1fr; 
        gap: 1rem;
      }
      .q-header { 
        padding: 1.5rem 1rem; 
        margin-bottom: 2rem;
      }
      .q-header h1 { 
        font-size: 2rem; 
      }
      .category-header {
        padding: 1.5rem;
      }
      .category-header h3 {
        font-size: 1.8rem;
      }
      .dept-card {
        min-height: 200px;
        padding: 2rem 1.5rem;
      }
      .token-box {
        padding: 2rem 1.5rem;
      }
      .token-box h2 {
        font-size: 2.5rem;
      }
    }

    @media (max-width: 576px) {
      .q-header h1 { 
        font-size: 1.8rem; 
      }
      .header-controls {
        flex-direction: column;
        gap: 1rem;
      }
      .dept-card {
        min-height: 180px;
      }
    }

    /* ===== Custom Scrollbar ===== */
    ::-webkit-scrollbar {
      width: 8px;
    }

    ::-webkit-scrollbar-track {
      background: rgba(0, 0, 0, 0.1);
    }

    ::-webkit-scrollbar-thumb {
      background: linear-gradient(45deg, var(--primary-green), var(--accent-teal));
      border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
      background: linear-gradient(45deg, var(--accent-teal), var(--primary-green));
    }
  </style>
  {{-- Page Header --}}
  <div class="q-header">
    <h1><i class="bi bi-list-check me-3"></i> Queue Management</h1>
    <div class="header-controls">
      <a href="{{ route('queue.history') }}" class="btn btn-light">
        <i class="bi bi-clock-history me-2"></i> View History
      </a>
      <img src="{{ asset('images/fabella-logo.png') }}"
           alt="Fabella logo"
           class="rounded-circle">
    </div>
  </div>

  <div class="row">
    {{-- LEFT: windows & their departments --}}
    <div class="col-lg-9">
      @foreach($queues as $window)
        <div class="category-section">
          @php
            $cls  = $window->name === 'Window A' ? 'window-a' : 'window-b';
            $icon = $window->name === 'Window A' ? 'bi-window' : 'bi-window-stack';
          @endphp
          <a href="{{ route('queue.admin_display', $window) }}"
             class="category-header {{ $cls }}">
            <h3>
              <i class="{{ $icon }} me-3"></i>
              {{ $window->name }}
            </h3>
          </a>
          <div class="departments-grid">
            @foreach($window->children as $dept)
              <a href="{{ route('queue.admin_display', $dept) }}"
                 class="dept-card">
                <div class="dept-name">{{ $dept->name }}</div>
              </a>
            @endforeach
          </div>
        </div>
      @endforeach
    </div>

    {{-- RIGHT: statistics panel --}}
    <div class="col-lg-3">
      <div class="token-panel">
        {{-- Overall totals --}}
        <div class="token-box">
          <h2 id="stat-total">{{ number_format($summary['total']) }}</h2>
          <span><i class="bi bi-ticket-perforated me-2"></i> Total Tokens</span>
        </div>
        <div class="token-box">
          <h2 id="stat-pending">{{ number_format($summary['pending']) }}</h2>
          <span><i class="bi bi-hourglass-split me-2"></i> Pending</span>
        </div>
        <div class="token-box">
          <h2 id="stat-complete">{{ number_format($summary['complete']) }}</h2>
          <span><i class="bi bi-check-circle me-2"></i> Completed</span>
        </div>

        {{-- Per‐window pending --}}
        @foreach($queues as $window)
          @php
            $icon = $window->name === 'Window A' ? 'bi-window' : 'bi-window-stack';
          @endphp
          <div class="token-box">
            <h2 id="w-{{ $window->id }}">
              {{ number_format($window->pending_count) }}
            </h2>
            <span><i class="{{ $icon }} me-2"></i> {{ $window->name }} Pending</span>
          </div>
        @endforeach
      </div>
    </div>
  </div>

  {{-- Live-refresh script --}}
  <script>
    const windowIds = @json($queues->pluck('id'));

    async function refreshStats() {
      try {
        const res = await fetch("{{ route('queue.summary') }}");
        if (!res.ok) throw new Error(res.statusText);
        const { total, pending, complete, windows } = await res.json();

        // overall
        document.getElementById('stat-total').innerText    = total.toLocaleString();
        document.getElementById('stat-pending').innerText  = pending.toLocaleString();
        document.getElementById('stat-complete').innerText = complete.toLocaleString();

        // per-window
        windowIds.forEach(id => {
          const el = document.getElementById('w-'+id);
          if (el && windows[id] !== undefined) {
            el.innerText = windows[id].toLocaleString();
          }
        });
      } catch (e) {
        console.error('Failed to refresh stats:', e);
      }
    }

    // Poll every 5 seconds
    setInterval(refreshStats, 5000);
  </script>
@endsection
