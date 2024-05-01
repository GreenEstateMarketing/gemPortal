@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        <div {!! $options['wrapperAttrs'] !!}>
    @endif
@endif

@if ($showLabel && $options['label'] !== false && $options['label_show'])
    {!! Form::customLabel($name, $options['label'], $options['label_attr']) !!}
@endif

@if ($showField)
    {!! Form::text($name, $options['value'], $options['attr']) !!}
    @include('core/base::forms.partials.help-block')
@endif

@include('core/base::forms.partials.errors')

@if ($showLabel && $showField)
    @if ($options['wrapper'] !== false)
        </div>
    @endif
@endif

@if (setting('google_map_api_key'))
    @if(Route::current()->getName()=="public.account.properties.create")
        <input type="hidden" id="agent_area"  value="{{auth('account')->user()->getPolygon()}}" />
    @else
        <input type="hidden" id="agent_area"  value="" />
    @endif
    <div id="map-container">
    <div id="map"></div>
        <span id="messageText" ></span>
    </div>


   <!-- <div id="infowindow-content">
        <img src="" width="16" height="16" id="place-icon">
        <span id="place-name" class="title"></span><br>
        <span id="place-address"></span>
    </div>-->
    <br>
    <br>
    @include('plugins/real-estate::partials.components.google-map')
@endif
