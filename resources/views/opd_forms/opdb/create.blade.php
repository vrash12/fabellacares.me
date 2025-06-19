{{-- resources/views/opd_forms/opdb/create.blade.php --}}
@extends('layouts.admin')

@section('content')
  <h1 class="mb-4">New OPD-OB Record</h1>

  @include('opd_forms.opdb._form', [
    'opd_form'   => null,
    'postRoute'  => route('ob-opd-forms.store'),
    'showButtons'=> true,
  ])
@endsection

@push('scripts')
<script>
  $(function(){
    $('#region').ph_address_selector({
      dataPath:   '/ph-json',
      region:     '#region',
      province:   '#province',
      city:       '#city',
      barangay:   '#barangay',
      regionText:   '#region-text',
      provinceText: '#province-text',
      cityText:     '#city-text',
      barangayText: '#barangay-text',
    });
  });
</script>
@endpush
