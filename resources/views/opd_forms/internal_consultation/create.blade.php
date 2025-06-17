
// resources/views/opd_forms/internal_consultation/create.blade.php
@extends('layouts.admin')

@section('content')
    @include('opd_forms.internal_consultation._form', [
        'consultForm' => $consultForm ?? null,
        'postRoute'   => isset($consultForm)
                            ? route('triage.internal.update', $consultForm)
                            : route('triage.internal.store')
    ])
@endsection