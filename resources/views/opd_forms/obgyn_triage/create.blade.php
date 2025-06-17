@extends('layouts.admin')

@section('content')
  @include('opd_forms.obgyn_triage._form', [
    'triageForm' => $triageForm ?? null,
    'postRoute'  => isset($triageForm)
                     ? route('triage.obgyn.update', $triageForm)
                     : route('triage.obgyn.store')
  ])
@endsection
