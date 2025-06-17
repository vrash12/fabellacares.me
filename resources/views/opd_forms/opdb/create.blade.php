{{--resources/views/opd_forms/opdb/create.blade.php--}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp

@extends($layout)


@section('content')
  <h1 class="mb-4">New OPD-OB Record</h1>
@include('opd_forms.opdb._form', [
    'opd_form'  => null,
    'postRoute' => route('ob-opd-forms.store'),  {{-- must use dash ( - ) --}}
    'showButtons' => true
])
@endsection
