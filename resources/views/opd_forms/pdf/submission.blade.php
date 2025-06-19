<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $submission->form->form_no }} – {{ $submission->id }}</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h1 { text-align: center; }
    .section { margin-bottom: 1em; }
    .section h2 { font-size: 14px; border-bottom: 1px solid #ccc; padding-bottom: .25em; }
    .field { margin: .25em 0; }
    .label { font-weight: bold; width: 30%; display: inline-block; vertical-align: top; }
    .value { display: inline-block; width: 65%; }
  </style>
</head>
<body>
  <h1>{{ $submission->form->name }} ({{ $submission->form->form_no }})</h1>
  <p><strong>Submitted by:</strong> {{ $submission->user->name }}  
     <strong>on</strong> {{ $submission->created_at->format('Y-m-d H:i') }}</p>

  @foreach($submission->answers as $key => $val)
    <div class="field">
      <span class="label">{{ \Str::of($key)->replace('_',' ')->title() }}:</span>
      <span class="value">
        @if(is_array($val)) {{ implode(', ', $val) }}
        @elseif($val instanceof \Illuminate\Support\Carbon) {{ $val->toDateString() }}
        @else {{ $val }} @endif
      </span>
    </div>
  @endforeach
</body>
</html>
