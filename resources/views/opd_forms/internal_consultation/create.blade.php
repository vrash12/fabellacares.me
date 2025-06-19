{{--resources/views/opd_forms/internal_consultation/create.blade.php--}}
@extends('layouts.admin')

@section('content')
    @include('opd_forms.internal_consultation._form', [
        'consultForm' => null,
        'postRoute'   => route('triage.internal.store')
    ])
@endsection