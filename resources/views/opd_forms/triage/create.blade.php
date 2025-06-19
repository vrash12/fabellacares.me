{{-- resources/views/opd_forms/triage/create.blade.php --}}
@extends('layouts.admin')

@section('content')
  @include('opd_forms.triage._form', [
    'triageForm' => $triageForm,           // null on “create”
    'postRoute'  => route('opd_forms.triage.store'),
    'method'     => 'POST',
  ])
@endsection
