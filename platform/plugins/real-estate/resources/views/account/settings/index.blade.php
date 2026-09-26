@extends('plugins/real-estate::account.layouts.skeleton')
@section('content')
  <div class="settings crop-avatar">
    <div class="">
      <div class="row full-with-row">
        @include('plugins/real-estate::account.dashboard.sidebar')
        <div class="col-12 col-md-9 col-xl-10 pt-5 pr-5 mb-5 sidebar-inner-pages">
            <div class="main-dashboard-form">
                <!-- Setting Title -->
                <div class="row">
                    <div class="col-12">
                        <h4 class="with-actions">{{ trans('plugins/real-estate::dashboard.account_field_title') }}</h4>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-4 order-lg-12">
{{--                        <form id="avatar-upload-form" enctype="multipart/form-data" action="javascript:void(0)" onsubmit="return false">--}}
                            <div class="avatar-upload-container">
                                <div class="form-group">
                                    <label for="account-avatar">{{ trans('plugins/real-estate::dashboard.profile-picture') }}</label>
                                    <div id="account-avatar">
                                        <div class="profile-image">
                                            <div class="avatar-view mt-card-avatar">
                                                @if($user->image_path)
                                                    <img class="br2" src="/storage/{{ $user->image_path }}" alt="Image">
                                                @else
                                                    <img class="br2" src="{{ $user->avatar_url }}" style="width: 200px;">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Print messages -->
                                <div id="print-msg" class="alert dn"></div>
                            </div>
{{--                        </form>--}}
                    </div>
                    <div class="col-lg-8 order-lg-0">
                        @if (session('status'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('status') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif
                        <form action="{{ route('public.account.post.settings') }}" id="setting-form" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Name -->
                            <div class="form-group">
                                <label for="first_name">{{ trans('plugins/real-estate::dashboard.first_name') }}</label>
                                <input type="text" class="form-control" name="first_name" id="first_name" required value="{{ old('first_name') ?? $user->first_name }}">
                            </div>
                            <!-- Name -->
                            <div class="form-group">
                                <label for="last_name">{{ trans('plugins/real-estate::dashboard.last_name') }}</label>
                                <input type="text" class="form-control" name="last_name" id="last_name" required value="{{ old('last_name') ?? $user->last_name }}">
                            </div>
                            <div class="form-group">
                                <label for="username">{{ trans('plugins/real-estate::dashboard.username') }}</label>
                                <input type="text" class="form-control" name="username" id="username" required value="{{ old('username') ?? $user->username }}">
                            </div>
                            <!-- Phone -->
                            <div class="form-group">
                                <label for="phone">{{ trans('plugins/real-estate::dashboard.phone') }}</label>
                                <input type="text" class="form-control" name="phone" id="phone" required value="{{ old('phone') ?? $user->phone }}">
                            </div>
                            <!--Short description-->
                            <div class="form-group">
                                <label for="description">{{ trans('plugins/real-estate::dashboard.description') }}</label>
                                <textarea class="form-control" name="description" id="description" rows="3" maxlength="300" placeholder="{{ trans('plugins/real-estate::dashboard.description_placeholder') }}">{{ old('description') ?? $user->description }}</textarea>
                            </div>
                            <!-- Email -->
                            <div class="form-group">
                                <label for="email">{{ trans('plugins/real-estate::dashboard.email') }}</label>
                                <input type="email" class="form-control" name="email" id="email" disabled="disabled" placeholder="{{ trans('plugins/real-estate::dashboard.email_placeholder') }}" required value="{{ old('email') ?? $user->email }}">
                                @if ($user->confirmed_at)
                                    <small class="f7 green">{{ trans('plugins/real-estate::dashboard.verified') }}<i class="ml1 far fa-check-circle"></i></small>
                                @else
                                    <small class="f7">{{ trans('plugins/real-estate::dashboard.verify_require_desc') }}<a href="{{ route('public.account.resend_confirmation', ['email' => $user->email]) }}" class="ml1">{{ trans('plugins/real-estate::dashboard.resend') }}</a></small>
                                @endif
                            </div>
                            <!-- Birthday -->
                            <div class="form-group">
                                <label for="dob">{{ trans('plugins/real-estate::dashboard.birthday') }}</label>
                                <div class="birthday-box">
                                    <select id="year" name="year" class="form-control{{ $errors->has('year') ? ' is-invalid' : '' }}" style="width: 74px!important; display: inline-block!important;" onchange="changeYear(this)"></select>
                                    <select id="month" name="month" class="form-control{{ $errors->has('month') ? ' is-invalid' : '' }}" style="width: 90px!important; display: inline-block!important;" onchange="changeMonth(this)"></select>
                                    <select id="day" name="day" class="form-control{{ $errors->has('day') ? ' is-invalid' : '' }}" style="width: 74px!important; display: inline-block!important;"></select>
                                    <span class="invalid-feedback">
                    <strong>{{ $errors->has('dob') ? $errors->first('dob') : '' }}</strong>
                  </span>
                                </div>
                            </div>
                            <!-- Years of experience -->
                            <div class="form-group">
                                <label for="years_of_experience">{{ trans('plugins/real-estate::dashboard.years_of_experience') }}</label>
                                <input type="number" min="0" max="25" class="form-control" name="years_of_experience" id="years_of_experience" value="{{ old('years_of_experience') ?? $user->years_of_experience }}">
                            </div>
                            <!-- Spoken languages -->
                            <div class="form-group">
                                <label>{{ trans('plugins/real-estate::dashboard.languages') }}</label>
                                <div class="checkbox-group">
                                    @foreach ($spokenLanguages as $language)
                                        <label class="checkbox-inline mr-3">
                                            <input type="checkbox" name="languages[]" value="{{ $language->id }}" {{ in_array($language->id, old('languages', $selectedLanguageIds)) ? 'checked' : '' }}>
                                            {{ $language->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Specialties / property types -->
                            <div class="form-group">
                                <label>{{ trans('plugins/real-estate::dashboard.specialties') }}</label>
                                <div class="checkbox-group">
                                    @foreach ($specialtyCategories as $category)
                                        <label class="checkbox-inline mr-3">
                                            <input type="checkbox" name="specialties[]" value="{{ $category->id }}" {{ in_array($category->id, old('specialties', $selectedCategoryIds)) ? 'checked' : '' }}>
                                            {{ $category->name }}
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Gender -->
                            <div class="form-group">
                                <label for="gender">{{ trans('plugins/real-estate::dashboard.gender') }}</label>
                                <select class="form-control" name="gender" id="gender">
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>{{ trans('plugins/real-estate::dashboard.gender_male') }}</option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>{{ trans('plugins/real-estate::dashboard.gender_female') }}</option>
                                    <option value="other" {{ $user->gender == 'other' ? 'selected' : '' }}>{{ trans('plugins/real-estate::dashboard.gender_other') }}</option>
                                </select>
                            </div>
                            <!-- Signature -->
                            <div class="form-group signature-widget" id="signature-widget" data-existing="{{ $user->hasSignature() ? '1' : '0' }}">
                                <label>{{ trans('plugins/real-estate::account.signature') }}</label>

                                @if ($user->hasSignature())
                                    <div class="signature-current">
                                        <img src="{{ $user->signature_data_uri }}" alt="{{ trans('plugins/real-estate::account.signature') }}" class="signature-current-img">
                                        <small class="text-muted d-block">{{ trans('plugins/real-estate::account.signature_on_file') }}</small>
                                    </div>
                                @endif

                                <div class="signature-tabs" role="tablist">
                                    <button type="button" class="signature-tab active" data-mode="upload">{{ trans('plugins/real-estate::account.signature_upload_tab') }}</button>
                                    <button type="button" class="signature-tab" data-mode="draw">{{ trans('plugins/real-estate::account.signature_draw_tab') }}</button>
                                </div>

                                <div class="signature-pane" data-pane="upload">
                                    <input type="file" name="signature_file" id="signature_file" accept="image/png" class="form-control">
                                </div>

                                <div class="signature-pane" data-pane="draw" hidden>
                                    <canvas id="signature-canvas" width="600" height="200"></canvas>
                                    <button type="button" id="signature-clear" class="btn btn-secondary btn-sm">{{ trans('plugins/real-estate::account.signature_clear') }}</button>
                                </div>

                                <input type="hidden" name="signature_data" id="signature_data" value="">
                                <input type="hidden" name="signature_mode" id="signature_mode" value="upload">

                                <div class="signature-error text-danger" id="signature-error"></div>

                                @if ($errors->has('signature_file') || $errors->has('signature_data'))
                                    <span class="invalid-feedback d-block">
                                        <strong>{{ $errors->first('signature_file') ?: $errors->first('signature_data') }}</strong>
                                    </span>
                                @endif
                            </div>
                            <button type="submit" class="btn btn-primary fw6">{{ trans('plugins/real-estate::dashboard.save') }}</button>
                        </form>
                    </div>
            </div>
          </div>
        </div>
      </div>
    </div>
{{--    @include('plugins/real-estate::account.modals.avatar')--}}
  </div>
@endsection
@push('styles')
  <link href="{{ asset('vendor/core/plugins/real-estate/css/account-signature.css') }}" rel="stylesheet">
@endpush
@push('scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/core/core/js-validation/js/js-validation.js')}}"></script>
  {!! JsValidator::formRequest(\Botble\RealEstate\Http\Requests\SettingRequest::class); !!}
  <script type="text/javascript">
    // index => month [0-11]
    let numberDaysInMonth = [31,28,31,30,31,30,31,31,30,31,30,31];

    $(document).ready(function() {
      // Init form select
      initSelectBox();
    });

    function initSelectBox() {
      let oldBirthday = '{{ $user->dob }}';
      let selectedDay = '';
      let selectedMonth = '';
      let selectedYear = '';

      if (oldBirthday !== '') {
        selectedDay = parseInt(oldBirthday.substr(8, 2));
        selectedMonth = parseInt(oldBirthday.substr(5, 2));
        selectedYear = parseInt(oldBirthday.substr(0, 4));
      }

      let dayOption = `<option value="">{{ trans('plugins/real-estate::dashboard.day_lc') }}</option>`;
      for (let i = 1; i <= numberDaysInMonth[0]; i++) { //add option days
        if (i === selectedDay) {
          dayOption += `<option value="${i}" selected>${i}</option>`;
        } else {
          dayOption += `<option value="${i}">${i}</option>`;
        }
      }
      $('#day').append(dayOption);

      let monthOption = `<option value="">{{ trans('plugins/real-estate::dashboard.month_lc') }}</option>`;
      for (let j = 1; j <= 12; j++) {
        if (j === selectedMonth) {
          monthOption += `<option value="${j}" selected>${j}</option>`;
        } else {
          monthOption += `<option value="${j}">${j}</option>`;
        }
      }
      $('#month').append(monthOption);

      let d = new Date();
      let yearOption = `<option value="">{{ trans('plugins/real-estate::dashboard.year_lc') }}</option>`;
      for (let k = d.getFullYear(); k >= 1918; k--) {// years start k
        if (k === selectedYear) {
          yearOption += `<option value="${k}" selected>${k}</option>`;
        } else {
          yearOption += `<option value="${k}">${k}</option>`;
        }
      }
      $('#year').append(yearOption);
    }

    function isLeapYear(year) {
      year = parseInt(year);
      if (year % 4 !== 0) {
        return false;
      }
      if (year % 400 === 0) {
        return true;
      }
      if (year % 100 === 0) {
        return false;
      }
      return true;
    }

    function changeYear(select) {
      if (isLeapYear($(select).val())) {
        // Update day in month of leap year.
        numberDaysInMonth[1] = 29;
      } else {
        numberDaysInMonth[1] = 28;
      }

      // Update day of leap year.
      let monthSelectedValue = parseInt($("#month").val());
      if (monthSelectedValue === 2) {
        let day = $('#day');
        let daySelectedValue = parseInt($(day).val());
        if (daySelectedValue > numberDaysInMonth[1]) {
          daySelectedValue = null;
        }

        $(day).empty();

        let option = `<option value="">{{ trans('plugins/real-estate::dashboard.day_lc') }}</option>`;
        for (let i = 1; i <= numberDaysInMonth[1]; i++) { //add option days
          if (i === daySelectedValue) {
            option += `<option value="${i}" selected>${i}</option>`;
          } else {
            option += `<option value="${i}">${i}</option>`;
          }
        }

        $(day).append(option);
      }
    }

    function changeMonth(select) {
      let day = $('#day');
      let daySelectedValue = parseInt($(day).val());
      let month = 0;

      if ($(select).val() !== '') {
        month = parseInt($(select).val()) - 1;
      }

      if (daySelectedValue > numberDaysInMonth[month]) {
        daySelectedValue = null;
      }

      $(day).empty();

      let option = `<option value="">{{ trans('plugins/real-estate::dashboard.day_lc') }}</option>`;

      for (let i = 1; i <= numberDaysInMonth[month]; i++) { //add option days
        if (i === daySelectedValue) {
          option += `<option value="${i}" selected>${i}</option>`;
        } else {
          option += `<option value="${i}">${i}</option>`;
        }
      }

      $(day).append(option);
    }
  </script>
  <script type="text/javascript" src="{{ asset('vendor/core/plugins/real-estate/js/account-signature.js') }}"></script>
@endpush
