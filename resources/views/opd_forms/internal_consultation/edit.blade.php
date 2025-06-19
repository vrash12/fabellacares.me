{{--resources/views/opd_forms/internal_consultation/edit.blade.php--}}
@extends('layouts.admin')

@section('content')
    @include('opd_forms.internal_consultation._form', [
        'consultForm' => $consultForm,
        'postRoute'   => route('triage.internal.update', $consultForm)
    ])
@endsection