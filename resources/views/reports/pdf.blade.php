{{-- resources/views/reports/pdf.blade.php --}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Patient Report PDF</title>
  <style>
    body { font-family: sans-serif; font-size: 12px; }
    h2, h3 { margin-bottom: 0.2em; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 1em; }
    th, td { border: 1px solid #333; padding: 4px; text-align: left; }
    .no-border { border: none; }
  </style>
</head>
<body>
  <h2>Patient Report</h2>
  <p>From: {{ $from }}  To: {{ $to }}</p>

  {{-- 1) Daily Visits --}}
  <h3>Daily Patient Visits</h3>
  <table>
    <thead>
      <tr>
        <th>Date</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($visits as $v)
        <tr>
          <td>{{ $v->day }}</td>
          <td>{{ $v->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  {{-- You can add charts as images if you generate them server‐side or include inline SVGs. 
       For simplicity, we’re just listing tables here. --}}

</body>
</html>
