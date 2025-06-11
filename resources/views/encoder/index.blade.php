{{-- resources/views/encoder/index.blade.php --}}
@extends('layouts.encoder')

@section('content')
  <div class="container">
    <h1>Encoder Dashboard</h1>
    <p>Here you can see all the pending tokens, assign patients, etc.</p>

    {{-- Example table of pending tokens --}}
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Token Code</th>
          <th>Department</th>
          <th>Created At</th>
        </tr>
      </thead>
      <tbody>
        @foreach($pending as $token)
          <tr>
            <td>{{ $token->code }}</td>
            <td>{{ $token->queue->name }}</td>
            <td>{{ $token->created_at->format('Y-m-d H:i') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection
