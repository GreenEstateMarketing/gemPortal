@extends('plugins/real-estate::account.layouts.skeleton')

@section('content')
    <div class="row">
        @include('plugins/real-estate::account.dashboard.sidebar')
        <div class="col-md-9 col-xl-10 pt-5 pr-5 sidebar-inner-pages">
            @include('plugins/real-estate::wizard.index')
        </div>
    </div>
@stop
