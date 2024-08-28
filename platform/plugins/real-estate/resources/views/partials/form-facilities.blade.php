@push('header')
    <script>
        window.trans = JSON.parse('{!! addslashes(json_encode(trans('plugins/real-estate::dashboard'))) !!}');
    </script>
@endpush

<div id="app">
    <facility
            :selected_facilities='@json($selectedFacilities)'
            :facilities='@json($facilities)'>
    </facility>
</div>
