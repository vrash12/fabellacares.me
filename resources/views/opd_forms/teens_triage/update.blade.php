{{-- resources/views/opd_forms/teens_triage/edit.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';

    // You must have the $submission model available here.
    // If you only passed 'teensForm', update your controller to also pass 'submission' => $teens.
    //
    // Build the correct update URL, supplying the {teen} wildcard:
    $postRoute = route('triage.teens.update', ['teen' => $submission->id]);
@endphp

@extends($layout)

@section('content')
  <div class="container py-4">
    <h1 class="mb-4">Edit Teens Triage Submission #{{ $submission->id }}</h1>

    @include('opd_forms.teens_triage._form', [
      'teensForm' => $submission->answers,
      'postRoute' => $postRoute,
    ])
  </div>
@endsection
