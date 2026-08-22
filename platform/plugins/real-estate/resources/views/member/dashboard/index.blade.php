@extends('plugins/real-estate::member.layouts.member_skeleton')
@section('content')
    <div class="dashboard crop-avatar">
        <div class="">
            <div class="row full-with-row">
                @include('plugins/real-estate::member.dashboard.sidebar')


                <div class="col-md-9 mb-3 col-xl-10 pt-5 pr-5 sidebar-inner-pages">
                    <div class="container ">
                        @if (auth('member')->check() && !auth('member')->user()->canPost())
                            <div class="alert alert-warning">{{ trans('plugins/real-estate::package.add_credit_warning') }}
                                <a href="{{ route('public.member.packages') }}">{{ trans('plugins/real-estate::package.add_credit') }}</a>
                            </div>
                        @endif
                    </div>
                    <br>
                    {!! apply_filters(ACCOUNT_TOP_STATISTIC_FILTER, null) !!}
                    <div class="row">
                        <div class="col-md-4">
                            <a href="/member/properties?filter_table_id=plugins-real-estate-properties&class=Botble%5CRealEstate%5CTables%5CMemberPropertyTable&filter_columns%5B%5D=re_properties.moderation_status&filter_operators%5B%5D=%3D&filter_values%5B%5D=approved" class="text-decoration-none">
                                <div class="agent-primary-text">
                                    <div class="br2 pa3 mb3 card-hover card-active p-4"
                                         style="box-shadow: 0 1px 1px #ccc;">
                                        <div class="media-body">
                                            <div class="f3">
                                                <span class="fw6"><i class="far fa-check-circle"></i></span>
                                                <span class="fr">{{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::APPROVED)->count() }}</span>
                                            </div>
                                            <h5 class="black-text text-right">{{ trans('plugins/real-estate::dashboard.approved_properties') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="/member/properties?filter_table_id=plugins-real-estate-properties&class=Botble%5CRealEstate%5CTables%5CMemberPropertyTable&filter_columns%5B%5D=re_properties.moderation_status&filter_operators%5B%5D=%3D&filter_values%5B%5D=pending" class="text-decoration-none">
                                <div class="agent-primary-text">
                                    <div class="br2 pa3 bg-white mb3 card-hover p-4"
                                         style="box-shadow: 0 1px 1px #ccc;">
                                        <div class="media-body">
                                            <div class="f3">
                                                <span class="fw6"><i class="fas fa-user-clock"></i></span>
                                                <span class="fr">{{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::PENDING)->count() }}</span>
                                            </div>
                                            <h5 class="black-text text-right">{{ trans('plugins/real-estate::dashboard.pending_approve_properties') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-4">
                            <a href="/member/properties?filter_table_id=plugins-real-estate-properties&class=Botble%5CRealEstate%5CTables%5CMemberPropertyTable&filter_columns%5B%5D=re_properties.moderation_status&filter_operators%5B%5D=%3D&filter_values%5B%5D=rejected" class="text-decoration-none">
                                <div class="agent-primary-text">
                                    <div class="br2 pa3 bg-white mb3 card-hover p-4"
                                         style="box-shadow: 0 1px 1px #ccc;">
                                        <div class="media-body">
                                            <div class="f3">
                                                <span class="fw6"><i class="far fa-edit"></i></span>
                                                <span class="fr">{{ $user->properties()->where('moderation_status', \Botble\RealEstate\Enums\ModerationStatusEnum::REJECTED)->count() }}</span>
                                            </div>
                                            <h5 class="black-text text-right">{{ trans('plugins/real-estate::dashboard.rejected_properties') }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>

                    <member-log-activity default-active-tab="activity-logs"></member-log-activity>

                </div>
            </div>
        </div>
        @include('plugins/real-estate::account.modals.avatar')
    </div>
@endsection
