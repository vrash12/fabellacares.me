{{-- resources/views/queue/display.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Queue – {{ $queue->short_name ?: $queue->name }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  {{-- Bootstrap (for basic resets and tabs) --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  {{-- Font Awesome (icons) --}}
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet">

  <style>
    :root {
      --primary-green:   #00b467;
      --secondary-teal:  #0e4749;
      --bg-dark:         #0d4640;
      --accent-blue:     #1e90ff;
      --text-white:      #ffffff;
      --border-radius:   1rem;
      --shadow-lg:       0 1rem 3rem rgba(0, 0, 0, 0.175);
      --shadow-xl:       0 1.5rem 4rem rgba(0, 0, 0, 0.25);
      --transition:      all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      min-height: 100vh;
      background: linear-gradient(135deg, var(--bg-dark) 0%, #0a3d3f 50%, var(--secondary-teal) 100%);
      color: var(--text-white);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }

    /* ─── Animated Background Pattern ─────────────────────────────────────── */
    body::before {
      content: '';
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background-image:
        radial-gradient(circle at 25% 25%, rgba(0, 180, 103, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 75% 75%, rgba(30, 144, 255, 0.1) 0%, transparent 50%);
      pointer-events: none;
      z-index: -1;
      animation: float 20s ease-in-out infinite;
    }
    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50%      { transform: translateY(-20px) rotate(180deg); }
    }

    /* ─── Enhanced Header ───────────────────────────────────────────────────── */
    .topbar {
      background: linear-gradient(135deg, var(--primary-green) 0%, #008a52 100%);
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      font-size: 2.5rem;
      font-weight: 700;
      box-shadow: var(--shadow-lg);
      position: relative;
      overflow: hidden;
    }
    .topbar::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      animation: shimmer 3s infinite;
    }
    @keyframes shimmer {
      0%   { left: -100%; }
      100% { left: 100%; }
    }

    .topbar .btn-back {
      font-size: 1rem;
      padding: 0.5rem 1rem;
      border: 2px solid rgba(255,255,255,0.3);
      border-radius: 0.5rem;
      color: #fff;
      background: rgba(255,255,255,0.1);
      transition: var(--transition);
      text-decoration: none;
    }
    .topbar .btn-back:hover {
      background: rgba(255,255,255,0.2);
      transform: translateY(-2px);
    }

    .topbar img {
      height: 70px;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
      transition: var(--transition);
    }
    .topbar img:hover {
      transform: scale(1.05);
    }

    /* ─── Window Menu & Department List Styles ───────────────────────────────── */
    .children-container {
      max-width: 900px;
      margin: 2rem auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 2rem;
    }
    .window-card {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      padding: 1.5rem;
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255,255,255,0.1);
    }
    .window-card h2 {
      font-size: 1.6rem;
      margin-bottom: 1rem;
      text-align: center;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      padding-bottom: 0.5rem;
    }
    .dept-list {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    .dept-list li {
      margin: 0.75rem 0;
    }
    .dept-list a {
      display: block;
      padding: 0.75rem 1rem;
      background: rgba(255,255,255,0.1);
      color: #fff;
      border-radius: 0.5rem;
      text-decoration: none;
      font-size: 1.1rem;
      transition: var(--transition);
    }
    .dept-list a:hover {
      background: var(--primary-green);
      transform: translateX(4px);
    }

    /* ─── Tab Navigation Styles ───────────────────────────────────────────── */
    .nav-tabs .nav-link {
      font-size: 1.2rem;
      font-weight: 600;
      color: #fff;
      background: rgba(255,255,255,0.1);
      margin-right: 0.5rem;
      border: none;
      border-top-left-radius: 0.75rem;
      border-top-right-radius: 0.75rem;
    }
    .nav-tabs .nav-link.active {
      background: var(--primary-green);
      color: #fff;
    }
    .tab-content {
      padding: 1rem;
      background: rgba(255,255,255,0.05);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255,255,255,0.1);
      margin-top: 1rem;
    }

    /* ─── Enhanced Queue List (Left Column in Ongoing Tab) ───────────────── */
    .queue-list {
      display: grid;
      grid-template-rows: repeat(5, 1fr);
      gap: 0.5rem;
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      padding: 1rem;
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255,255,255,0.1);
    }
    .queue-slot {
      display: grid;
      grid-template-columns: 60px 1fr;
      align-items: center;
      font-size: 2.5rem;
      font-weight: 700;
      background: rgba(255,255,255,0.1);
      border-radius: 0.75rem;
      padding: 1rem;
      transition: var(--transition);
      border: 1px solid rgba(255,255,255,0.1);
      position: relative;
      overflow: hidden;
    }
    .queue-slot::before {
      content: '';
      position: absolute;
      left: 0; top: 0;
      width: 4px; height: 100%;
      background: var(--primary-green);
      transition: var(--transition);
    }
    .queue-slot:hover {
      background: rgba(255,255,255,0.15);
      transform: translateX(5px);
      box-shadow: var(--shadow-lg);
    }
    .queue-slot:hover::before {
      width: 6px;
      background: var(--accent-blue);
    }
    .queue-slot div {
      text-align: center;
      text-shadow: 0 2px 4px rgba(0,0,0,0.3);
    }
    .queue-slot div:first-child {
      color: var(--primary-green);
      font-weight: 800;
    }

    /* ─── Enhanced Right Pane (Now Serving in Ongoing Tab) ──────────────── */
    .right-pane {
      display: grid;
      grid-template-rows: 120px 80px 1fr;
      gap: 1rem;
      height: 100%;
    }
    .dept {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 3rem;
      font-weight: 800;
      background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255,255,255,0.2);
      text-shadow: 0 4px 8px rgba(0,0,0,0.3);
      position: relative;
      overflow: hidden;
    }
    .dept::before {
      content: '';
      position: absolute;
      top: -50%; left: -50%;
      width: 200%; height: 200%;
      background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
      animation: rotate 10s linear infinite;
    }
    @keyframes rotate {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    .timestamp {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      font-weight: 600;
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-lg);
      border: 1px solid rgba(255,255,255,0.1);
      gap: 0.5rem;
    }
    .timestamp i {
      color: var(--primary-green);
      animation: pulse 2s infinite;
    }
    @keyframes pulse {
      0%,100% { transform: scale(1); }
      50%     { transform: scale(1.02); }
    }
    .now-serving {
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0.05) 100%);
      backdrop-filter: blur(20px);
      border-radius: var(--border-radius);
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255,255,255,0.2);
      position: relative;
      overflow: hidden;
    }
    .now-serving::before {
      content: '';
      position: absolute;
      top: 0; left: 0; right: 0; bottom: 0;
      background: linear-gradient(45deg, transparent 30%, rgba(0,180,103,0.1) 50%, transparent 70%);
      animation: sweep 3s ease-in-out infinite;
    }
    @keyframes sweep {
      0%   { transform: translateX(-100%) skewX(-15deg); }
      100% { transform: translateX(100%) skewX(-15deg); }
    }
    .now-serving span {
      font-size: 7rem;
      font-weight: 900;
      letter-spacing: 4px;
      text-shadow: 0 6px 12px rgba(0,0,0,0.4);
      position: relative;
      z-index: 2;
      background: linear-gradient(135deg, var(--primary-green), var(--accent-blue));
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      animation: glow 3s ease-in-out infinite alternate;
    }
    @keyframes glow {
      from { filter: drop-shadow(0 0 10px rgba(0,180,103,0.5)); }
      to   { filter: drop-shadow(0 0 20px rgba(30,144,255,0.8)); }
    }

    /* ─── Finished List (Second Tab) ─────────────────────────────────────── */
    .finished-list {
      max-height: 350px;
      overflow-y: auto;
      margin-top: 1rem;
      background: rgba(255,255,255,0.05);
      border-radius: var(--border-radius);
      padding: 0.75rem;
      box-shadow: var(--shadow-xl);
      border: 1px solid rgba(255,255,255,0.1);
    }
    .finished-item {
      display: flex;
      justify-content: space-between;
      padding: 0.5rem 1rem;
      border-bottom: 1px solid rgba(255,255,255,0.2);
      font-size: 1.8rem;
      color: var(--text-white);
    }
    .finished-item:last-child {
      border-bottom: none;
    }

    /* ─── Update Animation for Queue Slots (Pulse) ───────────────────────── */
    .updating {
      animation: updatePulse 0.6s ease-in-out;
    }
    @keyframes updatePulse {
      0%,100% { transform: scale(1); }
      50%     { transform: scale(1.02); opacity: 0.8; }
    }

    /* ─── Responsive Design ───────────────────────────────────────────────── */
    @media (max-width: 768px) {
      .queue-list {
        grid-template-rows: repeat(5, 80px);
        margin-bottom: 1rem;
      }
      .queue-slot {
        font-size: 1.8rem;
        padding: 0.75rem;
      }
      .dept {
        font-size: 2.5rem;
      }
      .now-serving span {
        font-size: 5rem;
      }
      .topbar {
        font-size: 1.8rem;
        padding: 0.75rem 1rem;
      }
      .topbar img {
        height: 50px;
      }
    }
  </style>
</head>
<body>

  {{-- ─── ENHANCED HEADER ──────────────────────────────────────────────────── --}}
  <div class="topbar">
    {{-- “Back” button → go back to queue selection --}}
    <a href="{{ route('queue.general') }}" class="btn-back">
      <i class="fas fa-arrow-left me-1"></i> Back
    </a>

    {{-- Center title (Queue name) --}}
    <div>
      <i class="fas fa-building me-2"></i>
      {{ $queue->short_name ?: $queue->name }}
    </div>

    {{-- Logo on the right --}}
    <img src="{{ asset('images/fabella-logo.png') }}" alt="Fabella Logo">
  </div>

  {{-- ─── MAIN CONTAINER ──────────────────────────────────────────────────── --}}
  <div class="container-fluid p-3">

    @php
      // 1) Fetch *immediate* children of the current queue
      $children = \App\Models\Queue::where('parent_id', $queue->id)
                    ->orderBy('name', 'asc')->get();

      // 2) Special check: if this is top-level “General” (id=1), we know its children are Window A (2) & Window B (3).
      $isGeneral = $queue->id === 1;
    @endphp

    {{-- =============================================================================
         CASE A) Top level “General” (id = 1) → show two columns: Window A & Window B
         ============================================================================= --}}
    @if($isGeneral)
      @php
        // Extract Window A and Window B from the “General” children
        $windowA = $children->where('id', 2)->first();
        $windowB = $children->where('id', 3)->first();

        // Now fetch each window’s own children (the actual departments):
        $windowA_depts = $windowA
          ? \App\Models\Queue::where('parent_id', $windowA->id)
                ->orderBy('name', 'asc')->get()
          : collect();

        $windowB_depts = $windowB
          ? \App\Models\Queue::where('parent_id', $windowB->id)
                ->orderBy('name', 'asc')->get()
          : collect();
      @endphp

      <div class="children-container">
        {{-- ── Window A Column ── --}}
        @if($windowA)
          <div class="window-card">
            <h2>Window A</h2>
            <ul class="dept-list">
              @foreach($windowA_depts as $dept)
                <li>
                  <a href="{{ route('queue.display', $dept->id) }}">
                    {{ $dept->name }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- ── Window B Column ── --}}
        @if($windowB)
          <div class="window-card">
            <h2>Window B</h2>
            <ul class="dept-list">
              @foreach($windowB_depts as $dept)
                <li>
                  <a href="{{ route('queue.display', $dept->id) }}">
                    {{ $dept->name }}
                  </a>
                </li>
              @endforeach
            </ul>
          </div>
        @endif
      </div>

    {{-- =============================================================================
         CASE B) Second level (“Window A” or “Window B”) which has departments as children,
         but also show the queue for this window itself
         ============================================================================= --}}
    @elseif($children->isNotEmpty())
      {{-- ── NAV TABS (Ongoing / Finished for this window) ──────────────────────────────────── --}}
      <ul class="nav nav-tabs" id="queueTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button
            class="nav-link active"
            id="ongoing-tab"
            data-bs-toggle="tab"
            data-bs-target="#ongoing"
            type="button"
            role="tab"
            aria-controls="ongoing"
            aria-selected="true">
            <i class="fas fa-hourglass-half me-1"></i> Ongoing
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            id="finished-tab"
            data-bs-toggle="tab"
            data-bs-target="#finished"
            type="button"
            role="tab"
            aria-controls="finished"
            aria-selected="false">
            <i class="fas fa-check-circle me-1"></i> Finished
          </button>
        </li>
      </ul>

      <div class="tab-content" id="queueTabsContent">
        {{-- ==================== ONGOING TAB (for Window A or B) ==================== --}}
        <div
          class="tab-pane fade show active"
          id="ongoing"
          role="tabpanel"
          aria-labelledby="ongoing-tab"
          style="min-height: 60vh;">

          <div class="row gx-3">
            {{-- LEFT: First Five Pending Slots for this window --}}
            <div class="col-lg-4 mb-3">
              <div class="queue-list" id="queueList">
                @for ($i = 0; $i < 5; $i++)
                  @php $tokenItem = $pending[$i] ?? null; @endphp
                  <div class="queue-slot" style="animation-delay: {{ $i * 0.1 }}s">
                    <div>{{ $i + 1 }}</div>
                    <div>
                      @if ($tokenItem)
                        {{ $tokenItem->code }}
                      @else
                        &nbsp;
                      @endif
                    </div>
                  </div>
                @endfor
              </div>
            </div>

            {{-- RIGHT: Window Name / Timestamp / Now Serving for this window --}}
            <div class="col-lg-8 ps-0 mb-3">
              <div class="right-pane h-100">
                <div class="dept">
                  {{ strtoupper($queue->short_name ?: $queue->name) }}
                </div>
                <div class="timestamp" id="tsLine">
                  <i class="fas fa-clock me-2"></i>
                  <span id="currentTime">{{ $currentTime }}</span>
                  <span> | Now Serving</span>
                </div>
                <div class="now-serving">
                  <span id="nowCode">{{ $currentServing }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        {{-- ==================== FINISHED TAB (for Window A or B) ==================== --}}
        <div
          class="tab-pane fade"
          id="finished"
          role="tabpanel"
          aria-labelledby="finished-tab">

          <h4 class="mt-3 mb-3 text-white"><i class="fas fa-history me-1"></i> Completed Tokens</h4>

          @if ($finished->isEmpty())
            <div class="text-center text-muted py-5">
              <i class="fas fa-info-circle me-1"></i> No tokens have been served yet.
            </div>
          @else
            <div class="finished-list">
              @foreach ($finished as $tokenItem)
                <div class="finished-item">
                  <span>{{ $tokenItem->code }}</span>
                  <small class="text-muted">
                    Served at {{ \Carbon\Carbon::parse($tokenItem->served_at)->format('H:i:s, d M Y') }}
                  </small>
                </div>
              @endforeach
            </div>
          @endif

        </div>
      </div>

      {{-- ─── Below the tabs, also show this window’s list of departments ─────────────────────────────── --}}
      <div class="children-container mt-4">
        <div class="window-card">
          <h2>{{ $queue->name }} Departments</h2>
          <ul class="dept-list">
            @foreach($children as $dept)
              <li>
                <a href="{{ route('queue.display', $dept->id) }}">
                  {{ $dept->name }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>
      </div>

    {{-- =============================================================================
         CASE C) Leaf‐level department (no children) → show Ongoing / Finished queue UI
         ============================================================================= --}}
    @else
      {{-- ── NAV TABS (Ongoing / Finished) ──────────────────────────────────── --}}
      <ul class="nav nav-tabs" id="queueTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button
            class="nav-link active"
            id="ongoing-tab"
            data-bs-toggle="tab"
            data-bs-target="#ongoing"
            type="button"
            role="tab"
            aria-controls="ongoing"
            aria-selected="true">
            <i class="fas fa-hourglass-half me-1"></i> Ongoing
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button
            class="nav-link"
            id="finished-tab"
            data-bs-toggle="tab"
            data-bs-target="#finished"
            type="button"
            role="tab"
            aria-controls="finished"
            aria-selected="false">
            <i class="fas fa-check-circle me-1"></i> Finished
          </button>
        </li>
      </ul>

      <div class="tab-content" id="queueTabsContent">
        {{-- ==================== ONGOING TAB ==================== --}}
        <div
          class="tab-pane fade show active"
          id="ongoing"
          role="tabpanel"
          aria-labelledby="ongoing-tab"
          style="min-height: 80vh;">

          <div class="row gx-3">
            {{-- LEFT: First Five Pending Slots --}}
            <div class="col-lg-4 mb-3">
              <div class="queue-list" id="queueList">
                @for ($i = 0; $i < 5; $i++)
                  @php $tokenItem = $pending[$i] ?? null; @endphp
                  <div class="queue-slot" style="animation-delay: {{ $i * 0.1 }}s">
                    <div>{{ $i + 1 }}</div>
                    <div>
                      @if ($tokenItem)
                        {{ $tokenItem->code }}
                      @else
                        &nbsp;
                      @endif
                    </div>
                  </div>
                @endfor
              </div>
            </div>

            {{-- RIGHT: Department Name / Timestamp / Now Serving --}}
            <div class="col-lg-8 ps-0 mb-3">
              <div class="right-pane h-100">
                <div class="dept">
                  {{ strtoupper($queue->short_name ?: $queue->name) }}
                </div>
                <div class="timestamp" id="tsLine">
                  <i class="fas fa-clock me-2"></i>
                  <span id="currentTime">{{ $currentTime }}</span>
                  <span> | Now Serving</span>
                </div>
                <div class="now-serving">
                  <span id="nowCode">{{ $currentServing }}</span>
                </div>
              </div>
            </div>
          </div>

        </div>

        {{-- ==================== FINISHED TAB ==================== --}}
        <div
          class="tab-pane fade"
          id="finished"
          role="tabpanel"
          aria-labelledby="finished-tab">

          <h4 class="mt-3 mb-3 text-white"><i class="fas fa-history me-1"></i> Completed Tokens</h4>

          @if ($finished->isEmpty())
            <div class="text-center text-muted py-5">
              <i class="fas fa-info-circle me-1"></i> No tokens have been served yet.
            </div>
          @else
            <div class="finished-list">
              @foreach ($finished as $tokenItem)
                <div class="finished-item">
                  <span>{{ $tokenItem->code }}</span>
                  <small class="text-muted">
                    Served at {{ \Carbon\Carbon::parse($tokenItem->served_at)->format('H:i:s, d M Y') }}
                  </small>
                </div>
              @endforeach
            </div>
          @endif

        </div>
      </div>
    @endif

  </div> {{-- /.container-fluid --}}

  {{-- ─── BOOTSTRAP JS (tabs + dependencies) ───────────────────────────────── --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- ─── LIVE‐POLL SCRIPT FOR “ONGOING” TAB (only on leaf and window levels) ───────────────────────────────── --}}
  @if($children->isEmpty() || (! $isGeneral && $children->isNotEmpty()))
  <script>
    const statusUrl = "{{ route('queue.status', $queue->id) }}";
    const listEl    = document.getElementById('queueList');
    const nowCodeEl = document.getElementById('nowCode');
    const tsEl      = document.getElementById('tsLine');

    let isRefreshing = false;

    async function refreshQueue() {
      if (isRefreshing) return;
      isRefreshing = true;

      try {
        const response = await fetch(statusUrl);
        if (!response.ok) return;
        const data = await response.json();
        const pending = data.pending;       // array of { code: … }
        const allCodes = data.all_codes;    // array of all codes, sorted

        // Animate left “next five” panel
        listEl.classList.add('updating');
        setTimeout(() => {
          listEl.innerHTML = '';
          for (let i = 0; i < 5; i++) {
            const code = pending[i]?.code || '';
            listEl.insertAdjacentHTML('beforeend', `
              <div class="queue-slot" style="animation-delay: ${i * 0.1}s">
                <div>${i + 1}</div>
                <div>${code || '&nbsp;'}</div>
              </div>
            `);
          }
          listEl.classList.remove('updating');
        }, 300);

        // Animate “Now Serving” if it changed
        const newNow = allCodes[0] || '—';
        nowCodeEl.style.transform = 'scale(0.8)';
        setTimeout(() => {
          nowCodeEl.innerText = newNow;
          nowCodeEl.style.transform = 'scale(1)';
        }, 200);

        // Update timestamp line
        tsEl.querySelector('#currentTime').innerText = new Date().toLocaleString(undefined, {
          day:   '2-digit',
          month: 'long',
          year:  'numeric',
          hour:  '2-digit',
          minute:'2-digit',
          second:'2-digit'
        });
      } catch (error) {
        console.error('Failed to refresh queue:', error);
      } finally {
        isRefreshing = false;
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Initial fetch + interval
      refreshQueue();
      setInterval(refreshQueue, 4000);

      // Also keep the “timestamp” clock ticking each second
      setInterval(() => {
        tsEl.querySelector('#currentTime').innerText = new Date().toLocaleString(undefined, {
          day:   '2-digit',
          month: 'long',
          year:  'numeric',
          hour:  '2-digit',
          minute:'2-digit',
          second:'2-digit'
        });
      }, 1000);
    });
  </script>
  @endif
</body>
</html>
