@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
            @endif
            @endif

            @if ($showLabel && $options['label'] !== false && $options['label_show'])
                {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
            @endif

            @if ($showField)
                <div class="image-box">
                    <input type="hidden" name="{{ $name }}" value="{{ $options['value'] }}" class="image-data">
                    <input type="file" name="document2" class="image_input"  accept="image/*" style="display: none;" required>
                    <div class="preview-image-wrapper">
                        <img src="{{ RvMedia::getImageUrl($options['value'], 'file', false, RvMedia::getDefaultImage()) }}" alt="preview File" class="preview_image" >
                        <a class="btn_remove_image" title="{{ trans('core/base::forms.remove_image') }}">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                    <div class="image-box-actions">
                        <a href="#" class="custom-select-image">
                            Choose File
                        </a>
                    </div>
                </div>
                @include('core/base::forms.partials.help-block')
            @endif

            @include('core/base::forms.partials.errors')

            @if ($showLabel && $showField)
                @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif
