{{-- resources/views/queue/admin_display.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Queue – {{ $queue->short_name ?? $queue->name }} (Admin)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  
  {{-- Bootstrap CSS (for tabs and basic styling) --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
  {{-- Bootstrap Icons --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css"
    rel="stylesheet">
  <style>
    :root {
      --primary: #00b467;
      --primary-dark: #008a4f;
      --bg: #0a1a1a;
      --surface: #1a2a2a;
      --surface-light: #2a3a3a;
      --accent: #00ff7f;
      --text: #ffffff;
      --text-muted: #b0b0b0;
      --border: rgba(255, 255, 255, 0.1);
      --glow: 0 0 20px rgba(0, 180, 103, 0.3);
      --shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      background: linear-gradient(135deg, var(--bg) 0%, #051015 100%);
      color: var(--text);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow: hidden;
    }

    /* ─── HEADER (“Team Bar”) ───────────────────────────────────────────────── */
    .teambar {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }
    .teambar::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
      animation: shimmer 3s infinite;
    }
    @keyframes shimmer {
      0%   { left: -100%; }
      100% { left: 100%; }
    }

    .btn-back {
      background: rgba(255, 255, 255, 0.2);
      color: white;
      padding: 0.5rem 1rem;
      border-radius: 12px;
      font-weight: 600;
      border: none;
      transition: transform 0.3s ease, background 0.3s ease;
      backdrop-filter: blur(10px);
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
    }
    .btn-back:hover {
      background: rgba(255, 255, 255, 0.3);
      transform: translateX(-2px);
    }

    .btn-serve {
      background: linear-gradient(135deg, var(--accent) 0%, #00cc66 100%);
      color: var(--bg);
      padding: 0.8rem 2rem;
      font-size: 1.2rem;
      border: none;
      border-radius: 12px;
      box-shadow: var(--glow);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .btn-serve:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 30px rgba(0, 255, 127, 0.5);
    }

    /* «NEW» Reset Counter button styling */
    .btn-reset {
      background: linear-gradient(135deg, #ffbe3c 0%, #ff9900 100%);
      color: var(--bg);
      padding: 0.8rem 1.75rem;
      font-size: 1.05rem;
      border: none;
      border-radius: 12px;
      box-shadow: 0 0 20px rgba(255, 190, 60, 0.35);
      transition: opacity .25s, transform .25s, box-shadow .25s;
      margin-left: 1rem;
    }
    .btn-reset:hover {
      transform: translateY(-2px);
      box-shadow: 0 0 32px rgba(255, 190, 60, 0.55);
    }

    .logo {
      height: 60px;
      filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.3));
    }

    /* ─── TAB NAVIGATION ───────────────────────────────────────────────────── */
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
      background: var(--primary);
      color: #fff;
    }

    .tab-content {
      margin-top: 1rem;
      background: var(--surface);
      border-radius: var(--border);
      box-shadow: var(--shadow);
      padding: 1.5rem;
      border: 1px solid var(--border);
      height: calc(100vh - 200px); /* Adjust if header height changes */
      overflow: hidden;
    }

    /* ─── LAYOUT GRID ───────────────────────────────────────────────────────── */
    .layout {
      display: grid;
      grid-template-columns: 300px 1fr;
      height: 100%;
      gap: 2px;
    }

    /* ─── QUEUE LIST (Ongoing Tab, Left Column) ─────────────────────────────── */
    .queue-list {
      background: var(--surface);
      border-radius: 0 20px 0 0;
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      box-shadow: var(--shadow);
      border-right: 3px solid var(--accent);
      overflow-y: auto;
      height: 100%;
    }

    .queue-header {
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .queue-slot {
      background: linear-gradient(135deg, var(--surface-light) 0%, var(--surface) 100%);
      border-radius: 16px;
      padding: 1rem 1.5rem;
      display: grid;
      grid-template-columns: 60px 1fr;
      align-items: center;
      gap: 1rem;
      transition: all 0.3s ease;
      border: 1px solid var(--border);
      position: relative;
      overflow: hidden;
    }
    .queue-slot:hover {
      transform: translateX(8px);
      border-color: var(--accent);
      box-shadow: 0 8px 25px rgba(0, 180, 103, 0.2);
    }

    .queue-number {
      background: linear-gradient(135deg, var(--accent) 0%, #00cc66 100%);
      color: var(--bg);
      border-radius: 50%;
      width: 45px;
      height: 45px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.2rem;
    }

    .queue-code {
      font-size: 2rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text);
      text-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    }

    /* ─── RIGHT PANE (Ongoing Tab) ──────────────────────────────────────────── */
    .right-pane {
      background: var(--surface);
      border-radius: 20px 0 0 0;
      display: grid;
      grid-template-rows: auto auto 1fr;
      box-shadow: var(--shadow);
      overflow: hidden;
    }

    .dept-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 2rem 1.5rem;
      text-align: center;
      font-size: 3rem;
      font-weight: 700;
      color: var(--text);
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      position: relative;
      overflow: hidden;
    }
    .dept-header::after {
      content: '';
      position: absolute;
      bottom: 0; left: 0;
      width: 100%; height: 4px;
      background: linear-gradient(90deg, var(--accent) 0%, transparent 50%, var(--accent) 100%);
      animation: pulse 2s ease-in-out infinite;
    }
    @keyframes pulse {
      0%, 100% { opacity: 0.5; }
      50%      { opacity: 1; }
    }

    .timestamp {
      background: var(--surface-light);
      padding: 1rem 1.5rem;
      text-align: center;
      font-size: 1.4rem;
      font-weight: 600;
      color: var(--text-muted);
      border-bottom: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.75rem;
      position: relative;
    }
    .status-indicator {
      width: 12px;
      height: 12px;
      background: var(--accent);
      border-radius: 50%;
      animation: blink 2s infinite;
    }
    @keyframes blink {
      0%, 50% { opacity: 1; }
      51%, 100% { opacity: 0.3; }
    }

    .now-serving {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 1.5rem;
      padding: 2rem;
      background: radial-gradient(circle at center, rgba(0, 255, 127, 0.1) 0%, transparent 70%);
      position: relative;
      overflow: hidden;
    }

    .serving-label {
      font-size: 2rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 3px;
    }
    .serving-code {
      font-size: 7rem;
      font-weight: 900;
      letter-spacing: 4px;
      color: var(--accent);
      text-shadow: 0 0 30px rgba(0, 255, 127, 0.5);
      animation: glow 3s ease-in-out infinite alternate;
      position: relative;
    }
    @keyframes glow {
      0%, 100% {
        text-shadow: 0 0 30px rgba(0, 255, 127, 0.5);
        transform: scale(1);
      }
      50% {
        text-shadow: 0 0 50px rgba(0, 255, 127, 0.8);
        transform: scale(1.02);
      }
    }
    .serving-code::before {
      content: '';
      position: absolute;
      top: -20px; left: -20px;
      right: -20px; bottom: -20px;
      border: 2px solid var(--accent);
      border-radius: 20px;
      opacity: 0.3;
      animation: borderPulse 4s infinite;
    }
    @keyframes borderPulse {
      0%, 100% {
        opacity: 0.3;
        transform: scale(1);
      }
      50% {
        opacity: 0.6;
        transform: scale(1.1);
      }
    }

    /* ─── FINISHED LIST (Finished Tab, full width) ────────────────────────── */
    .finished-list-container {
      height: 100%;
      display: flex;
      flex-direction: column;
    }
    .finished-label {
      font-size: 2rem;
      font-weight: 600;
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      color: var(--accent);
    }
    .finished-list {
      flex: 1;
      overflow-y: auto;
      background: var(--surface-light);
      border-radius: 12px;
      padding: 1rem;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
    }
    .finished-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 0.75rem 1rem;
      border-bottom: 1px solid var(--border);
      font-size: 1.5rem;
      color: var(--text);
    }
    .finished-item:last-child {
      border-bottom: none;
    }
    .finished-code {
      font-weight: 700;
      letter-spacing: 1px;
    }
    .finished-time {
      font-size: 1rem;
      color: var(--text-muted);
    }

    /* ─── LOADING OVERLAY ─────────────────────────────────────────────────── */
    .loading-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(10, 26, 26, 0.9);
      display: flex;
      align-items: center;
      justify-content: center;
      z-index: 1000;
      visibility: hidden;
      opacity: 0;
      transition: all 0.3s ease;
    }
    .loading-overlay.active {
      opacity: 1;
      visibility: visible;
    }
    .spinner {
      width: 60px; height: 60px;
      border: 4px solid var(--surface);
      border-top: 4px solid var(--accent);
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }
    @keyframes spin {
      0%   { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* ─── RESPONSIVE ADJUSTMENTS ───────────────────────────────────────────── */
    @media (max-width: 768px) {
      .layout {
        grid-template-columns: 1fr;
        grid-template-rows: auto 1fr;
        height: auto;
      }
      .queue-list {
        border-radius: 0;
        border-right: none;
        border-bottom: 3px solid var(--accent);
        grid-template-rows: repeat(5, 80px);
      }
      .queue-slot {
        font-size: 1.6rem;
        padding: 0.75rem;
      }
      .dept-header {
        font-size: 2.5rem;
      }
      .serving-code {
        font-size: 5rem;
      }
      .teambar {
        padding: 0.75rem 1rem;
        font-size: 1.8rem;
      }
      .logo {
        height: 50px;
      }
      .tab-content {
        height: auto;
        padding: 1rem;
      }
    }
  </style>  


</head>
<body>
  {{-- ─── LOADING OVERLAY ──────────────────────────────────────────────────── --}}
  <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
  </div>

  {{-- ─── HEADER (“Team Bar”) ───────────────────────────────────────────────── --}}
  <div class="teambar">
    <div class="d-flex align-items-center gap-3">
      {{-- Back to Admin Queue List --}}
      <a href="{{ route('queue.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back
      </a>

      {{-- ← Four management buttons moved from index --}}
    
      <a href="{{ route('queue.delete.list', $queue) }}" class="btn-back">
        <i class="bi bi-trash"></i> Manage
      </a>
      <form action="{{ route('queue.store', $queue) }}" method="POST" class="d-inline">
        @csrf
        <button class="btn-back" type="submit" formtarget="_blank">
          <i class="bi bi-plus-circle"></i> Add Token
        </button>
      </form>
      @php $next = $queue->tokens()->whereNull('served_at')->orderBy('created_at')->first(); @endphp
      @if($next)
        <a href="{{ route('queue.tokens.edit', [$queue, $next]) }}" class="btn-back">
          <i class="bi bi-pencil-square"></i> Edit Next
        </a>
      @else
        <span class="btn-back text-muted" style="opacity:.6;cursor:not-allowed">
          <i class="bi bi-pencil-square"></i> No Token
        </span>
      @endif

      {{-- “Serve Next” Button --}}
      <form action="{{ route('queue.serveNext.admin', $queue) }}"
            method="POST"
            class="d-inline teambar-form">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn-serve"
                onclick="return confirm('Serve next token?');">
          <i class="bi bi-play-fill"></i> Serve Next
        </button>
      </form>

      {{-- «NEW» “Reset Counter” Button --}}
      <form action="{{ route('queue.reset', $queue) }}"
            method="POST"
            class="d-inline teambar-form"
            onsubmit="return confirm(
              'Reset the token counter for {{ $queue->name }} back to 1? Existing tokens stay untouched.'
            );">
        @csrf
        @method('PATCH')
        <button type="submit" class="btn-reset">
          <i class="bi bi-arrow-counterclockwise"></i> Reset Counter
        </button>
      </form>

      {{-- Route “Next Pending” into Child Queues (if any) --}}
      @if($queue->children->isNotEmpty())
        <div class="ms-3 d-inline">
          @foreach($queue->children as $child)
            <form action="{{ route('queue.route', [$queue, $child]) }}"
                  method="POST"
                  class="d-inline teambar-form"
                  onsubmit="return confirm('Route next token to {{ $child->name }}?');">
              @csrf
              <button type="submit"
                      class="btn btn-outline-light btn-sm">
                → {{ $child->short_name }}
              </button>
            </form>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Logo on Right --}}
    <img src="{{ asset('images/fabella-logo.png') }}"
         alt="Fabella Logo"
         class="logo">
  </div>

  {{-- ─── TAB NAVIGATION ───────────────────────────────────────────────────── --}}
  <div class="container-fluid p-3">
    <ul class="nav nav-tabs" id="adminQueueTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="ongoing-tab"
                data-bs-toggle="tab"
                data-bs-target="#ongoing"
                type="button"
                role="tab"
                aria-controls="ongoing"
                aria-selected="true">
          <i class="bi bi-hourglass-split me-1"></i> Ongoing
        </button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="finished-tab"
                data-bs-toggle="tab"
                data-bs-target="#finished"
                type="button"
                role="tab"
                aria-controls="finished"
                aria-selected="false">
          <i class="bi bi-check2-circle me-1"></i> Finished
        </button>
      </li>
    </ul>

    {{-- ─── TAB CONTENT ─────────────────────────────────────────────────────── --}}
    <div class="tab-content" id="adminQueueTabsContent">
      {{-- ONGOING TAB --}}
      <div class="tab-pane fade show active"
           id="ongoing"
           role="tabpanel"
           aria-labelledby="ongoing-tab">

        <div class="layout">
          {{-- LEFT: Next Five Pending Tokens --}}
          <div class="queue-list">
            <div class="queue-header">
              <i class="bi bi-list-ol me-1"></i> Queue
            </div>
            <div id="queueList">
              @foreach($tokens as $idx => $t)
                <div class="queue-slot">
                  <div class="queue-number">{{ $idx + 1 }}</div>
                  <div class="queue-code">{{ $t->code }}</div>
                </div>
              @endforeach
              @for($i = $tokens->count(); $i < 5; $i++)
                <div class="queue-slot">
                  <div class="queue-number">{{ $i + 1 }}</div>
                  <div class="queue-code" style="opacity: 0.3;">—</div>
                </div>
              @endfor
            </div>
          </div>

          {{-- RIGHT: Dept Name / Timestamp / Now Serving --}}
          <div class="right-pane">
            <div class="dept-header">
              {{ $queue->short_name ?? $queue->name }}
            </div>

            <div class="timestamp" id="tsLine">
              <div class="status-indicator"></div>
              <span>{{ $currentTime }} | Now Serving</span>
            </div>

            <div class="now-serving">
              <div class="serving-label">Now Serving</div>
              <div class="serving-code" id="nowCode">
                {{ $currentServing ?: '—' }}
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- FINISHED TAB --}}
      <div class="tab-pane fade"
           id="finished"
           role="tabpanel"
           aria-labelledby="finished-tab">

        <div class="finished-list-container">
          <div class="finished-label">
            <i class="bi bi-clock-history me-1"></i> Completed Tokens
          </div>

          @if($finished->isEmpty())
            <div class="text-center text-muted py-5">
              <i class="bi bi-info-circle me-1"></i>
              No tokens have been served yet.
            </div>
          @else
            <div class="finished-list">
              @foreach($finished as $token)
                <div class="finished-item">
                  <span class="finished-code">{{ $token->code }}</span>
                  <span class="finished-time">
                    Served at {{ \Carbon\Carbon::parse($token->served_at)
                                 ->format('H:i:s, d M Y') }}
                  </span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ─── BOOTSTRAP JS (tabs + dependencies) ───────────────────────────────── --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  {{-- ─── LIVE‐POLL SCRIPT FOR “ONGOING” TAB ───────────────────────────────── --}}
 <script>
    const statusUrl = "{{ route('queue.status', $queue) }}";
    const listEl    = document.getElementById('queueList');
    const nowCodeEl = document.getElementById('nowCode');
    const tsEl      = document.getElementById('tsLine');
    const loadingOverlay = document.getElementById('loadingOverlay');
    let isRefreshing = false;

    async function refreshQueue() {
      if (isRefreshing) return;
      isRefreshing = true;

      try {
        const response = await fetch(statusUrl);
        if (!response.ok) return;
        const data = await response.json();
        const pending = data.pending.slice(0, 5);
        const allCodes = data.all_codes;

        // Animate and rebuild the left “queue-list”
        listEl.classList.add('updating');
        setTimeout(() => {
          listEl.innerHTML = '';
          for (let i = 0; i < 5; i++) {
            const code = pending[i]?.code || '';
            const opacity = pending[i] ? '1' : '0.3';
            listEl.insertAdjacentHTML('beforeend', `
              <div class="queue-slot">
                <div class="queue-number">${i+1}</div>
                <div class="queue-code" style="opacity: ${opacity};">
                  ${code || '&nbsp;'}
                </div>
              </div>
            `);
          }
          listEl.classList.remove('updating');
        }, 300);

        // Animate “Now Serving” update
        const newNow = allCodes[0] || '—';
        nowCodeEl.style.transform = 'scale(0.8)';
        setTimeout(() => {
          nowCodeEl.innerText = newNow;
          nowCodeEl.style.transform = 'scale(1)';
        }, 200);

        // Update timestamp
        tsEl.querySelector('span').innerText = 
          new Date().toLocaleString(undefined, {
            day:   '2-digit',
            month: 'long',
            year:  'numeric',
            hour:  '2-digit',
            minute:'2-digit',
            second:'2-digit'
          }) + ' | Now Serving';

      } catch (error) {
        console.error('Failed to refresh queue:', error);
      } finally {
        isRefreshing = false;
      }
    }

    /** Show loading overlay when any header‐form is submitted **/
    function showOverlay() {
      loadingOverlay.classList.add('active');
    }

    // Attach showOverlay() to every form inside the .teambar
    document.querySelectorAll('.teambar-form').forEach(formEl => {
      formEl.addEventListener('submit', showOverlay);
    });

    // Initial load + polling every 4 seconds
    setTimeout(refreshQueue, 1000);
    setInterval(refreshQueue, 4000);
  </script>
</body>
</html>
