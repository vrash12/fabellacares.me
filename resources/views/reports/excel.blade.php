{{-- resources/views/reports/excel.blade.php --}}
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Patient Report Excel</title>
</head>
<body>
  <h2>Patient Report</h2>
  <p>From: {{ $from }}  To: {{ $to }}</p>

  {{-- 1) Daily Visits --}}
  <h3>Daily Patient Visits</h3>
  <table border="1" cellpadding="3" cellspacing="0">
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

  <br/>

  {{-- 2) Age Range --}}
  <h3>Age Range of Patients</h3>
  <table border="1" cellpadding="3" cellspacing="0">
    <thead>
      <tr>
        <th>Age Range</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($ageStats as $stat)
        <tr>
          <td>{{ $stat->age_range }}</td>
          <td>{{ $stat->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br/>

  {{-- 3) Gender Distribution --}}
  <h3>Gender Distribution</h3>
  <table border="1" cellpadding="3" cellspacing="0">
    <thead>
      <tr>
        <th>Sex</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($genderStats as $stat)
        <tr>
          <td>{{ $stat->sex }}</td>
          <td>{{ $stat->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br/>

  {{-- 4) Blood Type Breakdown --}}
  <h3>Blood Type Breakdown</h3>
  <table border="1" cellpadding="3" cellspacing="0">
    <thead>
      <tr>
        <th>Blood Type</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($bloodStats as $stat)
        <tr>
          <td>{{ $stat->blood_type }}</td>
          <td>{{ $stat->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <br/>

  {{-- 5) Delivery Type Breakdown --}}
  <h3>Delivery Type Breakdown</h3>
  <table border="1" cellpadding="3" cellspacing="0">
    <thead>
      <tr>
        <th>Delivery Type</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
      @foreach($deliveryStats as $stat)
        <tr>
          <td>{{ $stat->delivery_type }}</td>
          <td>{{ $stat->total }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
