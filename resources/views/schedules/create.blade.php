{{-- resources/views/schedules/create.blade.php --}}
@extends('layouts.admin')

@section('content')
  <div class="container col-lg-6">
    <h2>Add Schedule</h2>
    <form action="{{ route('schedules.store') }}" method="POST">
      @csrf

      {{-- This is the only place we include the form‐only partial: --}}
      @include('schedules._form')

      <button class="btn btn-success mt-3">Save</button>
      <a href="{{ route('schedules.index') }}" class="btn btn-secondary mt-3">Cancel</a>
    </form>
  </div>
@endsection
