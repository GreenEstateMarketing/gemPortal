
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
<properties type="mapsearch" url="{{ route('public.ajax.properties') }}"  price_list="{{json_encode(getPrices())}}"  chosenfullist="{{json_encode($chosenFullArr)}}" chosenlist="{{json_encode($chosenArr)}}" cities="{{json_encode($cities)}}" category_id={{$category_id }}  parent_id={{$parent_id}} current_currency="{{CurrentCurrency()->title}}"></properties>
<br><br>

