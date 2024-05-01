@extends('plugins/real-estate::member.layouts.member_skeleton')
@section('content')
    <div class="container">
        <div class="row">

            <div class="col-md-12 col-xl-12 pt-5 pr-5">
               <!-- @if ($showStart)
                    {!! Form::open(Arr::except($formOptions, ['template'])) !!}
                @endif-->
<!--                        action="{{route('general-save-property')}}"-->
                        <form method="POST" action=""  accept-charset="UTF-8" id="
                        form_member" enctype="multipart/form-data" novalidate="novalidate">
                @csrf
                            {{-- Hiding Language Notification --}}
               {{-- @php do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel()) @endphp --}}
                <div class="row">
                    <div class="col-md-12">
                        @if ($showFields && $form->hasMainFields())
                            <div class="main-form">
                                <div class="{{ $form->getWrapperClass() }}">
                                    @foreach ($fields as $key => $field)
                                        @if ($field->getName() == $form->getBreakFieldPoint())
                                            @break
                                        @else
                                            @unset($fields[$key])
                                        @endif
                                        @if (!in_array($field->getName(), $exclude))
                                            {!! $field->render() !!}
                                            @if ($field->getName() == 'name' && defined('BASE_FILTER_SLUG_AREA'))
                                                {!! apply_filters(BASE_FILTER_SLUG_AREA, $form->getModel()) !!}
                                            @endif
                                        @endif
                                    @endforeach
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        @endif

                        @foreach ($form->getMetaBoxes() as $key => $metaBox)
                            {!! $form->getMetaBox($key) !!}
                        @endforeach
                        {{-- Hiding SEO widget -- }}
                        {{--@php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp --}}
                    </div>

                @include('plugins/real-estate::partials.form-contact_form')
                <!--<div class="col-md-3 right-sidebar">
                        {!! $form->getActionButtons() !!}
                        {{-- hiding language on right sidebar --}}
                       {{-- @php do_action(BASE_ACTION_META_BOXES, 'top', $form->getModel()) @endphp --}}
                        @foreach ($fields as $field)
                            @if (!in_array($field->getName(), $exclude))
                                <div class="widget meta-boxes">
                                    <div class="widget-title">
                                        <h4>{!! Form::customLabel($field->getName(), $field->getOption('label'), $field->getOption('label_attr')) !!}</h4>
                                    </div>
                                    <div class="widget-body">
                                        {!! $field->render([], in_array($field->getType(), ['radio', 'checkbox'])) !!}
                                    </div>
                                </div>
                            @endif
                        @endforeach

                        @php do_action(BASE_ACTION_META_BOXES, 'side', $form->getModel()) @endphp
                    </div>-->
                </div>

                @if ($showEnd)
                    {!! Form::close() !!}
                @endif

        </div>
    </div>
@stop

@push('footer')
    {!! $form->renderValidatorJs() !!}
@endpush
