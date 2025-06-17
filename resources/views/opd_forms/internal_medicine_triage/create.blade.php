@extends('layouts.admin')

@section('content')
  @include('opd_forms.internal_medicine_triage._form', [
      'triageForm' => $triageForm ?? null,
      'postRoute'  => isset($triageForm)
                        ? route('triage.internal.update', $triageForm)
                        : route('triage.internal.store')
  ])
@endsection
