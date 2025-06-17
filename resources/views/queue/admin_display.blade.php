{{-- resources/views/queue/admin_display.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Queue – {{ $queue->short_name ?? $queue->name }} (Admin)</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  {{-- Bootstrap CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
  {{-- Bootstrap Icons --}}
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
  {{-- Select2 CSS --}}
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.4.0/dist/select2-bootstrap-5.min.css" rel="stylesheet">

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
  --border: rgba(255,255,255,0.1);
  --glow: 0 0 20px rgba(0,180,103,0.3);
  --shadow: 0 8px 32px rgba(0,0,0,0.3);
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

/* HEADER (“Team Bar”) */
.teambar {
  background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: space-between;
  box-shadow: var(--shadow);
  position: relative;
}
.teambar::before {
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
  0%   { left: -100%; }
  100% { left: 100%; }
}

.btn-back {
  background: rgba(255,255,255,0.2);
  color: #fff;
  padding: .5rem 1rem;
  border-radius: 12px;
  font-weight: 600;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: .5rem;
  transition: .3s;
}
.btn-back:hover {
  transform: translateX(-2px);
  background: rgba(255,255,255,0.3);
}

.btn-serve {
  background: linear-gradient(135deg, var(--accent) 0%, #00cc66 100%);
  color: var(--bg);
  padding: .8rem 2rem;
  font-size: 1.2rem;
  border: none;
  border-radius: 12px;
  box-shadow: var(--glow);
  transition: .3s;
}
.btn-serve:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 30px rgba(0,255,127,0.5);
}

.btn-reset {
  background: linear-gradient(135deg, #ffbe3c 0%, #ff9900 100%);
  color: var(--bg);
  padding: .8rem 1.75rem;
  border: none;
  border-radius: 12px;
  box-shadow: 0 0 20px rgba(255,190,60,0.35);
  transition: .25s;
  margin-left: 1rem;
}
.btn-reset:hover {
  transform: translateY(-2px);
  box-shadow: 0 0 32px rgba(255,190,60,0.55);
}

.logo {
  height: 60px;
  filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
}

/* NAV + TABS */
.nav-tabs .nav-link {
  font-size: 1.2rem;
  font-weight: 600;
  background: rgba(255,255,255,0.1);
  color: #fff;
  border: none;
  border-top-left-radius: .75rem;
  border-top-right-radius: .75rem;
}
.nav-tabs .nav-link.active {
  background: var(--primary);
}

.tab-content {
  margin-top: 1rem;
  background: var(--surface);
  border-radius: var(--border);
  box-shadow: var(--shadow);
  padding: 1.5rem;
  border: 1px solid var(--border);
  height: calc(100vh - 200px);
  overflow: hidden;
}

/* LAYOUT */
.layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  height: 100%;
  gap: 2px;
}

/* QUEUE LIST (Ongoing Tab, Left Column) */
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
}

.queue-slot {
  background: linear-gradient(135deg, var(--surface-light) 0%, var(--surface) 100%);
  border-radius: 16px;
  padding: 1rem 1.5rem;
  display: grid;
  grid-template-columns: 60px 1fr;
  align-items: center;
  gap: 1rem;
  transition: .3s;
  border: 1px solid var(--border);
}
.queue-slot:hover {
  transform: translateX(8px);
  box-shadow: 0 8px 25px rgba(0,180,103,0.2);
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
}
.queue-code small {
  display: block;
  font-size: .9rem;
  color: var(--text-muted);
}

/* RIGHT PANE (Ongoing Tab) */
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
  position: relative;
}

.timestamp {
  background: var(--surface-light);
  padding: 1rem 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: .75rem;
  font-size: 1.4rem;
  border-bottom: 1px solid var(--border);
}
.modal-content,
.modal-content label,
.modal-content .select2-container--bootstrap-5 .select2-selection,
.modal-content .select2-results__option {      /* dropdown list */
    color: #212529 !important;                 /* Bootstrap’s default body colour */
}

.modal-backdrop.show {
    background-color: rgba(0,0,0,0.85);   /* 85 % black */
    backdrop-filter: blur(4px);
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
  51%, 100% { opacity: .3; }
}

.now-serving {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: radial-gradient(circle at center, rgba(0,255,127,0.1) 0%, transparent 70%);
}

.serving-label {
  font-size: 2rem;
  font-weight: 600;
  color: var(--text-muted);
}

.serving-code {
  font-size: 7rem;
  font-weight: 900;
  color: var(--accent);
  position: relative;
}
.serving-code small {
  display: block;
  font-size: 1.25rem;
  color: var(--text-muted);
}

/* FINISHED LIST */
.finished-list-container {
  display: flex;
  flex-direction: column;
  height: 100%;
}
.finished-label {
  font-size: 2rem;
  font-weight: 600;
  color: var(--accent);
  margin-bottom: 1rem;
}
.finished-list {
  flex: 1;
  overflow-y: auto;
  background: var(--surface-light);
  padding: 1rem;
  border-radius: 12px;
}
.finished-item {
  display: flex;
  justify-content: space-between;
  padding: .75rem 1rem;
  border-bottom: 1px solid var(--border);
}
.finished-item:last-child {
  border-bottom: none;
}
.finished-code {
  font-weight: 700;
}
.finished-time {
  font-size: 1rem;
  color: var(--text-muted);
}

/* LOADING OVERLAY */
.loading-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(10,26,26,0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  visibility: hidden;
  transition: .3s;
}
.loading-overlay.active {
  opacity: 1;
  visibility: visible;
}
.spinner {
  width: 60px;
  height: 60px;
  border: 4px solid var(--surface);
  border-top: 4px solid var(--accent);
  border-radius: 50%;
  animation: spin 1s linear infinite;
}
@keyframes spin {
  0%   { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

  </style>

</head>
<body>
  {{-- Loading overlay --}}
  <div class="loading-overlay" id="loadingOverlay">
    <div class="spinner"></div>
  </div>

  {{-- HEADER (“Team Bar”) --}}
  <div class="teambar">
    <div class="d-flex align-items-center gap-3">
      <a href="{{ route('queue.index') }}" class="btn-back">
        <i class="bi bi-arrow-left"></i> Back
      </a>
      <a href="{{ route('queue.delete.list', $queue) }}" class="btn-back">
        <i class="bi bi-trash"></i> Manage
      </a>
      <button type="button"
              class="btn-back"
              data-bs-toggle="modal"
              data-bs-target="#addTokenModal">
        <i class="bi bi-plus-circle"></i> Add Token
      </button>
      @php
        $next = $queue->tokens()
                      ->whereNull('served_at')
                      ->orderBy('created_at')
                      ->with('submission.patient')
                      ->first();
      @endphp
      @if($next)
        <a href="{{ route('queue.tokens.edit', [$queue, $next]) }}" class="btn-back">
          <i class="bi bi-pencil-square"></i> Edit Next
        </a>
      @else
        <span class="btn-back text-muted" style="opacity:.6;cursor:not-allowed">
          <i class="bi bi-pencil-square"></i> No Token
        </span>
      @endif

      <form action="{{ route('queue.serveNext.admin', $queue) }}"
            method="POST"
            class="d-inline teambar-form">
        @csrf @method('PATCH')
        <button class="btn-serve"
                onclick="return confirm('Serve next token?');">
          <i class="bi bi-play-fill"></i> Serve Next
        </button>
      </form>

      <form action="{{ route('queue.reset', $queue) }}"
            method="POST"
            class="d-inline teambar-form"
            onsubmit="return confirm('Reset the token counter for {{ $queue->name }}?');">
        @csrf @method('PATCH')
        <button class="btn-reset">
          <i class="bi bi-arrow-counterclockwise"></i> Reset Counter
        </button>
      </form>

      @if($queue->children->isNotEmpty())
        <div class="ms-3 d-inline">
          @foreach($queue->children as $child)
            <form action="{{ route('queue.route', [$queue, $child]) }}"
                  method="POST"
                  class="d-inline teambar-form"
                  onsubmit="return confirm('Route next token to {{ $child->name }}?');">
              @csrf
              <button class="btn btn-outline-light btn-sm">
                → {{ $child->short_name }}
              </button>
            </form>
          @endforeach
        </div>
      @endif
    </div>

    <img src="{{ asset('images/fabella-logo.png') }}"
         alt="Logo"
         class="logo">
  </div>

  {{-- TABS --}}
  <div class="container-fluid p-3">
    <ul class="nav nav-tabs" id="adminQueueTabs" role="tablist">
      <li class="nav-item" role="presentation">
        <button class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#ongoing"
                type="button">Ongoing</button>
      </li>
      <li class="nav-item" role="presentation">
        <button class="nav-link"
                data-bs-toggle="tab"
                data-bs-target="#finished"
                type="button">Finished</button>
      </li>
    </ul>

    <div class="tab-content" id="adminQueueTabsContent">
      {{-- ONGOING --}}
      <div class="tab-pane fade show active" id="ongoing">
        <div class="layout">
          {{-- LEFT: Pending Tokens --}}
          <div class="queue-list">
            <div class="queue-header">
              <i class="bi bi-list-ol me-1"></i> Queue
            </div>
            <div id="queueList">
              @foreach($tokens as $i => $t)
                <div class="queue-slot">
                  <div class="queue-number">{{ $i + 1 }}</div>
                  <div class="queue-code">
                    {{ $t->code }}
                    <small>{{ $t->submission?->patient?->name }}</small>
                  </div>
                </div>
              @endforeach
              @for($j = $tokens->count(); $j < 5; $j++)
                <div class="queue-slot">
                  <div class="queue-number">{{ $j + 1 }}</div>
                  <div class="queue-code" style="opacity:.3">—</div>
                </div>
              @endfor
            </div>
          </div>

          {{-- RIGHT: Now Serving --}}
          <div class="right-pane">
            <div class="dept-header">{{ $queue->short_name ?? $queue->name }}</div>
            <div class="timestamp" id="tsLine">
              <div class="status-indicator"></div>
              <span>{{ $currentTime }} | Now Serving</span>
            </div>
            <div class="now-serving">
              <div class="serving-label">Now Serving</div>
              <div class="serving-code" id="nowCode">
                {{ $currentServing ?: '—' }}
                <small>{{ $tokens->first()?->submission?->patient?->name }}</small>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- FINISHED --}}
      <div class="tab-pane fade" id="finished">
        <div class="finished-list-container">
          <div class="finished-label">
            <i class="bi bi-clock-history me-1"></i> Completed Tokens
          </div>
          @if($finished->isEmpty())
            <div class="text-center text-muted py-5">
              <i class="bi bi-info-circle me-1"></i>No tokens have been served yet.
            </div>
          @else
            <div class="finished-list">
              @foreach($finished as $tk)
                <div class="finished-item">
                  <span class="finished-code">{{ $tk->code }}</span>
                  <span class="finished-time">
                    Served at {{ \Carbon\Carbon::parse($tk->served_at)->format('H:i:s, d M Y') }}
                  </span>
                </div>
              @endforeach
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- ADD TOKEN MODAL --}}
  <div class="modal fade" id="addTokenModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <form id="addTokenForm"
            method="POST"
            action="{{ route('queue.store', $queue) }}"
            class="modal-content">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title">Issue New Token</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <label for="patientSelect" class="form-label">Patient</label>
    <select id="patientSelect" class="form-select" style="width:100%" required>
    <option value="">‒ search patient ‒</option>
    <option value="1">Alice Lee</option>
    <option value="2">Bob Reyes</option>
</select>


        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Issue &amp; Print</button>
        </div>
      </form>
    </div>
  </div>
 <!-- jQuery, Bootstrap and Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
  // Show loading overlay on any header‐form submit
  document.querySelectorAll('.teambar-form').forEach(form => {
    form.addEventListener('submit', () => {
      document.getElementById('loadingOverlay').classList.add('active');
    });
  });

// Initialize Select2 for the patient picker when the DOM is ready
$(document).ready(function() {
  $('#patientSelect').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#addTokenModal'),
    placeholder: '‒ search patient ‒',          // keeps text visible
    allowClear: false,                          // optional
    minimumInputLength: 2,
    width: '100%',                              // makes container span full width
    ajax: {
      url: '{{ route("patients.search") }}', // URL to fetch patients
      dataType: 'json',
      delay: 250,
      data: function(params) {
        return { q: params.term };            // send the search term to server
      },
      processResults: function(data) {
        return { results: data.results };     // process the server response
      }
    }
  });
});


  });

  // Live‐polling to refresh the Queue List and “Now Serving”
  (function() {
    const statusUrl = "{{ route('queue.status', $queue) }}";
    const listEl    = document.getElementById('queueList');
    const nowEl     = document.getElementById('nowCode');
    const tsSpan    = document.querySelector('#tsLine span');
    let isBusy      = false;

    async function refreshQueue() {
      if (isBusy) return;
      isBusy = true;
      try {
        const res = await fetch(statusUrl);
        if (!res.ok) return;
        const { pending, all_codes } = await res.json();

        // Rebuild the left‐hand queue list
        listEl.innerHTML = '';
        pending.slice(0,5).forEach((p,i) => {
          listEl.insertAdjacentHTML('beforeend', `
            <div class="queue-slot">
              <div class="queue-number">${i+1}</div>
              <div class="queue-code" style="opacity:${p?1:0.3}">
                ${p.code||'—'}<small>${p.patient_name||''}</small>
              </div>
            </div>`);
        });

        // Update the “Now Serving” code
        nowEl.firstChild.nodeValue = all_codes[0] || '—';

        // Update timestamp
        const now = new Date();
        tsSpan.textContent = now.toLocaleString(undefined, {
          day:   '2-digit',
          month: 'long',
          year:  'numeric',
          hour:  '2-digit',
          minute:'2-digit',
          second:'2-digit'
        }) + ' | Now Serving';

      } catch (e) {
        console.error('Error refreshing queue:', e);
      } finally {
        isBusy = false;
      }
    }

    // Initial load and polling every 4 seconds
    setTimeout(refreshQueue, 1000);
    setInterval(refreshQueue, 4000);
  })();
</script>


</body>
</html>
