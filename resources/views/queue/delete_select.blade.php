{{-- resources/views/queue/delete_select.blade.php --}}
@extends('layouts.admin')

@section('content')
  <div class="container py-4">
    <h1 class="mb-4">Choose a Queue to Manage</h1>
    @if($queues->isEmpty())
      <div class="alert alert-info">
        No queues found.
      </div>
    @else
      <div class="row gx-3 gy-3">
        @foreach($queues as $queue)
          <div class="col-md-4">
            <div class="card h‐100 shadow-sm">
              <div class="card-body d-flex flex-column justify-content-between">
                <h5 class="card-title">{{ $queue->name }}</h5>
                <p class="card-text text‐muted mb‐4">
                  {{ $queue->tokens()
                           ->whereNull('served_at')
                           ->count() }}
                  pending token{{ $queue->tokens()->whereNull('served_at')->count() === 1 ? '' : 's' }}
                </p>
                <a href="{{ route('queue.delete.list', $queue->id) }}"
                   class="btn btn-primary w‐100">
                  Manage Tokens
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
@endsection
