<template>
    <!-- search-->

    <div class="Properties search-component">

        <div class="modal" tabindex="-1" id="filterModal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <!--                    <div class="modal-header">
                                            <h5 class="modal-title">Modal title</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>-->
                    <p type="hidden" class="close" data-dismiss="modal" id="close" aria-label="Close">
                        <span aria-hidden="true" style="display:none" >&times;</span>
                    </p>
                    <div class="modal-body">
                        <div class="row ">
                            <div class="col-md-4">
                                <label>Property Type</label>
                                <select name="category_id"  v-model="category_id" id="category_id"  class="form-control filter-input">
                                    <option value="">Any</option>
                                    <option value="1">House</option>
                                    <option value="2">Flat</option>
                                    <option value="3">Room</option>
                                    <option value="4">Upper Portion</option>
                                    <option value="5">Lower Portion</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label>Min Price</label>
                                <select name="min_price" v-model="min_price"   class="form-control filter-input">
                                    <option value="">Any</option>
                                    <option value="5000000">5,000,000</option>
                                    <option value="6000000">6,000,000</option>
                                    <option value="7000000">7,000,000</option>
                                    <option value="8000000">8,000,000</option>
                                    <option value="9000000">9,000,000</option>
                                    <option value="10000000">10,000,000</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Max Price</label>
                                <select name="max_price" v-model="max_price"   class="form-control filter-input">
                                    <option value="">Unlimited</option>
                                    <option value="5000000">5,000,000</option>
                                    <option value="6000000">6,000,000</option>
                                    <option value="7000000">7,000,000</option>
                                    <option value="8000000">8,000,000</option>
                                    <option value="9000000">9,000,000</option>
                                    <option value="10000000">10,000,000</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <label>Beds</label>
                                <select name="bedroom" v-model="bedroom" class="form-control select-bedroom filter-input">
                                    <option value="">Any</option>
                                    <option value="1">1 room</option>
                                    <option value="2">2 rooms</option>
                                    <option value="3">3 rooms</option>
                                    <option value="4">4 rooms</option>
                                    <option value="5">5+ rooms</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Baths</label>
                                <select name="bathroom"  v-model="bathroom"   class="form-control filter-input">
                                    <option value="">Any</option>
                                    <option value="1">1 room</option>
                                    <option value="2">2 rooms</option>
                                    <option value="3">3 rooms</option>
                                    <option value="4">4 rooms</option>
                                    <option value="5">5+ rooms</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label>Land Area</label>
                                <select name="square" v-model="square"   class="form-control filter-input">
                                    <option value="">Any</option>
                                    <option value="5000">5,000</option>
                                    <option value="6000">6000</option>
                                    <option value="7000">7000</option>
                                    <option value="8000">8000</option>
                                    <option value="9000">9000</option>
                                    <option value="10000">10000</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-reset-modal" @click="resetFilters">Reset</button>
                        <button type="button" class="btn btn-info-modal" @click="getProperties" >Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="search-background">
            <div class="container">
                <!-- <form action="/properties" method="GET" id="frmhomesearch">-->

                <div class="row search-wrap">
                    <div class="search-data-wrap layout-col">
                        <div class="input-group input-container">
                            <input type="text" class="form-control"   v-model="search_data"  @change="getProperties" name="search_data"  placeholder="Enter keyword (House,Office) " id="search_data" autocomplete="off">

                            <div class="list_suggest">

                            </div>
                        </div>
                    </div>
                    <div class="location-wrap layout-col">
                        <div class="input-group input-container">
                            <input type="text" class="form-control right-border getlocation"     v-model="location" @input="getList" name="location"  placeholder="city,state" id="location" autocomplete="off">

                            <div class="list_suggest">

                            </div>
                        </div>
                    </div>
                    <div class="minprice-wrap layout-col">
                        <div class="input-group">
                            <select name="min_price" v-model="min_price" @change="getProperties" id="min-price" class="form-control left-border">
                                <option value="">Min Price</option>
                                <option value="5000000">5,000,000</option>
                                <option value="6000000">6,000,000</option>
                                <option value="7000000">7,000,000</option>
                                <option value="8000000">8,000,000</option>
                                <option value="9000000">9,000,000</option>
                                <option value="10000000">10,000,000</option>
                            </select>
                        </div>
                    </div>
                    <div class="maxprice-wrap layout-col">


                        <select name="max_price" v-model="max_price" @change="getProperties" id="max-price" class="form-control">
                            <option value="">Max Price</option>
                            <option value="5000000">5,000,000</option>
                            <option value="6000000">6,000,000</option>
                            <option value="7000000">7,000,000</option>
                            <option value="8000000">8,000,000</option>
                            <option value="9000000">9,000,000</option>
                            <option value="10000000">10,000,000</option>
                        </select>

                    </div>
                    <div class="bedroom-wrap layout-col">
                        <select name="bedroom" v-model="bedroom" @change="getProperties"  class="form-control select-bedroom">
                            <option value="">Bedrooms</option>
                            <option value="1">1 room</option>
                            <option value="2">2 rooms</option>
                            <option value="3">3 rooms</option>
                            <option value="4">4 rooms</option>
                            <option value="5">5+ rooms</option>
                        </select>
                    </div>
                    <div class="bathroom-wrap layout-col">
                        <div class="input-group  align-items-center">
                            <select name="bathroom"  v-model="bathroom" @change="getProperties" id="select-bathroom" class="form-control">
                                <option value="">Bathrooms</option>
                                <option value="1">1 room</option>
                                <option value="2">2 rooms</option>
                                <option value="3">3 rooms</option>
                                <option value="4">4 rooms</option>
                                <option value="5">5+ rooms</option>
                            </select>

                            <i class="fa fa-search active-icon" v-on:click="getProperties"></i>
                        </div>
                    </div>
                    <div class="filter-wrap align-self-center">

                        <button class="btn btn-primary"  data-toggle="modal" data-target="#filterModal" @click="openModal" type="button" >Filter <i class="fa fa-list pl-2"></i></button>
                    </div>
                </div>
                <!--</form>-->
            </div>
        </div>


        <div class="bs-example">
            <div class="btn-group btn-group-toggle toggleBtn" data-toggle="buttons">
                <label class="btn btn-primary active clickBtn" data-attr="map">
                    Map
                </label>
                <label class="btn btn-primary clickBtn" data-attr="list">
                    List
                </label>

            </div>
        </div>
        <!--<div class="modal bd-example-modal-lg" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="row mt-5">
                        <div class="col-md-3">
                            <label>Property Type</label>
                            <select name="category_id"  v-model="category_id" id="category_id"  class="form-control">
                                <option value="">Any</option>
                                <option value="1">House</option>
                                <option value="2">Flat</option>
                                <option value="3">Room</option>
                                <option value="4">Upper Portion</option>
                                <option value="5">Lower Portion</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Min Price</label>
                            <select name="min_price" v-model="min_price"   class="form-control">
                                <option value="">Any</option>
                                <option value="5000000">5,000,000</option>
                                <option value="6000000">6,000,000</option>
                                <option value="7000000">7,000,000</option>
                                <option value="8000000">8,000,000</option>
                                <option value="9000000">9,000,000</option>
                                <option value="10000000">10,000,000</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Max Price</label>
                            <select name="max_price" v-model="max_price"   class="form-control">
                                <option value="">Unlimited</option>
                                <option value="5000000">5,000,000</option>
                                <option value="6000000">6,000,000</option>
                                <option value="7000000">7,000,000</option>
                                <option value="8000000">8,000,000</option>
                                <option value="9000000">9,000,000</option>
                                <option value="10000000">10,000,000</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-md-3">
                            <label>Beds</label>
                            <select name="bedroom" v-model="bedroom" id="select-bedroom" class="form-control">
                                <option value="">Any</option>
                                <option value="1">1 room</option>
                                <option value="2">2 rooms</option>
                                <option value="3">3 rooms</option>
                                <option value="4">4 rooms</option>
                                <option value="5">5+ rooms</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Baths</label>
                            <select name="bathroom"  v-model="bathroom"   class="form-control">
                                <option value="">Any</option>
                                <option value="1">1 room</option>
                                <option value="2">2 rooms</option>
                                <option value="3">3 rooms</option>
                                <option value="4">4 rooms</option>
                                <option value="5">5+ rooms</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Land Area</label>
                            <select name="square" v-model="square"   class="form-control">
                                <option value="">Any</option>
                                <option value="5000">5,000</option>
                                <option value="6000">6000</option>
                                <option value="7000">7000</option>
                                <option value="8000">8000</option>
                                <option value="9000">9000</option>
                                <option value="10000">10000</option>
                            </select>
                        </div>
                        <div class="col-md-3">

                        </div>
                    </div>
                    <div class="row mt-5 text-center">
                        <div class="col-offset-2 col-md-3"><button type="button" class="btn btn-primary" >Reset</button></div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-info" @click="getProperties" >Search</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>-->
        <div class="layout-properties">

            <div class="properties_side_list" >
                <div class="bg-gray">
                    <div class="results">
                        <h4 class="ml-3">Results: {{links.total}} Listings</h4>
                    </div>
                    <hr>
                    <div class="sort-by">
                        <span class="ml-3 bold">Sort By</span>

                        <select @change="getProperties"  v-model="sort_by" name="sort_by" id="sort_by" class="p-1 bg-white"><option  value="default_sorting">Default</option> <option value="date_asc">Oldest</option> <option value="date_desc">Newest</option> <option value="price_asc">Price: low to high</option> <option value="price_desc">Price: high to low</option> <option value="name_asc">Name: A-Z</option> <option value="name_desc">Name: Z-A</option></select>
                    </div>
                </div>
                <hr>
                <div class="side-listing">
                    <div class="half-circle-spinner" v-if="isLoading">
                        <div class="circle circle-1"></div>
                        <div class="circle circle-2"></div>
                    </div>
                    <div v-show="show_empty_string && !isLoading && !data.length" class="col-12 text-center">
                        <span>{{ __('No property found') }}!</span>
                    </div>
<!--                    v-favorites-->
                    <div v-for="item in data" :key="item.id" v-show="!isLoading && data.length" >


                        <div class="pl-0" >
                            <div class="wishlist-wrap">
                                <a href="#"  :data-id="item.id" :title="__('I care about this property!!!')" class="text-orange heart add-to-wishlist">
                                    <i class="far fa-heart"></i>
                                </a>
                            </div>
                            <div class="d-flex justify-content-around ">
                                <a :href="item.url"> <img :data-src="item.image" :src="item.image" :alt="item.name" class="thumb img-fluid img-size"></a>

                                <div class="listing-type-side">

                                    <h6 class="mb-0">{{ item.price }}</h6>
                                    <a :href="item.url"> <p class="mb-0">{{item.city}}</p></a>
                                    <a :href="item.url"> <p class="mb-0">{{ item.location }}</p></a>
                                    <!--                                    <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Number of rooms')" v-if="item.number_bedroom"> <img src="/themes/real-scout/images/bed.svg" alt="icon">{{ item.number_bedroom }} </span>
                                                                      <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Number of rest rooms')" v-if="item.number_bathroom">  <img src="/themes/real-scout/images/bath.svg" alt="icon"> {{ item.number_bathroom }}</span>
                                                                      <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Square')" v-if="item.square"> <img src="/themes/real-scout/images/area.svg" alt="icon">{{ item.square_text }}</span>-->




                                </div>

                            </div>
                            <div class="room-info mg-left pt-3" ><b class="pr-2 bed-no">{{ item.number_bedroom }}</b><i class="fa fa-bed fa-2x bed-icon" aria-hidden="true"></i><b class="pr-2 bath-no">{{ item.number_bathroom }}</b><i class="fa fa-bath fa-2x bath-icon" aria-hidden="true"></i><i class="fa fa-building  square-icon" aria-hidden="true"></i><b class="pr-2 square-no">{{ item.square_text }}</b></div>



                        </div>
                        <hr>
                    </div>
                </div>

                <pagination :data="links" @pagination-change-page="getProperties"></pagination>

            </div>

            <div id="map-container"  style="height: 652.5px;position:relative;">
                <div id="property_search_map" style="height: 652.5px;position:relative;">

                </div>
            </div>
        </div>
        <div class="container porperties_list">
            <div class="bg-list">
                <h4 class="ml-3">Results: {{links.total}} Listings</h4>
                <span class="ml-3 bold">Sort By</span>

                <select @change="getProperties"  v-model="sort_by" style="width: 25% !important;" name="sort_by"  class="bg-white p-1 sort_by p-select-list form-control"><option  value="default_sorting">Default</option> <option value="date_asc">Oldest</option> <option value="date_desc">Newest</option> <option value="price_asc">Price: low to high</option> <option value="price_desc">Price: high to low</option> <option value="name_asc">Name: A-Z</option> <option value="name_desc">Name: Z-A</option></select>
            </div>
            <div class="row">
                <div class="half-circle-spinner" v-if="isLoading">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div v-if="show_empty_string && !isLoading && !data.length" class="col-12 text-center">
                    <span>{{ __('No property found') }}!</span>
                </div>
                <div class="col-sm-4 col-md-3 colm10" v-for="item in data" :key="item.id" v-if="!isLoading && data.length">
                    <div class="hourseitem">
                        <div class="blii">
                            <div class="img"><img class="thumb" :data-src="item.image" :src="item.image" :alt="item.name">
                            </div>
                            <a :href="item.url" class="linkdetail"></a>
                            <div class="media-count-wrapper">
                                <div class="media-count">
                                    <img ::data-src="item.image" :src="item.image" alt="media">
                                    <span>{{ item.images.length }}</span>
                                </div>
                            </div>
                            <div class="status" v-html="$sanitize(item.status_html, {allowedTags: ['span'], allowedAttributes: {'span': ['class']}})"></div>
                            <ul class="item-price-wrap hide-on-list"><li class="h-type"><span>{{ item.category_name }}</span></li> <li class="item-price">{{ item.price }}</li></ul>
                        </div>
                        <div class="info">
                            <div class="row">
                                <div class="col-md-10">
                                    <h3><a :href="item.url">{{ item.name }}</a></h3>

                                </div>
                                <div class="col-md-2">
                                    <a href="#" class="text-orange heart add-to-wishlist" :data-id="item.id" :title="__('I care about this property!!!')"><i class="far fa-heart"></i></a>
                                </div>
                            </div>
                            <p class="city"><i class="fas fa-map-marker-alt" style="opacity: 0.7"></i>&nbsp {{ item.location }}</p>
                            <p class="threemt bold500">
                            <div class="room-info pt-3" ><b class="pr-2 bed-no">{{ item.number_bedroom }}</b><i class="fa fa-bed fa-2x bed-icon" aria-hidden="true"></i><b class="pr-2 bath-no">{{ item.number_bathroom }}</b><i class="fa fa-bath fa-2x bath-icon" aria-hidden="true"></i><i class="fa fa-building  square-icon" aria-hidden="true"></i><b class="pr-2 square-no">{{ item.square_text }}</b></div>

                            <!--                                <span data-toggle="tooltip" data-placement="top" :data-original-title="__('Number of rooms')" v-if="item.number_bedroom"> <img src="/themes/real-scout/images/bed.svg" alt="icon">{{ item.number_bedroom }} </span>
                                                            <span data-toggle="tooltip" data-placement="top" :data-original-title="__('Number of rest rooms')" v-if="item.number_bathroom">  <img src="/themes/real-scout/images/bath.svg" alt="icon"> {{ item.number_bathroom }}</span>
                                                            <span data-toggle="tooltip" data-placement="top" :data-original-title="__('Square')" v-if="item.square"> <img src="/themes/real-scout/images/area.svg" alt="icon">{{ item.square_text }} </span>-->

                        </div>
                    </div>
                </div>
                <pagination :data="links" @pagination-change-page="getProperties"></pagination>
            </div>
        </div>
    </div>
</template>
<script>
import "leaflet/dist/leaflet.css";
import L from "leaflet";
import img1 from "leaflet/dist/images/marker-icon-2x.png";
import img2 from "leaflet/dist/images/marker-icon.png";
import img3 from "leaflet/dist/images/marker-shadow.png";
delete L.Icon.Default.prototype._getIconUrl;
L.Icon.Default.imagePath = '/';
L.Icon.Default.mergeOptions({
    iconRetinaUrl:img1,
    iconUrl:img2,
    shadowUrl:img3,
});
export default {
    name:'welcome',
    mapName:'property_search_map',
    map:null,
    data: function() {
        return {
            isLoading: true,
            showModal:false,
            // center: [33.74928730,72.78111600],
            location:"",
            bathroom:"",
            bedroom:"",
            floor:"",
            min_price:"",
            sort_by:"default_sorting",
            category_id:"",
            max_price:"",
            markers:[],
            search_data:"",
            square:"",
            themeUrl:"",
            city_id:"",
            markersLayer:null,
            data:[],
            links:{}
        };
    },
    mounted() {
        this.getProperties();
        //  this.initMap();
    },
    props: {
        url: {
            type: String,
            default: () => null,
            required: true
        },
        type: {
            type: String,
            default: () => 'sale',
        },
        property_id: {
            type: Number,
            default: () => null,
        },
        project_id: {
            type: Number,
            default: () => null,
        },
        show_empty_string: {
            type: Boolean,
            default: () => false
        },
    },
    methods: {
        openModal: function () {
            this.showModal = true
        },
        getProperties(page='') {
            this.data =[];
            this.links={};
            this.isLoading = true;
            let url = this.url + '?type=' + this.type;
            if (page == '') {
                page = 1;
            }
            /*if (this.type) {
                let url = this.url + '?type=' + this.type;
            }*/
            if (this.property_id) {
                url += '&property_id=' + this.property_id;
            }
            if (this.project_id) {
                url += '&project_id=' + this.project_id;
            }
            if(this.location)
            {
                url += '&location=' + this.location;
                this.location=this.location;
            }else {
                //  var url_location = new URL(location.href).searchParams.get('location')
                var url_location=location.href;
                var url_loc = url_location ? url_location.indexOf( "location" ) : -1;
                if (url_loc > 0) {
                    var url_locat = new URL(location.href).searchParams.get('location');
                    url += '&location=' + url_locat;
                    this.location = url_locat;
                }
            }
            if(this.bedroom)
            {
                url += '&bedroom=' + this.bedroom;
                this.bathroom=this.bathroom;
            }
            else
            {
                //  var url_location = new URL(location.href).searchParams.get('location')
                var url_location=location.href;
                var url_bedroom = url_location ? url_location.indexOf( "bedroom" ) : -1;
                if (url_bedroom > 0) {
                    var url_bed = new URL(location.href).searchParams.get('bedroom');
                    url += '&bedroom=' + url_bed;
                    this.bedroom = url_bed;
                }
            }
            if(this.bathroom)
            {
                url += '&bathroom=' + this.bathroom;
                this.bathroom=this.bathroom;
            }
            else
            {
                var url_location=location.href;
                var url_bathroom = url_location ? url_location.indexOf( "bathroom" ) : -1;
                if (url_bathroom > 0) {
                    var url_bath = new URL(location.href).searchParams.get('bathroom');
                    url += '&bathroom=' + url_bath;
                    this.bathroom = url_bath;
                }
            }
            if(this.floor) {
                url += '&floor=' + this.floor;
                this.floor = this.floor;
            }
            else
            {
                var url_location=location.href;
                var url_floor = url_location ? url_location.indexOf( "floor" ) : -1;
                if (url_floor > 0) {
                    var url_f = new URL(location.href).searchParams.get('floor');
                    url += '&floor=' + url_f;
                    this.floor = url_f;
                }
            }
            if(this.min_price)
            {
                url += '&min_price=' + this.min_price;
                this.min_price=this.min_price;
            }
            else
            {
                var url_location=location.href;
                var url_min = url_location ? url_location.indexOf( "min_price" ) : -1;
                if (url_min > 0) {
                    var url_mi = new URL(location.href).searchParams.get('min_price');
                    url += '&min_price=' + url_mi;
                    this.min_price = url_mi;
                }
            }
            if(this.max_price)
            {
                url += '&max_price=' + this.max_price;
                this.max_price=this.max_price;
            }
            else
            {
                var url_location=location.href;
                var url_max = url_location ? url_location.indexOf( "max_price" ) : -1;
                if (url_max > 0) {
                    var url_ma = new URL(location.href).searchParams.get('max_price');
                    url += '&max_price=' + url_ma;
                    this.max_price = url_ma;
                }
            }
            if(this.city_id)
            {
                url += '&city_id=' + this.city_id;

            }
            else
            {
                /*var url_location=location.href;
                var url_max = url_location ? url_location.indexOf( "max_price" ) : -1;
                if (url_max > 0) {
                    var url_ma = new URL(location.href).searchParams.get('max_price');
                    url += '&max_price=' + url_ma;
                    this.max_price = url_ma;
                }*/
            }
            if(this.search_data)
            {
                var manual_key=$("#search_data-tokenfield").val();
                if(manual_key!=""){
                    url += '&keyword=' +manual_key;
                }
                else {
                    url += '&keyword=' +$('#search_data').val();
                }

            }
            else
            {
                var url_location=location.href;
                var url_k = url_location ? url_location.indexOf( "k" ) : -1;
                if (url_k > 0) {
                    var url_ka = new URL(location.href).searchParams.get('k');
                    url += '&keyword=' + url_ka;
                   this.search_data =url_ka;

                }
            }
            if(this.sort_by)
            {
                url += '&sort_by=' + this.sort_by;
            }
            if(this.category_id)
            {
                url += '&category_id=' + this.category_id;
            }
            if(this.square)
            {
                url += '&square=' + this.square;
            }
            url += '&page=' +page;
            ////later added here more filters .price etc.
            axios.get(url)
                .then(res => {
                    this.data = res.data.data;
                    this.links=res.data.meta;
                    this.isLoading = false;
                    // $("#filterModal").modal("hide");
                    document.getElementById('close').click();
                    this.initMap();
                    this.insertMarkers();
                });
        },
        getList:function () {
            var k=this.location;
            let url ='/properties?k='+k ;
            /*if(this.location)
            {
                url += '&k=' + k;
            }*/
            axios.get(url)
                .then(res => {
                    console.log(res);
                    //alert(res.data.data);
                    if(res.data) {
                        $(".list_suggest").html(res.data.data);
                        $(".list_suggest").css('display', 'block !important');
                    }
                    else{
                        $(".list_suggest").css('display', 'none !important');
                    }
                });
        },
        resetFilters:function () {
            $(".filter-input").each(function () {
                $(this).val('').change();
            });
        },
        initMap:function () {
            //  this.markersLayer = new L.LayerGroup();
            /*this.map = new L.Map(
                 'property_search_map',{scrollWheelZoom: false,drawControl:true});
             this.map.doubleClickZoom.disable();
             L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                 attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
             }).addTo(this.map);*/
            /* var houston = new L.LatLng(33.3883,77.38838);
            this.map.setView(houston,8);*/
            var map = L.map("property_search_map").setView([33.78963,77.352882], 10);
            var markers=[];
            var i=0;
            this.data.forEach(data => {
                var markerLocation = new L.LatLng(data.latitude, data.longitude);
                var marker = new L.Marker(markerLocation);
                //this.markers[i++]=marker;
                this.markersLayer.addLayer(marker);
                //
                // markers.push([+data.latitude,+data.longitude]);
                markers[i]= L.marker([data.latitude,data.longitude]);
                //var marker= L.marker([data.latitude, +data.longitude]);
                //marker popup
                marker.bindPopup('<div class="infowindow-wrap d-flex" >' +
                    '<div class="img-thumbnail-info">' +
                    '<img src="'+data.image+'">'+
                    '</div>' +
                    '<div class="right-side"><div class="title-info" >' +
                    '<a href="'+data.url+'"><p>'+data.name+'</p></a>' +
                    '</div>' +
                    '<div class="location-info pt-2" >' +
                    '<a href="'+data.url+'"><p>'+data.location+'</p></a>' +
                    '</div>' +
                    '<div class="price-info pt-2" ><b>'+data.price+'</b></div></div>' +
                    '</div><div class="room-info pt-3" ><b class="pr-2 bed-no">'+data.number_bedroom+'</b><i class="fa fa-bed fa-2x bed-icon" aria-hidden="true"></i><b class="pr-2 bath-no">'+data.number_bathroom+'</b><i class="fa fa-bath fa-2x bath-icon" aria-hidden="true"></i><i class="fa fa-building  square-icon" aria-hidden="true"></i><b class="pr-2 square-no">'+data.square_text+'</b></div>' +
                    '').openPopup();
                // this.markersLayer.addLayer(marker);
            });
            console.log(markers);
            var group = L.featureGroup(markers).addTo(map);
            setTimeout(function () {
                map.fitBounds(group.getBounds());
            }, 1000);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            //  var drawnItems = new L.FeatureGroup();
            //  this.map.addLayer(drawnItems);
            /*var drawControl = new L.Control.Draw({
                draw: {
                    circle: false,
                    marker: false,
                    polyline: false
                },
                edit: {
                    featureGroup: drawnItems
                }
            });*/
            this.data.forEach(data => {
            });
            //this.map.addControl(drawControl);
        },
        insertMarkers: function () {
            // Iterate through each individual estate
            // Each estate will cr  alert("marker");eate a new marker
            //  alert("marker");
            this.markersLayer.clearLayers();
            const infowindow = new google.maps.InfoWindow({
                content: "contentString",
            });
            var i=0;
            this.data.forEach(data => {
                // alert("marker");
                var markerLocation = new L.LatLng(data.latitude, data.longitude);
                var marker = new L.Marker(markerLocation,/*{ title:data.name+' '+data.location, radius: 20 }*/);
                //  alert("marker");
                marker.bindPopup('<div class="infowindow-wrap">' +
                    '<div class="img-thumbnail pt-2">' +
                    '<img src="'+data.url+'">'+
                    '</div>' +
                    '<div class="title-info pt-2">' +
                    '<a href="'+data.url+'"><b>'+data.name+'</b></a>' +
                    '</div>' +
                    '<div class="location-info pt-2">' +
                    '<a href="'+data.url+'"><b>'+data.location+'</b></a>' +
                    '</div>' +
                    '<div class="price-info pt-2"><b>'+data.price+'</b></div>' +
                    '<div class="room-info pt-2"><img src="/themes/real-scout/images/bed.svg" class="pr-2" alt="icon"><b class="pr-2">'+data.number_bedroom+'</b><img class="pr-2" src="/themes/real-scout/images/bath.svg" alt="icon"><b class="pr-2">'+data.number_bathroom+'</b><img class="pr-2" src="/themes/real-scout/images/area.svg" alt="icon"><b class="pr-2">'+data.square_text+'</b></div>' +
                    '</div>').openPopup();
                this.markers.push([+data.latitude,+data.longitude]);
                //this.markers[i++]=marker;
                this.markersLayer.addLayer(marker);
            });
            console.log(this.markers);
            this.markersLayer.addTo(this.map);
            /* var group = new L.featureGroup([[33.67071880,72.99540520],[33.67079850,72.69535590],[33.67079850,72.99535590],[36.67079850,74.99535590]]);
             this.map.fitBounds(group.getBounds());*/
            /*var bounds = L.latLngBounds([[33.67071880,72.99540520],[33.67079850,72.69535590],[33.67079850,72.99535590],[36.67079850,74.99535590]]);
            this.map.fitBounds(bounds);*/
            //var group = new L.featureGroup(this.markers);
            // this.map.fitBounds(this.markersLayer.getBounds());
            //console.log(this.markers);
        },
       /* directives: {
            favorites: function () {
                let getCookie = function (cname) {
                    var name = cname + '=';
                    var ca = document.cookie.split(';');
                    for (var i = 0; i < ca.length; i++) {
                        var c = ca[i];
                        while (c.charAt(0) == ' ') {
                            c = c.substring(1);
                        }
                        if (c.indexOf(name) == 0) {
                            return c.substring(name.length, c.length);
                        }
                    }
                    return '';
                }
                let cookieName = window.currentLanguage + '_wishlist';
                let wishListCookies = decodeURIComponent(getCookie(cookieName));
                if (wishListCookies != null && wishListCookies != undefined && !!wishListCookies) {
                    var arrList = JSON.parse(wishListCookies);
                    var countWishlist = arrList.length;
                    $('.wishlist-count').text(countWishlist);
                    if (countWishlist > 0) {
                        $('.add-to-wishlist').removeClass('far fa-heart');
                        $.each(arrList, function (key,value) {
                            if (value != null) {
                                $(document).find(`.add-to-wishlist[data-id=${value.id}] i`).addClass('fas fa-heart');
                            }
                        });
                    }
                }
            }
        }*/
    }
}
</script>

