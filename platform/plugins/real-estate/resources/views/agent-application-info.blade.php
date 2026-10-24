@if ($application)
    <div class="row">
        <div class="col-md-6">
            <p><strong>{{ trans('plugins/real-estate::agent-application.time') }}</strong>: <i>{{ $application->created_at }}</i></p>
            <p><strong>{{ trans('core/base::tables.name') }}</strong>: <i>{{ $application->name }}</i></p>
            <p><strong>{{ trans('core/base::tables.email') }}</strong>: <i><a href="mailto:{{ $application->email }}">{{ $application->email }}</a></i></p>
            <p><strong>{{ trans('plugins/real-estate::agent-application.phone') }}</strong>: <i><a href="tel:{{ $application->phone }}">{{ $application->phone }}</a></i></p>
        </div>
        <div class="col-md-6">
            <p><strong>{{ trans('plugins/real-estate::agent-application.education_level') }}</strong>: <i>{{ $application->education_level }}</i></p>
            <p><strong>{{ trans('plugins/real-estate::agent-application.years_of_experience') }}</strong>: <i>{{ $application->years_of_experience }}</i></p>
            <p><strong>{{ trans('plugins/real-estate::agent-application.expertise') }}</strong>: <i>{{ $application->categories->pluck('name')->implode(', ') ?: '...' }}</i></p>
            <p>
                <strong>{{ trans('plugins/real-estate::agent-application.resume') }}</strong>:
                <a href="{{ route('agent-application.resume', $application->id) }}" target="_blank">
                    <i class="fa fa-file-alt"></i> {{ $application->resume_original_name }}
                </a>
            </p>
        </div>
    </div>
@endif
