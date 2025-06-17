{{-- resources/views/opd_forms/follow_up/index.blade.php --}}
@extends('layouts.encoder')

@section('content')
  <div class="page-header d-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0">
      <i class="bi bi-file-earmark-text me-2"></i>
      Follow-Up Records (OPD-F-08)
    </h1>
    <a href="{{ route('follow-up-opd-forms.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Add Follow-Up Record
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card shadow-sm">
    <div class="table-responsive p-3">
      <table id="fu-table" class="table table-bordered align-middle">
        <thead class="table-light text-center">
          <tr>
            <th>Date Created</th>
            <th>Patient</th>
            <th># Follow-Ups</th>
            <th class="text-center" style="width:160px">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($submissions as $sub)
            <tr>
              <td>{{ $sub->created_at->format('Y-m-d') }}</td>
              <td>{{ optional($sub->patient)->name ?? '—' }}</td>
              <td class="text-center">
                {{ count($sub->answers['followups'] ?? []) }}
              </td>
              <td class="text-center">
                {{-- 1) Show --}}
                <a href="{{ route('follow-up-opd-forms.show', [
                              'follow_up_opd_form' => $sub->id
                          ]) }}"
                   class="btn btn-sm btn-secondary"
                   title="View">
                  <i class="bi bi-eye"></i>
                </a>

                {{-- 2) Edit --}}
                <a href="{{ route('follow-up-opd-forms.edit', [
                              'follow_up_opd_form' => $sub->id
                          ]) }}"
                   class="btn btn-sm btn-info"
                   title="Edit">
                  <i class="bi bi-pencil-square"></i>
                </a>

                {{-- 3) Delete --}}
                <form action="{{ route('follow-up-opd-forms.destroy', [
                                      'follow_up_opd_form' => $sub->id
                                  ]) }}"
                      method="POST"
                      class="d-inline"
                      onsubmit="return confirm('Delete this record?');">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center text-muted">
                No records yet
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  @push('scripts')
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>
      $('#fu-table').DataTable({ pageLength: 10, lengthChange: false });
    </script>
  @endpush
@endsection
