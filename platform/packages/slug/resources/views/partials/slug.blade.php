@if (empty($object))
    <div class="form-group @if ($errors->has('slug')) has-error @endif">
        {!! Form::permalink('slug', old('slug'), 0, $prefix) !!}
        {!! Form::error('slug') !!}
    </div>
@else
    <div class="form-group @if ($errors->has('slug')) has-error @endif">
        {!! Form::permalink('slug', $object->slug, $object->slug_id, $prefix, SlugHelper::canPreview(get_class($object)) && $object->status != \Botble\Base\Enums\BaseStatusEnum::PUBLISHED) !!}
        {!! Form::error('slug') !!}
    </div>
@endif
<input type="hidden" name="model" value="{{ get_class($object) }}">
