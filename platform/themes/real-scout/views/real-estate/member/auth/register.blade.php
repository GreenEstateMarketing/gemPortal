<div class="container">
    <!-- Modal -->
    <div class="modal fade terms-modal"  id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" >
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Terms & Conditions</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="term_condition_body">

                </div>
                <div class="modal-footer">
                    <div class="form-group">

                        <label>
                            <input type="checkbox"
                                   name="modal_terms" id="modal_terms" value="1" required /></label><label>&nbsp; I accept</label>
                        <span  style="cursor: pointer" class="red" >
                    GEM Terms & Conditions
                </span>
                        {{--{{ trans('plugins/real-estate::dashboard.gem-terms') }}--}}


                    </div>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card login-form">
                <div class="card-header-custom">
                    <h4 class="text-center">{{ trans('plugins/real-estate::dashboard.register-title') }}</h4>
                </div>
                <div class="card-body">
                     <br>
                    @include(Theme::getThemeNamespace() . '::views.real-estate.account.auth.includes.messages')
                    <form method="POST" action="{{ route('member.register.save') }}">
                        @csrf
                        <div class="form-group">
                            <input id="first_name" type="text"
                                   class="form-control{{ $errors->has('full_name') ? ' is-invalid' : '' }}"
                                   name="full_name" value="{{ old('full_name') }}" required autofocus
                                   placeholder="{{ trans('plugins/real-estate::dashboard.full_name') }}">
                            @if ($errors->has('full_name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('full_name') }}</strong>
                                    </span>
                            @endif
                        </div>

                        <div class="form-group">
                            <input id="email" type="email"
                                   class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}"
                                   name="email" value="{{ old('email') }}" required
                                   placeholder="{{ trans('plugins/real-estate::dashboard.email') }}">
                            @if ($errors->has('email'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input id="mobile_no" type="number"
                                   class="form-control{{ $errors->has('mobile_no') ? ' is-invalid' : '' }}"
                                   name="mobile_no" required
                                   placeholder="{{ trans('plugins/real-estate::dashboard.mobile_no') }}">
                            @if ($errors->has('mobile_no'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('mobile_no') }}</strong>
                                    </span>
                            @endif
                        </div>
                        <div class="form-group">
                            <input id="password" type="password"
                                   class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}"
                                   name="password" required
                                   placeholder="{{ trans('plugins/real-estate::dashboard.password') }}">
                            @if ($errors->has('password'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                            @endif
                        </div>
<!--                        <div class="form-group">
                            <input id="password-confirm" type="password" class="form-control"
                                   name="password_confirmation" required
                                   placeholder="{{ trans('plugins/real-estate::dashboard.password-confirmation') }}">
                        </div>-->
<!--                        href="{{route('gem.terms')}}"-->
                        <div class="form-group">
                            <div class="">
                                <span>
                                <input type="checkbox"
                                       name="terms" id="terms" value="1" required /></span><label>&nbsp; I accept</label>
                                <span  style="cursor: pointer" class="red" data-toggle="modal" data-target="#exampleModal">
                    GEM Term & Conditions
                </span>
                                {{--{{ trans('plugins/real-estate::dashboard.gem-terms') }}--}}

                            </div>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-blue btn-full fw6">
                                {{ trans('plugins/real-estate::dashboard.register-cta') }}
                            </button>
                        </div>

                        <div class="form-group text-center">
                            <p>{{ __('Have an account already?') }} <a href="{{ route('member.login') }}" target="_blank" class="d-block d-sm-inline-block text-sm-left text-center">{{ __('Login') }}</a></p>
                        </div>

                        <div class="text-center">
                            {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\RealEstate\Models\Member::class) !!}
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
