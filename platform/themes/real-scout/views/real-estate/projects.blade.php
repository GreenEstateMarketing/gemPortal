
<section class="inner-page">
<!--<div class="bgheadproject hidden-xs">
        <div class="description">
            <div class="container-fluid w90">
                <h1 class="text-center">{{ __('Properties') }}</h1>
                <p class="text-center">{{ theme_option('properties_description') }}</p>
                {!! Theme::partial('breadcrumb') !!}
    </div>
</div>
</div>-->



</section>

<!-- <welcome type="mapsearch" url="{{ route('public.ajax.properties') }}"  ></welcome>-->

<projects type="mapsearch" url="{{ route('public.ajax.projects') }}" cities="{{json_encode($cities)}}"  price_list="{{json_encode(getPrices())}}"   chosenlist="{{json_encode($chosenArr)}}" chosenfullist="{{json_encode($chosenFullArr)}}" parent_id={{$parent_id}}   current_currency="{{CurrentCurrency()->title}}" csrf_token="{{ csrf_token() }}"></projects>
<br><br>


