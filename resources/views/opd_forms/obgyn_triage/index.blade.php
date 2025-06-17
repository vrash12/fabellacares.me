{{-- resources/views/opd_forms/obgyn_triage/index.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
  <h1 class="h3">OB-GYN Triage Submissions</h1>
  <a href="{{ route('triage.obgyn.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> New OB-GYN Triage
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
            <td>{{ data_get($sub, 'chief_complaint', '—') }}</td>
            <td>{{ $sub->created_at->format('Y-m-d H:i') }}</td>
            <td class="text-end">
              <a href="{{ route('triage.obgyn.show', $sub) }}" class="btn btn-sm btn-secondary">View</a>
              <a href="{{ route('triage.obgyn.edit', $sub) }}" class="btn btn-sm btn-info">Edit</a>
              <form action="{{ route('triage.obgyn.destroy', $sub) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Delete this OB-GYN triage submission?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-4">No OB-GYN triage submissions yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection


{{-- resources/views/opd_forms/pedia_triage/index.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)

@section('content')
<div class="page-header mb-4 d-flex justify-content-between align-items-center">
  <h1 class="h3">Pediatric Triage Submissions</h1>
  <a href="{{ route('triage.pedia.create') }}" class="btn btn-primary">
    <i class="bi bi-plus-lg"></i> New Pedia Triage
  </a>
</div>

<div class="card shadow-sm">
  <div class="card-body p-0">
    <table class="table table-striped mb-0">
      <thead>
        <tr>
          <th>#</th>
          <th>Main Concern</th>
          <th>Created At</th>
          <th class="text-end">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($submissions as $sub)
          <tr>
            <td>{{ $sub->id }}</td>
            <td>{{ data_get($sub, 'main_concern', '—') }}</td>
            <td>{{ $sub->created_at->format('Y-m-d H:i') }}</td>
            <td class="text-end">
              <a href="{{ route('triage.pedia.show', $sub) }}" class="btn btn-sm btn-secondary">View</a>
              <a href="{{ route('triage.pedia.edit', $sub) }}" class="btn btn-sm btn-info">Edit</a>
              <form action="{{ route('triage.pedia.destroy', $sub) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Delete this pediatric triage submission?');">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center py-4">No pediatric triage submissions yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
