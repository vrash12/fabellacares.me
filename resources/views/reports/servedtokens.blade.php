{{--resources/views/reports/servedtokens.blade.php--}}
@extends('layouts.app')

@section('content')
  <h2>Served-Token History</h2>
  <form method="get" action="{{ route('reports.servedtokens') }}">
    <label>From:</label>
    <input type="text" name="from" value="{{ old('from', $dateFrom) }}" placeholder="mm/dd/yyyy">

    <label>To:</label>
    <input type="text" name="to"   value="{{ old('to',   $dateTo) }}"   placeholder="mm/dd/yyyy">

    <button type="submit">Apply Filter</button>
  </form>

  <table>
    <thead>
      <tr>
        <th>Department</th>
        <th>Token</th>
        <th>Served At</th>
      </tr>
    </thead>
    <tbody>
      @forelse($history as $row)
        <tr>
          <td>{{ $row->queue->name }}</td>
          <td>{{ $row->code }}</td>
          <td>{{ $row->served_at->format('Y-m-d H:i') }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="3">No served-tokens found in this date range.</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
