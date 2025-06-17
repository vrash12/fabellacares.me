@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
    <h1 class="h3">Teens Triage Submissions</h1>
    <a href="{{ route('triage.teens.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> New Teens Triage
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Chief Complaint</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($submissions as $sub)
                    <tr>
                        <td>{{ $sub->id }}</td>
                        <td>{{ data_get($sub->answers, 'chief_complaint', '—') }}</td>
                        <td>{{ $sub->created_at->format('Y-m-d H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('triage.teens.show', $sub) }}" class="btn btn-sm btn-secondary">View</a>
                            <a href="{{ route('triage.teens.edit', $sub) }}" class="btn btn-sm btn-info">Edit</a>
                            <form action="{{ route('triage.teens.destroy', $sub) }}"
                                  method="POST"
                                  class="d-inline"
                                  onsubmit="return confirm('Delete this submission?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            No teens triage submissions yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
