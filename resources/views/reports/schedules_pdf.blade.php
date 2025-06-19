<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Work Schedules Report</title>
  <style>
    body { font-family: sans-serif; font-size: 11px; }
    h2   { margin-bottom: .4em; }
    table { width: 100%; border-collapse: collapse; margin-top: .5em; }
    th,td { border: 1px solid #000; padding: 4px; text-align: left; }
    th { background: #eee; }
  </style>
</head>
<body>
  <h2>Work Schedules Report</h2>
  <p>From: {{ $from }}  To: {{ $to }}</p>

  <table>
    <thead>
      <tr>
        <th>Department</th>
        <th>Staff Name</th>
        <th>Role</th>
        <th>Date</th>
        <th>Start Day</th>
        <th>Shift Length (hrs)</th>
      </tr>
    </thead>
    <tbody>
      @foreach($schedules as $s)
        <tr>
          <td>{{ $s->department }}</td>
          <td>{{ $s->staff_name }}</td>
          <td>{{ ucfirst($s->role) }}</td>
          <td>{{ $s->date }}</td>
          <td>{{ $s->start_day }}</td>
          <td>{{ $s->shift_length }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
