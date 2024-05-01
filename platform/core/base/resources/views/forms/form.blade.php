@extends('core/base::layouts.master')
@section('content')
    @if ($showStart)
        {!! Form::open(Arr::except($formOptions, ['template'])) !!}
    @endif
{{-- Hiding admin language Notification --}}
    {{--@php
        do_action(BASE_ACTION_TOP_FORM_CONTENT_NOTIFICATION, request(), $form->getModel())
    @endphp --}}
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


        </div>

    </div>
    <div class="row">

    <div class="col-md-12 right-sidebar">

        {{-- Hiding Admin Language --}}
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
        @foreach ($form->getMetaBoxes() as $key => $metaBox)
            {!! $form->getMetaBox($key) !!}
        @endforeach
        @if(Route::current()->getName() == 'property.create' || Route::current()->getName() == 'property.edit' )

            <input type="hidden" id="super_user" name="super_user" value="{{Auth()->user()->super_user}}" />
            <input type="hidden" id="json_documents" name="json_documents" value="{{$form->getModel()->documents?:""}}" />

            <input type="hidden" id="category_id" name="category_id" value="{{$form->getModel()->category_id}}" />
            <input type="hidden" id="category_name" name="category_name" value="{{$form->getModel()->category->name}}" />
            <input type="hidden" id="type" name="type" value="{{$form->getModel()->type}}" />
            <input type="hidden" id="template_desp" name="template_desp" value="" />
        @endif

        @php do_action(BASE_ACTION_META_BOXES, 'advanced', $form->getModel()) @endphp
        @if(Route::current()->getName() != 'consult.edit')

        {!! $form->getActionButtons() !!}
        @endif
        @if ($showEnd)
            {!! Form::close() !!}
        @endif

        @yield('form_end')
    <!-- comments form admin-->
<!--        -->
            @if(Route::current()->getName() == 'property.edit')

                <div class="widget meta-boxes">
                    <div class="widget-title">
                        <h4>
                            <span> Comments</span>
                        </h4>
                    </div>
                    <div class="widget-body">
                        {!!Theme::partial('admin_comment', ['properties' => $form->getModel()]) !!}

                    </div>
                </div>
            @endif
    </div>

    </div>


@stop

@if ($form->getValidatorClass())
    @if ($form->isUseInlineJs())
        {!! Assets::scriptToHtml('jquery') !!}
        {!! Assets::scriptToHtml('form-validation') !!}
        {!! $form->renderValidatorJs() !!}
    @else
        @push('footer')
            {!! $form->renderValidatorJs() !!}
        @endpush
    @endif
@endif
