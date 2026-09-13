@extends('plugins/real-estate::member.layouts.member_skeleton')

@section('content')
    <div class="row full-with-row">
        @include('plugins/real-estate::member.dashboard.sidebar')
        <div class="col-md-9 col-xl-10 pt-5 pr-5 sidebar-inner-pages">
            @include('plugins/real-estate::wizard.partials.ad-verification-content')
        </div>
    </div>
@stop
