{{-- resources/views/queue/general_select.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>General’s Queue • {{ $queue->name }}</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  {{-- Bootstrap 5.1.3 --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">

  {{-- Font Awesome --}}
  <link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
    rel="stylesheet">

  <style>
    :root {
      --primary:       #00b467;       /* bright green */
      --primary-dark:  #008a4f;       /* darker green */
      --accent:        #00ff7f;       /* neon green accent */
      --bg:            #0a1a1a;       /* deep charcoal */
      --surface:       #1a2a2a;       /* slightly lighter */
      --surface-light: #2a3a3a;       /* even lighter */
      --text:          #ffffff;       /* white */
      --text-muted:    #b0b0b0;       /* light gray */
      --border:        rgba(255,255,255,0.1);
      --shadow:        0 8px 32px rgba(0,0,0,0.3);
      --shadow-lg:     0 1rem 3rem rgba(0,0,0,0.175);
      --shadow-xl:     0 1.5rem 4rem rgba(0,0,0,0.25);
      --radius:        1rem;
      --transition:    all 0.3s ease;
    }

    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      background: linear-gradient(135deg, var(--bg) 0%, #051015 100%);
      color: var(--text);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      overflow-x: hidden;
    }

    /* ── WINDOW BUTTONS (TOP) ─────────────────────────────────────────────────── */
    .button-deck-top {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
      padding: 1.5rem;
      background: var(--surface);
      box-shadow: var(--shadow);
      border-bottom: 3px solid var(--accent);
    }
    .window-button {
      background: var(--accent);
      color: var(--bg);
      border: none;
      border-radius: var(--radius);
      padding: 1.5rem 1rem;
      flex: 1 1 250px;
      text-transform: uppercase;
      font-weight: 600;
      font-size: 1.4rem;
      text-align: center;
      cursor: pointer;
      box-shadow: var(--shadow);
      transition: background 0.2s, transform 0.2s, box-shadow 0.2s;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }
    .window-button i {
      font-size: 1.8rem;
    }
    .window-button:hover {
      background: var(--primary-dark);
      transform: translateY(-3px);
      box-shadow: 0 0 20px rgba(0,255,127,0.4);
    }

    /* ── HEADER BAR ───────────────────────────────────────────────────────────── */
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
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.08), transparent);
      animation: shimmer 3s infinite;
    }
    @keyframes shimmer {
      0%   { left: -100%; }
      100% { left: 100%; }
    }
    .btn-back {
      background: rgba(255,255,255,0.2);
      color: var(--text);
      padding: 0.5rem 1rem;
      border-radius: 0.5rem;
      border: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: var(--transition);
      text-decoration: none;
    }
    .btn-back:hover {
      background: rgba(255,255,255,0.3);
      transform: translateX(-2px);
    }
    .logo {
      height: 60px;
      filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
      transition: var(--transition);
    }
    .logo:hover {
      transform: scale(1.05);
    }

    /* ── MAIN GRID ────────────────────────────────────────────────────────────── */
    .layout {
      display: grid;
      grid-template-columns: 300px 1fr;
      grid-template-rows: auto 1fr;
      height: calc(100vh - 120px);
      gap: 2px;
    }

    /* ── LEFT PANEL: QUEUE LIST ───────────────────────────────────────────────── */
    .queue-list {
      background: var(--surface);
      border-radius: 0 0 0 var(--radius);
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      box-shadow: var(--shadow);
      border-right: 3px solid var(--accent);
      overflow-y: auto;
    }
    .queue-header {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--accent);
      text-transform: uppercase;
      text-align: center;
      margin-bottom: 1rem;
      letter-spacing: 1px;
    }
    .queue-slot {
      background: linear-gradient(135deg, var(--surface-light) 0%, var(--surface) 100%);
      border-radius: var(--radius);
      padding: 0.75rem 1rem;
      display: grid;
      grid-template-columns: 60px 1fr;
      align-items: center;
      gap: 1rem;
      transition: var(--transition);
      border: 1px solid var(--border);
      position: relative;
      overflow: hidden;
    }
    .queue-slot:hover {
      transform: translateX(6px);
      border-color: var(--accent);
      box-shadow: 0 8px 20px rgba(0,180,103,0.2);
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
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text);
      text-shadow: 0 0 5px rgba(0,0,0,0.3);
    }

    /* ── RIGHT PANEL: NOW SERVING & TIMESTAMP ───────────────────────────────── */
    .right-pane {
      background: var(--surface);
      border-radius: 0 var(--radius) var(--radius) 0;
      display: grid;
      grid-template-rows: auto auto 1fr;
      box-shadow: var(--shadow);
    }
    .dept-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 2rem;
      text-align: center;
      font-size: 3.5rem;
      font-weight: 700;
      color: var(--text);
      border-radius: 0 var(--radius) 0 0;
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
      0%,100% { opacity: 0.5; }
      50%     { opacity: 1; }
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
      0%,50%   { opacity: 1; }
      51%,100% { opacity: 0.3; }
    }
    .now-serving {
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 1rem;
      padding: 3rem;
      background: radial-gradient(circle at center, rgba(0,255,127,0.1) 0%, transparent 70%);
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
      font-size: 6rem;
      font-weight: 900;
      letter-spacing: 4px;
      color: var(--accent);
      text-shadow: 0 0 20px rgba(0,255,127,0.5);
      animation: glow 3s ease-in-out infinite alternate;
      position: relative;
    }
    @keyframes glow {
      0%,100% {
        text-shadow: 0 0 20px rgba(0,255,127,0.5);
        transform: scale(1);
      }
      50% {
        text-shadow: 0 0 40px rgba(0,255,127,0.8);
        transform: scale(1.02);
      }
    }
    .serving-code::before {
      content: '';
      position: absolute;
      top: -20px; left: -20px; right: -20px; bottom: -20px;
      border: 2px solid var(--accent);
      border-radius: var(--radius);
      opacity: 0.3;
      animation: borderPulse 4s infinite;
    }
    @keyframes borderPulse {
      0%,100% {
        opacity: 0.3;
        transform: scale(1);
      }
      50% {
        opacity: 0.6;
        transform: scale(1.05);
      }
    }

    /* ── Responsive Adjustments ─────────────────────────────────────────────── */
    @media (max-width: 768px) {
      .layout {
        grid-template-columns: 1fr;
        grid-template-rows: auto auto 1fr;
        height: auto;
      }
      .queue-list {
        border-radius: 0;
        border-right: none;
        border-bottom: 3px solid var(--accent);
      }
      .queue-slot {
        font-size: 1.5rem;
        padding: 0.5rem 0.75rem;
      }
      .dept-header {
        font-size: 2.5rem;
        padding: 1.5rem;
      }
      .serving-code {
        font-size: 4.5rem;
      }
      .button-deck-top {
        flex-direction: column;
        gap: 1rem;
      }
      .window-button {
        font-size: 1.25rem;
        padding: 1rem 0.75rem;
      }
      .window-button i {
        font-size: 1.5rem;
      }
    }
  </style>
</head>
<body>

  {{-- ── WINDOW BUTTONS AT THE VERY TOP ─────────────────────────────────────── --}}
  <div class="button-deck-top">
    @foreach($windows as $window)
      <button class="window-button"
              onclick="window.location='{{ route('queue.display', $window->id) }}'">
        <i class="fas fa-tv"></i>
        {{ $window->name }}
      </button>
    @endforeach
  </div>

  {{-- ── HEADER BAR ─────────────────────────────────────────────────────────── --}}
  <div class="teambar">
    <img src="{{ asset('images/fabella-logo.png') }}" alt="Fabella Logo" class="logo">
  </div>

  {{-- ── MAIN GRID ───────────────────────────────────────────────────────────── --}}
  <div class="layout">

    {{-- LEFT: SHOW GENERAL’S ENTIRE QUEUE ─────────────────────────────────── --}}
    <div class="queue-list">
      <div class="queue-header">
        <i class="fas fa-list-ol me-2"></i> General’s Entire Queue
      </div>
      <div id="queueList">
        @forelse($tokens as $idx => $t)
          <div class="queue-slot">
            <div class="queue-number">{{ $idx + 1 }}</div>
            <div class="queue-code">{{ $t->code }}</div>
          </div>
        @empty
          <div class="queue-slot">
            <div class="queue-number">—</div>
            <div class="queue-code" style="opacity:0.3;">No tokens</div>
          </div>
        @endforelse
      </div>
    </div>

    {{-- RIGHT: NOW SERVING + TIMESTAMP ─────────────────────────────────────── --}}
    <div class="right-pane">
      <div class="dept-header">
        {{ $queue->name }}
      </div>
      <div class="timestamp" id="tsLine">
        <div class="status-indicator"></div>
        <span>{{ $currentTime }} | Now Serving</span>
      </div>
      <div class="now-serving">
        <div class="serving-label">Now Serving</div>
        <div class="serving-code" id="nowCode">
          {{ $currentServing ?: '—' }}
        </div>
      </div>
    </div>

  </div>

  {{-- ── LIVE POLL SCRIPT ─────────────────────────────────────────────────── --}}
  <script>
    const statusUrl = "{{ route('queue.status', $queue->id) }}";
    const listEl    = document.getElementById('queueList');
    const nowEl     = document.getElementById('nowCode');
    const tsLineEl  = document.getElementById('tsLine');

    let isRefreshing = false;
    async function refreshGeneral() {
      if (isRefreshing) return;
      isRefreshing = true;
      try {
        const resp = await fetch(statusUrl);
        if (!resp.ok) throw new Error('Network error');
        const data = await resp.json();
        const pending = data.pending;     // Array of all upcoming tokens
        const allCodes = data.all_codes;  // Full queue, with index 0 = now serving

        // Rebuild the entire queue list
        listEl.classList.add('updating');
        setTimeout(() => {
          listEl.innerHTML = '';
          if (pending.length > 0) {
            pending.forEach((t, i) => {
              listEl.insertAdjacentHTML('beforeend', `
                <div class="queue-slot">
                  <div class="queue-number">${i + 1}</div>
                  <div class="queue-code">${t.code}</div>
                </div>
              `);
            });
          } else {
            // No tokens at all
            listEl.insertAdjacentHTML('beforeend', `
              <div class="queue-slot">
                <div class="queue-number">—</div>
                <div class="queue-code" style="opacity:0.3;">No tokens</div>
              </div>
            `);
          }
          listEl.classList.remove('updating');
        }, 300);

        // Update “Now Serving”
        const newNow = allCodes[0] || '—';
        nowEl.style.transform = 'scale(0.8)';
        setTimeout(() => {
          nowEl.innerText = newNow;
          nowEl.style.transform = 'scale(1)';
        }, 200);

        // Update timestamp line
        tsLineEl.querySelector('span').innerText = new Date().toLocaleString(undefined, {
          day: '2-digit', month: 'long', year: 'numeric',
          hour: '2-digit', minute: '2-digit', second: '2-digit'
        }) + ' | Now Serving';

      } catch (err) {
        console.error('Failed to refresh general:', err);
        document.querySelector('.status-indicator').style.background = 'red';
      } finally {
        isRefreshing = false;
      }
    }

    document.addEventListener('DOMContentLoaded', () => {
      // Initial fetch + interval
      setTimeout(refreshGeneral, 1000);
      setInterval(refreshGeneral, 4000);
    });
  </script>
</body>
</html>
