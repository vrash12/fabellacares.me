{{--resources/views/opd_forms/teens_triage/create.blade.php--}}
@extends('layouts.admin')

@section('content')
  @include('opd_forms.teens_triage._form', [
    'teensForm' => $teensForm ?? null,
    'postRoute' => isset($teensForm)
      ? route('triage.teens.update', $teensForm)
      : route('triage.teens.store')
  ])
@endsection
