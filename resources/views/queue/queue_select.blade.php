{{-- resources/views/queue/queue_select.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Select Window • Queue Display</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css"
        rel="stylesheet">

  <style>
    :root {
      --primary: #00b467;
      --bg: #061010;
      --surface: #102020;
      --surface-light: #183030;
      --accent: #00ff7f;
      --text: #ffffff;
      --text-muted: #b0b0b0;
      --shadow: 0 8px 32px rgba(0,0,0,.35);
    }
    body {
      margin: 0;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, var(--bg) 0%, #000a0a 100%);
      color: var(--text);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    h1 {
      margin-bottom: 2rem;
      font-weight: 700;
      letter-spacing: 1px;
    }
    .card-deck {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 1.5rem;
      width: 100%;
      max-width: 1000px;
      padding: 0 1.5rem;
    }
    .dept-card {
      background: linear-gradient(135deg, var(--surface) 0%, var(--surface-light) 100%);
      border: none;
      color: var(--text);
      border-radius: 1rem;
      padding: 1.5rem;
      text-align: center;
      position: relative;
      overflow: hidden;
      cursor: pointer;
      transition: transform .3s, box-shadow .3s;
      box-shadow: var(--shadow);
    }
    .dept-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 12px 40px rgba(0,255,127,.25);
    }
    .dept-card::before {
      content: '';
      position: absolute;
      top: 0; left: -100%;
      width: 100%; height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.08), transparent);
      transition: left .8s;
    }
    .dept-card:hover::before {
      left: 100%;
    }
    .dept-icon {
      font-size: 2rem;
      color: var(--accent);
      margin-bottom: .75rem;
      text-shadow: 0 0 15px rgba(0,255,127,.5);
    }
    .dept-name {
      font-size: 1.25rem;
      font-weight: 600;
      letter-spacing: .5px;
      margin-bottom: 1rem;
    }
    .mini-queue {
      background: rgba(0,0,0,0.2);
      border-radius: 0.5rem;
      padding: 0.75rem;
      text-align: left;
      font-size: 0.9rem;
      line-height: 1.4;
      margin-bottom: 1rem;
    }
    .mini-queue .serving {
      font-weight: 700;
      margin-bottom: 0.5rem;
    }
    .mini-queue .slot {
      display: flex;
      justify-content: space-between;
      margin: 0.25rem 0;
    }
    .mini-queue .slot span {
      display: inline-block;
      min-width: 2rem;
    }
    footer {
      margin-top: 3rem;
      font-size: .9rem;
      color: var(--text-muted);
    }
  </style>
</head>
<body>
  <h1>Select Window</h1>

  <div class="card-deck">
    @foreach($windows as $window)
      <div class="dept-card">
        {{-- When you click the entire card, go down to departments of this window --}}
        <div onclick="window.location='{{ route('queue.department_select', $window->id) }}'">
          <div class="dept-icon">
            <i class="bi bi-tv"></i>
          </div>
          <div class="dept-name">{{ $window->name }}</div>
        </div>

        {{-- ─────────── Mini‐Queue Display for this Window ─────────── --}}
        <div class="mini-queue">
          {{-- 1) Now Serving: --}}
          <div class="serving">
            Now Serving: 
            {{ $nowServingMap[$window->id] ?? '—' }}
          </div>

          {{-- 2) Next up to 5 pending tokens: --}}
          @if($window->tokens->isEmpty())
            <div class="slot"><span>—</span><span>&nbsp;</span></div>
            <div class="slot"><span>—</span><span>&nbsp;</span></div>
            <div class="slot"><span>—</span><span>&nbsp;</span></div>
            <div class="slot"><span>—</span><span>&nbsp;</span></div>
            <div class="slot"><span>—</span><span>&nbsp;</span></div>
          @else
            @foreach($window->tokens as $idx => $tok)
              <div class="slot">
                <span>{{ $idx + 1 }}</span>
                <span>{{ $tok->code }}</span>
              </div>
            @endforeach
            {{-- if fewer than 5, fill the rest with blank “—” rows: --}}
            @for($i = $window->tokens->count(); $i < 5; $i++)
              <div class="slot">
                <span>—</span><span>&nbsp;</span>
              </div>
            @endfor
          @endif
        </div>
        {{-- ──────────────────────────────────────────────────────────── --}}
      </div>
    @endforeach
  </div>

  <footer>
    Queue display • {{ now()->format('Y') }}
  </footer>
</body>
</html>
