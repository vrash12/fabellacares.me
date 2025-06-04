{{-- resources/views/components/trends/guidance.blade.php --}}
@props([
  'title',
  'subtitle' => '',
  'rules',    // array of [threshold, comparator, label, css-class]
  'delta',    // numeric delta (or % delta if percent=true)
  'percent' => false,
])

@php
  // Format helper
  $fmt = fn($v) => $percent
    ? number_format($v * 100, 1) . ' %' 
    : number_format($v, 0);

  // Rule‐matching helper
  $test = function($rule) use ($delta, $percent) {
    [$thr, $cmp] = $rule;
    if ($delta === null) return false;
    $val = $percent ? abs($delta) : $delta;
    switch ($cmp) {
      case 'gte': return $val >= $thr;
      case 'gt':  return $val >  $thr;
      case 'lte': return $val <= $thr;
      case 'lt':  return $val <  $thr;
      case 'abs': return $val >= $thr; // always absolute
    }
    return false;
  };
@endphp

<div class="card mb-4 shadow-sm">
  <div class="card-header bg-light">
    <i class="bi bi-flag me-1"></i> {{ $title }}
    @if($subtitle)
      <small class="text-muted"> {{ $subtitle }} </small>
    @endif
  </div>
  <div class="card-body p-0">
    <table class="table mb-0">
      <thead class="table-light">
        <tr>
          <th style="width:140px;">Threshold</th>
          <th>Recommended Action</th>
        </tr>
      </thead>
      <tbody>
        @foreach($rules as $r)
          @php
            [$thr, $cmp, $label, $cls] = $r;
          @endphp
          <tr class="{{ $test($r) ? $cls : '' }}">
            <td class="text-center">
              @if($cmp === 'abs')
                ± {{ $fmt($thr) }}
              @else
                {{ $cmp === 'gte' || $cmp === 'gt' ? '≥' : '≤' }}
                {{ $fmt($thr) }}
              @endif
            </td>
            <td>{{ $label }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
