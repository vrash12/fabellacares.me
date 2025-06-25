{{--resources/views/opd_forms/internal_medicine_triage/create.blade.php--}}
@php
    $layout = auth()->user()->role === 'encoder'
            ? 'layouts.encoder'
            : 'layouts.admin';
@endphp
@extends($layout)


@section('content')
  @include('opd_forms.internal_medicine_triage._form', [
      'triageForm' => $triageForm ?? null,
      'postRoute'  => isset($triageForm)
                        ? route('triage.internal.update', $triageForm)
                        : route('triage.internal.store')
  ])
@endsection
