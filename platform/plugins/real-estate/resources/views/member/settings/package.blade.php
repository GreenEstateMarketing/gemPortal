@extends('plugins/real-estate::member.layouts.member_skeleton')
@section('content')
    <div class="settings" id="app-real-estate">
        <div class="">
            <div class="row full-with-row">
                @include('plugins/real-estate::member.dashboard.sidebar')
                <div class="col-12 col-md-9 col-xl-10 pt-5 pr-5 sidebar-inner-pages">
                    <div class="main-dashboard-form">
                        <div class="mb-5">
                            <!-- Title -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="with-actions">{{ trans('plugins/real-estate::dashboard.packages_title') }}</h4>
                                </div>
                            </div>

                            <!-- Content -->
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <packages url="{{ route('public.member.ajax.packages') }}"
                                                subscribe_url="{{ route('public.member.ajax.package.subscribe') }}"></packages>

                        </div>
                    </div>

                    <div class="main-dashboard-form">
                        <div class="mb-5">
                            <!-- Title -->
                            <div class="row">
                                <div class="col-12">
                                    <h4 class="with-actions">{{ trans('plugins/real-estate::dashboard.transactions_title') }}</h4>
                                </div>
                            </div>

                            <!-- Content -->
                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif

                            <member url="{{ route('public.member.ajax.transaction') }}"
                            ></member>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

