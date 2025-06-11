{{--  HS-STYLE GUIDANCE CARD  --}}
@props([
  'title',
  'subtitle' => '',
  'rules',          // [threshold, comparator, label, css-class]
  'delta',
  'percent' => false,
])

@php
  /* ─── helpers ─────────────────────────────────────────── */
  $fmt  = fn($v) => $percent ? number_format($v*100,1).'%' : number_format($v,0);
  $hit  = function($r) use($delta,$percent){
            [$thr,$cmp] = $r;
            if($delta===null) return false;
            $v = $percent ? abs($delta) : $delta;
            return match($cmp) {
              'gte' => $v >= $thr,
              'gt'  => $v >  $thr,
              'lte' => $v <= $thr,
              'lt'  => $v <  $thr,
              'abs' => abs($v) >= $thr,
              default => false
            };
          };
  /* a little ✨ for the header badge */
  $badge = $delta === null
          ? ['secondary','🤔','No data yet']
          : ($delta > 0
              ? ['danger','📈','Increase']
              : ($delta < 0
                  ? ['success','📉','Decrease']
                  : ['primary','➖','Flat']));
@endphp

<style>
  /* ───── HS card theme (scoped) ───────────────────────── */
  .hs-card  {border:0;border-radius:18px;overflow:hidden}
  .hs-head  {padding:1rem 1.25rem;background:linear-gradient(90deg,#7836ff 0%,#26c6ff 100%);
             color:#fff;display:flex;align-items:center;gap:.65rem}
  .hs-head h5{margin:0;font-weight:800;font-size:1.1rem;letter-spacing:.2px}
  .hs-head small{opacity:.85;font-size:.75rem}
  .hs-badge {font-size:.7rem;padding:.25rem .55rem;border-radius:12px}
  .table-hs thead{background:#f6f8ff;font-size:.8rem}
  .table-hs td,.table-hs th{vertical-align:middle;padding:.55rem .8rem}
  /* colour rows when they match the rule */
  .table-hs tr.bg-success  {background:#d1f9df !important}
  .table-hs tr.bg-warning  {background:#fff4d6 !important}
  .table-hs tr.bg-danger   {background:#ffd8d8 !important}
</style>

<div class="card hs-card shadow-sm mb-4">
  {{-- flashy header --}}
  <div class="hs-head">
    <span class="hs-badge bg-{{ $badge[0] }}">{{ $badge[1] }}</span>
    <div class="flex-grow-1">
      <h5>{{ $title }}</h5>
      @if($subtitle)<small>{{ $subtitle }}</small>@endif
    </div>
  </div>

  {{-- rule table --}}
  <div class="card-body p-0">
    <table class="table table-hs mb-0">
      <thead>
        <tr>
          <th style="width:140px">Threshold</th>
          <th class="w-100">What to do (in plain English 🙂)</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rules as $r)
          @php [$thr,$cmp,$label,$cls] = $r; @endphp
          <tr class="{{ $hit($r)?$cls:'' }}">
            <td class="text-center fw-bold">
              @if($cmp==='abs') ±
              @elseif(in_array($cmp,['gte','gt'])) ≥
              @else ≤ @endif
              {{ $fmt($thr) }}
            </td>
            <td>{{ $label }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
