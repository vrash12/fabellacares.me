{{-- resources/views/opd_forms/high_risk/edit.blade.php --}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)


@section('content')
<div class="page-header mb-4">
  <h1 class="h3">Edit High-Risk Submission #{{ $submission->id }}</h1>
</div>

@include('opd_forms.high_risk._form', [
  'opd_form'   => $submission,
  'postRoute'  => route('high-risk-opd-forms.update', $submission),
  'showButtons'=> true,
  'needPut'    => true,
])
@endsection
