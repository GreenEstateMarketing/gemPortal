@extends('core/base::layouts.master')
@section('content')
    {!! Form::open(['route' => 'real-estate.feature-flags.post']) !!}
    <div class="max-width-1200">
        <div class="flexbox-annotated-section">
            <div class="flexbox-annotated-section-annotation">
                <div class="annotated-section-title pd-all-20">
                    <h2>Feature Flags</h2>
                </div>
                <div class="annotated-section-description pd-all-20 p-none-t">
                    <p class="color-note">Enable or disable experimental behaviors. Flags are off by default.</p>
                </div>
            </div>

            <div class="flexbox-annotated-section-content">
                <div class="wrapper-content pd-all-20">
                    @foreach ($flags as $key => $flag)
                        <div class="form-group @if (!$loop->last) mb-20 @else mb-0 @endif">
                            <input type="hidden" name="feature_flag_{{ $key }}" value="0">
                            <label>
                                <input type="checkbox" class="hrv-checkbox" value="1"
                                       @if (setting('feature_flag_' . $key)) checked @endif
                                       name="feature_flag_{{ $key }}" id="feature_flag_{{ $key }}">
                                {{ $flag['name'] }}
                            </label>
                            @if (! empty($flag['description']))
                                <p class="color-note">{{ $flag['description'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="flexbox-annotated-section" style="border: none">
            <div class="flexbox-annotated-section-annotation">
                &nbsp;
            </div>
            <div class="flexbox-annotated-section-content">
                <button class="btn btn-info" type="submit">{{ trans('core/setting::setting.save_settings') }}</button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
