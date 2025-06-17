{{-- resources/views/queue/delete_list.blade.php --}}
@extends('layouts.admin')

@section('content')
  <div class="container py-4">
    {{-- 1) Back Link + Title --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
      <a href="{{ route('queue.delete.select') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Queue Selection
      </a>
      <h2>Delete Tokens from “{{ $queue->name }}”</h2>
      {{-- empty placeholder so title is centered --}}
      <div style="width: 180px;"></div>
    </div>

   

    {{-- 3) If no pending tokens, show a notice --}}
    @if($tokens->isEmpty())
      <div class="alert alert-info">
        There are no pending tokens in this queue.
      </div>
    @else
      {{-- 4) Table of pending tokens --}}
      <div class="table-responsive">
        <table class="table table-striped table-hover align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width: 60px;">#</th>
              <th>Token Code</th>
              <th>Requested At</th>
              <th style="width: 150px;">Actions</th>
            </tr>
          </thead>
          <tbody>
            @foreach($tokens as $idx => $token)
              <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $token->code }}</td>
                <td>{{ $token->created_at->format('M d, Y H:i:s') }}</td>
                <td>
                  <form
                    action="{{ route('queue.delete.token', ['queue' => $queue->id, 'token' => $token->id]) }}"
                    method="POST"
                    onsubmit="return confirm('Are you sure you want to delete token {{ $token->code }}?');"
                  >
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                      <i class="bi bi-trash me-1"></i>Delete
                    </button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @endif
  </div>
@endsection
