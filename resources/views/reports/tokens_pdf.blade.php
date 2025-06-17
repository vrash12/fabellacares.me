<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Served-Token History</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; }
    h2   { margin-bottom: .4em; }
    table { width: 100%; border-collapse: collapse; }
    th,td { border: 1px solid #000; padding: 4px; }
  </style>
</head>
<body>
  <h2>Served-Token History</h2>
  <p>From: {{ $from }}  To: {{ $to }}</p>

  <table>
    <thead>
      <tr>
        <th>Department</th>
        <th>Token</th>
        <th>Served At</th>
      </tr>
    </thead>
    <tbody>
      @foreach($tokens as $t)
        <tr>
          <td>{{ $t->department }}</td>
          <td>{{ $t->code }}</td>
          <td>{{ $t->served_at }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
