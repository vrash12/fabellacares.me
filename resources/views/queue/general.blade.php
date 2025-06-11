{{-- resources/views/queue/general.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Public Queue • General</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  {{-- Bootstrap & FontAwesome --}}
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
    rel="stylesheet">
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
      min-height: 100vh;
    }

    /* ─── HEADER ───────────────────────────────────────────────────────────── */
    .public-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 2rem 1.5rem;
      text-align: center;
      box-shadow: var(--shadow);
      position: relative;
      overflow: hidden;
    }
    .public-header::before {
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

    .header-title {
      font-size: 3.5rem;
      font-weight: 900;
      text-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);
      margin-bottom: 0.5rem;
      letter-spacing: 2px;
    }
    .header-subtitle {
      font-size: 1.5rem;
      font-weight: 400;
      opacity: 0.9;
      letter-spacing: 1px;
    }

    /* ─── MAIN CONTAINER ───────────────────────────────────────────────────── */
    .main-container {
      padding: 2rem;
      display: grid;
      gap: 2rem;
      grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
      max-width: 1400px;
      margin: 0 auto;
    }

    /* ─── WINDOW CARD ──────────────────────────────────────────────────────── */
    .window-card {
      background: var(--surface);
      border-radius: 20px;
      box-shadow: var(--shadow);
      border: 1px solid var(--border);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      display: grid;
      grid-template-rows: auto auto 1fr;
      min-height: 600px;
    }
    .window-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 180, 103, 0.2);
    }
 .dept-buttons {
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
      justify-content: center;
      padding: 1rem;
    }
    .dept-card {
      background: var(--surface-light);
      color: var(--text);
      border-radius: 12px;
      width: 160px;
      height: 100px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 600;
      text-decoration: none;
      transition: transform .3s ease, box-shadow .3s ease;
    }
    .dept-card:hover {
      transform: translateY(-4px);
      box-shadow: 0 8px 25px rgba(0,180,103,0.2);
    }
    /* ─── WINDOW HEADER ────────────────────────────────────────────────────── */
    .window-header {
      background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
      padding: 1.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    .window-header::after {
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

    .window-name {
      font-size: 2.5rem;
      font-weight: 700;
      color: var(--text);
      text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
      margin: 0;
    }

    /* ─── NOW SERVING SECTION ──────────────────────────────────────────────── */
    .now-serving-section {
      background: var(--surface-light);
      padding: 2rem;
      text-align: center;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      gap: 1rem;
      position: relative;
      background: radial-gradient(circle at center, rgba(0, 255, 127, 0.1) 0%, transparent 70%);
    }

    .serving-label {
      font-size: 1.5rem;
      font-weight: 600;
      color: var(--text-muted);
      text-transform: uppercase;
      letter-spacing: 2px;
      display: flex;
      align-items: center;
      gap: 0.75rem;
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

    .serving-code {
      font-size: 5rem;
      font-weight: 900;
      letter-spacing: 3px;
      color: var(--accent);
      text-shadow: 0 0 30px rgba(0, 255, 127, 0.5);
      animation: glow 3s ease-in-out infinite alternate;
      position: relative;
      transition: transform 0.3s ease;
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
      top: -15px; left: -15px;
      right: -15px; bottom: -15px;
      border: 2px solid var(--accent);
      border-radius: 15px;
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

    /* ─── QUEUE LIST ───────────────────────────────────────────────────────── */
    .queue-section {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 1rem;
      flex: 1;
    }

    .queue-header {
      text-align: center;
      font-size: 1.3rem;
      font-weight: 700;
      color: var(--accent);
      margin-bottom: 1rem;
      text-transform: uppercase;
      letter-spacing: 2px;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
    }

    .queue-list {
      display: flex;
      flex-direction: column;
      gap: 0.75rem;
      flex: 1;
    }

    .queue-slot {
      background: linear-gradient(135deg, var(--surface-light) 0%, var(--surface) 100%);
      border-radius: 12px;
      padding: 1rem 1.5rem;
      display: grid;
      grid-template-columns: 50px 1fr;
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
    .queue-slot.empty {
      opacity: 0.3;
    }

    .queue-number {
      background: linear-gradient(135deg, var(--accent) 0%, #00cc66 100%);
      color: var(--bg);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.1rem;
    }

    .queue-code {
      font-size: 1.8rem;
      font-weight: 700;
      letter-spacing: 1px;
      color: var(--text);
      text-shadow: 0 0 8px rgba(0, 0, 0, 0.3);
    }

    .empty-state {
      text-align: center;
      color: var(--text-muted);
      font-style: italic;
      padding: 2rem;
      font-size: 1.1rem;
    }

    /* ─── TIMESTAMP ────────────────────────────────────────────────────────── */
    .timestamp {
      background: var(--surface-light);
      padding: 0.75rem 1.5rem;
      text-align: center;
      font-size: 1rem;
      font-weight: 500;
      color: var(--text-muted);
      border-top: 1px solid var(--border);
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
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
      .header-title {
        font-size: 2.5rem;
      }
      .header-subtitle {
        font-size: 1.2rem;
      }
      .main-container {
        padding: 1rem;
        grid-template-columns: 1fr;
        gap: 1.5rem;
      }
      .window-name {
        font-size: 2rem;
      }
      .serving-code {
        font-size: 4rem;
      }
      .queue-code {
        font-size: 1.5rem;
      }
    }

    @media (max-width: 480px) {
      .header-title {
        font-size: 2rem;
      }
      .serving-code {
        font-size: 3rem;
      }
      .queue-slot {
        padding: 0.75rem 1rem;
      }
      .queue-code {
        font-size: 1.3rem;
      }
    }

    /* ─── ANIMATIONS ───────────────────────────────────────────────────────── */
    .updating {
      opacity: 0.7;
      transform: scale(0.98);
      transition: all 0.3s ease;
    }

    .fade-in {
      animation: fadeIn 0.5s ease-in-out;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body>
  {{-- ─── LOADING OVERLAY ──────────────────────────────────────────────────── --}}
  <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
  </div>

  {{-- ─── HEADER ───────────────────────────────────────────────────────────── --}}
  <div class="public-header">
    <h1 class="header-title">
      <i class="bi bi-list-ol me-3"></i>Public Queue
    </h1>
    <p class="header-subtitle">Live Queue Status & Updates</p>
  </div>

  {{-- ─── MAIN CONTENT ─────────────────────────────────────────────────────── --}}
  <div class="main-container">
    @forelse($windows as $window)
      <div class="window-card fade-in" data-window-id="{{ $window->id }}">
        {{-- Window Header --}}
        <div class="window-header">
          <h2 class="window-name">{{ $window->short_name ?? $window->name }}</h2>
        </div>

          <div class="dept-buttons">
          @foreach($window->children as $dept)
            <a href="{{ route('queue.display', $dept) }}" class="dept-card">
              <i class="bi bi-person-lines-fill me-2"></i>
              {{ $dept->short_name ?? $dept->name }}
            </a>
          @endforeach
        </div>

        {{-- Now Serving Section --}}
        <div class="now-serving-section">
          <div class="serving-label">
            <div class="status-indicator"></div>
            <span>Now Serving</span>
          </div>
          <div class="serving-code" data-serving-code="{{ $window->id }}">
            {{ $currentServing[$window->id] ?? '—' }}
          </div>
        </div>

        {{-- Queue List Section --}}
        <div class="queue-section">
          <div class="queue-header">
            <i class="bi bi-hourglass-split me-1"></i> Next Up
          </div>
          
          <div class="queue-list" data-queue-list="{{ $window->id }}">
            @php
              $pendingTokens = $pending[$window->id] ?? collect();
            @endphp
            
            @if($pendingTokens->isEmpty())
              <div class="empty-state">
                <i class="bi bi-info-circle me-2"></i>
                No tokens in queue
              </div>
            @else
              @foreach($pendingTokens->take(5) as $index => $token)
                <div class="queue-slot">
                  <div class="queue-number">{{ $index + 1 }}</div>
                  <div class="queue-code">{{ $token->code }}</div>
                </div>
              @endforeach
              
              {{-- Fill remaining slots with empty ones --}}
              @for($i = $pendingTokens->count(); $i < 5; $i++)
                <div class="queue-slot empty">
                  <div class="queue-number">{{ $i + 1 }}</div>
                  <div class="queue-code">—</div>
                </div>
              @endfor
            @endif
          </div>
        </div>

        {{-- Timestamp --}}
        <div class="timestamp" data-timestamp="{{ $window->id }}">
          <i class="bi bi-clock me-2"></i>
          <span>{{ now()->format('H:i:s, d M Y') }} | Live Updates</span>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="window-card">
          <div class="empty-state">
            <i class="bi bi-exclamation-triangle me-2"></i>
            No active queues available
          </div>
        </div>
      </div>
    @endforelse
  </div>

  {{-- ─── BOOTSTRAP JS ─────────────────────────────────────────────────────── --}}
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

  {{-- ─── LIVE POLLING SCRIPT ──────────────────────────────────────────────── --}}
  <script>
    const windows = @json($windows->pluck('id'));
    const statusUrlBase = "{{ url('queues') }}/";
    let isRefreshing = false;

    async function refreshQueues() {
      if (isRefreshing) return;
      isRefreshing = true;

      const loadingOverlay = document.getElementById('loadingOverlay');
      
      try {
        for (let windowId of windows) {
          const response = await fetch(statusUrlBase + windowId + '/status');
          if (!response.ok) continue;
          
          const data = await response.json();
          const pending = data.pending || [];
          const allCodes = data.all_codes || [];
          const currentServing = allCodes[0] || '—';
          
          // Update "Now Serving" with animation
          const servingEl = document.querySelector(`[data-serving-code="${windowId}"]`);
          if (servingEl && servingEl.innerText !== currentServing) {
            servingEl.style.transform = 'scale(0.8)';
            setTimeout(() => {
              servingEl.innerText = currentServing;
              servingEl.style.transform = 'scale(1)';
            }, 200);
          }

          // Update queue list with animation
          const queueListEl = document.querySelector(`[data-queue-list="${windowId}"]`);
          if (queueListEl) {
            queueListEl.classList.add('updating');
            
            setTimeout(() => {
              // Clear existing content
              queueListEl.innerHTML = '';
              
              if (pending.length === 0) {
                queueListEl.innerHTML = `
                  <div class="empty-state">
                    <i class="bi bi-info-circle me-2"></i>
                    No tokens in queue
                  </div>
                `;
              } else {
                // Show up to 5 pending tokens
                const tokensToShow = pending.slice(0, 5);
                
                tokensToShow.forEach((token, index) => {
                  queueListEl.insertAdjacentHTML('beforeend', `
                    <div class="queue-slot">
                      <div class="queue-number">${index + 1}</div>
                      <div class="queue-code">${token.code}</div>
                    </div>
                  `);
                });
                
                // Fill remaining slots with empty ones
                for (let i = tokensToShow.length; i < 5; i++) {
                  queueListEl.insertAdjacentHTML('beforeend', `
                    <div class="queue-slot empty">
                      <div class="queue-number">${i + 1}</div>
                      <div class="queue-code">—</div>
                    </div>
                  `);
                }
              }
              
              queueListEl.classList.remove('updating');
            }, 300);
          }

          // Update timestamp
          const timestampEl = document.querySelector(`[data-timestamp="${windowId}"] span`);
          if (timestampEl) {
            const now = new Date();
            const timeString = now.toLocaleString(undefined, {
              day: '2-digit',
              month: 'long', 
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
              second: '2-digit'
            });
            timestampEl.innerText = timeString + ' | Live Updates';
          }
        }
      } catch (error) {
        console.error('Failed to refresh queues:', error);
      } finally {
        isRefreshing = false;
      }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', () => {
      // Initial refresh after a short delay
      setTimeout(refreshQueues, 1000);
      
      // Set up polling every 4 seconds
      setInterval(refreshQueues, 4000);
      
      // Add fade-in animation to cards
      const cards = document.querySelectorAll('.window-card');
      cards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
      });
    });

    // Handle visibility change to pause/resume polling when tab is not active
    document.addEventListener('visibilitychange', () => {
      if (!document.hidden) {
        // Resume polling when tab becomes active
        setTimeout(refreshQueues, 500);
      }
    });
  </script>
</body>
</html>