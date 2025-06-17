{{--resources/views/opd_forms/pedia_triage/create.blade.php--}}
@extends('layouts.admin')

@section('content')
  @include('opd_forms.pedia_triage._form', [
    'pediaForm' => $pediaForm ?? null,
    'postRoute' => isset($pediaForm)
       ? route('triage.pedia.update', $pediaForm)
       : route('triage.pedia.store')
  ])
@endsection
