<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: sans-serif; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
    th { background: #f0f0f0; }
  </style>
</head>
<body>
  <h2>OPD Patients</h2>
  <table>
    <thead>
      <tr>
        <th>#</th><th>Name</th><th>Sex</th><th>Age</th><th>Visits</th><th>Created</th>
      </tr>
    </thead>
    <tbody>
      @foreach($patients as $i => $p)
        <tr>
          <td>{{ $i+1 }}</td>
          <td>{{ $p->name }}</td>
          <td>{{ ucfirst($p->profile->sex ?? '—') }}</td>
          <td>{{ $p->profile->birth_date ? now()->diffInYears($p->profile->birth_date) : '—' }}</td>
          <td>{{ $p->visits_count }}</td>
          <td>{{ $p->created_at->format('Y-m-d') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
