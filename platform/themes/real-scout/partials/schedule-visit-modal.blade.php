@php $scheduleVisitFormAction = route('public.send.consult'); @endphp
<div class="modal fade" id="scheduleVisitModal" tabindex="-1" role="dialog" aria-labelledby="scheduleVisitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="scheduleVisitModalLabel">{{ __('Schedule a Site Visit') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="{{ $scheduleVisitFormAction }}" method="post" id="schedule-visit-form" class="generic-form">
                    @csrf
                    <input type="hidden" value="project" name="type">
                    <input type="hidden" value="{{ $data->id }}" name="data_id">
                    <input type="hidden" value="Site Visit" name="interest">
                    <div class="form-group">
                        <input type="text" name="name" class="form-control" placeholder="{{ __('Name') }} *" required>
                    </div>
                    <div class="form-group">
                        <input type="text" name="phone" class="form-control" placeholder="{{ __('Phone') }} *" required>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-control" placeholder="{{ __('Email') }} *" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-sm-6">
                            <label for="preferred_visit_date">{{ __('Preferred Date') }}</label>
                            <input type="date" name="preferred_visit_date" id="preferred_visit_date" class="form-control">
                        </div>
                        <div class="form-group col-sm-6">
                            <label for="preferred_visit_time">{{ __('Preferred Time') }}</label>
                            <input type="time" name="preferred_visit_time" id="preferred_visit_time" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="content" class="form-control" rows="4" placeholder="{{ __('Anything else we should know?') }}" required>{{ __('I would like to schedule a site visit for :project.', ['project' => $data->name]) }}</textarea>
                    </div>
                    @if (setting('enable_captcha') && is_plugin_active('captcha'))
                        <div class="form-group">
                            {!! Captcha::display() !!}
                        </div>
                    @endif
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-lg btn-orange btn-block">{{ __('Request Site Visit') }}</button>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="alert alert-success text-success text-left" style="display: none;">
                        <span></span>
                    </div>
                    <div class="alert alert-danger text-danger text-left" style="display: none;">
                        <span></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    (function ($) {
        "use strict";
        $(document).ajaxSuccess(function (event, xhr, settings) {
            var isScheduleVisitRequest = settings.url === @json($scheduleVisitFormAction)
                && settings.data instanceof FormData
                && settings.data.get('interest') === 'Site Visit';

            if (isScheduleVisitRequest && xhr.responseJSON && !xhr.responseJSON.error) {
                $('#schedule-visit-form').find('input[type=date], input[type=time]').val('');
            }
        });
    })(jQuery);
</script>
