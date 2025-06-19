{{-- resources/views/opd_forms/triage/index.blade.php --}}
@extends(auth()->user()->role==='encoder' ? 'layouts.encoder' : 'layouts.admin')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2>All Triage Records</h2>
    <a href="{{ route('opd_forms.triage.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-lg"></i> New Triage
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body p-0">
      <table class="table mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Patient</th>
            <th>Date</th>
            <th>Chief Complaint</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($forms as $form)
            <tr>
              <td>{{ $form->id }}</td>
              <td>{{ $form->patient->name }}</td>
              <td>{{ $form->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ data_get($form,'chief_complaint','—') }}</td>
              <td class="text-end">
                <a href="{{ route('opd_forms.triage.show',$form) }}" class="btn btn-sm btn-secondary">View</a>
                <a href="{{ route('opd_forms.triage.edit',$form) }}" class="btn btn-sm btn-info">Edit</a>
                <form action="{{ route('opd_forms.triage.destroy',$form) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Delete this record?');">
                  @csrf @method('DELETE')
                  <button class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4">No triage records yet.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">{{ $forms->links() }}</div>
@endsection
