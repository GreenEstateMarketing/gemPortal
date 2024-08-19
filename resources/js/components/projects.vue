<template>

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
                        <span aria-hidden="true" style="display:none">&times;</span>
                    </p>
                    <h3 class="ml-4">Advance Search</h3>
                    <div class="modal-body">
                        <center>
                            <div class="row ">
                                <!--                            <div class="col-md-4">
                                                                <label class="mx-left">Property Type</label>
                                                                <select name="category_id"  v-model="category_id" id="category_id"  class="form-control filter-input">
                                                                    <option value="">Any</option>
                                                                    <option value="1">House</option>
                                                                    <option value="2">Flat</option>
                                                                    <option value="3">Room</option>
                                                                    <option value="4">Upper Portion</option>
                                                                    <option value="5">Lower Portion</option>
                                                                </select>
                                                            </div>-->

                                <!--                            <div class="col-md-4">
                                                                <label class="mx-left">Min Price</label>
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
                                                                <label class="mx-left">Max Price</label>
                                                                <select name="max_price" v-model="max_price"   class="form-control filter-input">
                                                                    <option value="">Unlimited</option>
                                                                    <option value="5000000">5,000,000</option>
                                                                    <option value="6000000">6,000,000</option>
                                                                    <option value="7000000">7,000,000</option>
                                                                    <option value="8000000">8,000,000</option>
                                                                    <option value="9000000">9,000,000</option>
                                                                    <option value="10000000">10,000,000</option>
                                                                </select>
                                                            </div>-->
                                <div class="price-dropdown pl-auto  col-md-6 mt-3 border-0">
                                    <label>Price</label>
                                    <div class="dropdown input-group form-control">
                                        <!--                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                    Dropdown button
                                                                </button>-->
                                        <a class="form-control dropdown-toggle border-0 text-left" href="#"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Price <span
                                            class="currency">({{ current_currency }})</span><strong
                                            class="caret"></strong>
                                        </a>
                                        <!--<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="#">Action</a>
                                            <a class="dropdown-item" href="#">Another action</a>
                                            <a class="dropdown-item" href="#">Something else here</a>
                                        </div>-->
                                        <div class="row  price-from-to-vue modalclass p-0">
                                            <div class="col-md-5">
                                                <span class="min_price_text">{{ min_price }}</span>
                                            </div>
                                            <div class="col-md-2 price_to_text">
                                                to
                                            </div>
                                            <div class="col-md-5">
                                                <span class="max_price_text">{{ max_price }}</span>
                                            </div>
                                        </div>
                                        <div class="dropdown-menu dropdown-menu-2" style="padding:10px;width:100%">
                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <input class="form-control price-label"
                                                           style="border:1px solid #a0a0a0 !important" name="min_price"
                                                           v-model="min_price" placeholder="Min"
                                                           data-dropdown-id="price-min" value=""/>
                                                </div>

                                                <div class="col-6">
                                                    <input class="form-control price-label"
                                                           style="border:1px solid #a0a0a0 !important" name="max_price"
                                                           v-model="max_price" placeholder="Max"
                                                           data-dropdown-id="price-max" value=""/>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row mt-2 justify-content-center">
                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                        style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden; cursor:pointer">


                                                        <li class="price-li-item" :data-value="index"
                                                            v-for="(item,index)  in price_list">{{ item }}
                                                        </li>

                                                    </ul>
                                                </div>

                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-max-ul   list-unstyled"
                                                        style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden; cursor:pointer">

                                                        <li class="price-li-item" :data-value="index"
                                                            v-for="(item,index)  in price_list">{{ item }}
                                                        </li>


                                                    </ul>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-reset-price"  style="margin: 10px; height: 35px !important;">Reset</button>


                                        </div>
                                    </div>
                                </div>
                                <!--                        </div>
                                                        <div class="row mt-3">-->

                                <div class="col-md-6 mt-3"
                                     v-if="parent_id==1 || parent_id==6 || ( parent_id==0 && (category_id==1|| category_id==6))">
                                    <label class="mx-left">Flats</label>
                                    <select name="flat" v-model="flat" class="form-control select-bedroom filter-input">
                                        <option value="">Flats</option>
                                        <option value="1">1</option>
                                        <option value="10">10</option>
                                        <option value="20">20</option>
                                        <option value="30">30</option>
                                        <option value="40">40</option>
                                        <option value="50">50+</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3" v-if="parent_id==1 || ( parent_id==0 && category_id==1)">
                                    <label class="mx-left">Floors</label>
                                    <select name="floor" v-model="floor" class="form-control filter-input">
                                        <option value="">Floors</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5+</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mt-3" v-if="parent_id==1 || ( parent_id==0 && category_id==1)">
                                    <label class="mx-left">BLocks</label>
                                    <select name="blocks" v-model="blocks" class="form-control filter-input">
                                        <option value="">Any</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5+</option>
                                    </select>
                                </div>

                            </div>
                        </center>
                    </div>
                    <div class="modal-footer">

                        <button type="button" class="btn btn-reset-modal" @click="resetFilters">Reset</button>
                        <button type="button" class="btn btn-info-modal" @click="getProperties">Search</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="search-background">
            <div class="container">
                <!-- <form action="/properties" method="GET" id="frmhomesearch">-->
                <div class="row no-gutters search-wrap justify-content-between">
                    <div class="col">
                        <div id="parentChipContainer" class="parent-chip-container mr-0">
                            <div id="chipContainer">
                                <div class="position-relative input-field-container"
                                     style="max-height: 32px;max-width: 60%;">
                                    <!--                                <div class="spinner-border spinner-border-sm float-right" role="status"
                                                                         style="display:none">
                                                                        <span class="sr-only">Loading...</span>
                                                                    </div>-->
                                    <input placeholder="Keyword" class="form-control projects-keyword" type="text" name=""
                                           id="autocomplete-ajax"
                                           style="position: absolute; z-index: 2; background: transparent; width: auto !important;"/>
                                    <input class="form-control projects-keyword" type="text" name=""
                                           id="autocomplete-ajax-x"
                                           disabled="disabled"
                                           style="color: #CCC; background: transparent; z-index: 1;"/>
                                </div>
                                <div id="chipViewMore" class="chip" style="display:none">
                                    <div class="chip-content"></div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-auto">
                        <div class="" style="background-color: white;height:100%">
                            <input id="city-name-from-map" type="hidden" class="select-city-state form-control"
                                   autocomplete="off"/>
                            <select class="form-control getlocation" v-model="city_id" id='city_id' name="city_id" style="visibility: hidden">
                                <option value="">Select city...</option>
                                <option v-for="(city,index) in cities" :key="index" :value="city.id">
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-auto">
                        <div class="price-dropdown layout-col ml-0" style="font-weight: 400 !important;">
                            <div class="dropdown input-group">
                                <!--                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Dropdown button
                                                        </button>-->
                                <a class="form-control-2 dropdown-toggle border-0 text-left" href="#" data-toggle="dropdown"
                                   aria-haspopup="true" aria-expanded="false">Price <span
                                    class="currency">({{ current_currency }})</span><strong class="caret"></strong>
                                </a>
                                <!--<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item" href="#">Action</a>
                                    <a class="dropdown-item" href="#">Another action</a>
                                    <a class="dropdown-item" href="#">Something else here</a>
                                </div>-->
                                <div class="row  price-from-to-vue modalclass p-0">
                                    <div class="col-md-4">
                                        <span class="min_price_text">{{ min_price }}</span>
                                    </div>
                                    <div class="col-md-2 price_to_text">
                                        to
                                    </div>
                                    <div class="col-md-4">
                                        <span class="max_price_text">{{ max_price }}</span>
                                    </div>
                                </div>
                                <div class="dropdown-menu dropdown-menu-2" style="padding:10px;width:100%">
                                    <div class="row justify-content-center">
                                        <div class="col-6">
                                            <input class="form-control price-label"
                                                   style="border:1px solid #a0a0a0 !important" name="min_price" id="input_min_price"
                                                   v-model="min_price" placeholder="Min" data-dropdown-id="price-min"
                                                   value=""/>
                                        </div>

                                        <div class="col-6">
                                            <input class="form-control price-label"
                                                   style="border:1px solid #a0a0a0 !important" name="max_price" id="input_max_price"
                                                   v-model="max_price" placeholder="Max" data-dropdown-id="price-max"
                                                   value=""/>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="row mt-2 justify-content-center">
                                        <div class="col-md-6">
                                            <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden; cursor:pointer">


                                                <li class="price-li-item" :data-value="index"
                                                    v-for="(item,index)  in price_list">{{ item }}
                                                </li>

                                            </ul>
                                        </div>

                                        <div class="col-md-6">
                                            <ul class="price-range col-md-12 price-max-ul   list-unstyled"
                                                style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden; cursor:pointer">

                                                <li class="price-li-item" :data-value="index"
                                                    v-for="(item,index)  in price_list">{{ item }}
                                                </li>


                                            </ul>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-primary btn-reset-price"  style="margin: 10px; height: 35px !important;">Reset</button>


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-auto">
                        <i class="fa fa-search active-icon" v-on:click="getProperties(1,true)"></i>
                    </div>

                </div>
                <!--</form>-->
            </div>
        </div>


        <div class="bs-example">
            <div class="btn-group btn-group-toggle toggleBtn" data-toggle="buttons">
                <label class="btn btn-primary active clickBtn" data-attr="map" style="cursor: pointer">
                    Map
                </label>
                <label class="btn btn-primary clickBtn" data-attr="list" style="cursor: pointer">
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

            <div class="properties_side_list">
                <div class="bg-gray">
                    <div class="results">
                        <h4 class="ml-3">Results: {{ links.total }} Listings</h4>
                    </div>
                    <hr>
                    <div class="sort-by">
                        <span class="ml-3 bold">Sort By</span>

                        <select @change="getProperties" v-model="sort_by" name="sort_by" id="sort_by"
                                class="p-1 bg-white">
                            <option value="default_sorting">Default</option>
                            <option value="date_asc">Oldest</option>
                            <option value="date_desc">Newest</option>
                            <option value="price_asc">Price: low to high</option>
                            <option value="price_desc">Price: high to low</option>
                            <option value="name_asc">Name: A-Z</option>
                            <option value="name_desc">Name: Z-A</option>
                        </select>
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
                    <div v-for="item in data" :key="item.id" v-show="!isLoading && data.length">


                        <div class="pl-0">
                            <div class="wishlist-wrap">
                                <a href="#" :data-id="item.id" :title="__('I care about this project!!!')"
                                   class="text-orange heart add-to-wishlist">
                                    <i class="far fa-heart"></i>
                                </a>
                            </div>
                            <div class="d-flex justify-content-around ">
                                <a :href="item.url"> <img :data-src="item.image" :src="item.image" :alt="item.name"
                                                          class="thumb img-fluid img-size"></a>

                                <div class="listing-type-side">
                                    <h6 class="mb-0"><a :href="item.url">{{ item.name_short }}</a></h6>
                                    <p class="mb-0">{{ item.city }}</p>
                                    <p class="mb-0">{{ item.location }}</p>
                                    <!--                                    <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Number of rooms')" v-if="item.number_bedroom"> <img src="/themes/real-scout/images/bed.svg" alt="icon">{{ item.number_bedroom }} </span>
                                                                      <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Number of rest rooms')" v-if="item.number_bathroom">  <img src="/themes/real-scout/images/bath.svg" alt="icon"> {{ item.number_bathroom }}</span>
                                                                      <span data-toggle="tooltip" class="ml-1 d-inline" data-placementitle-info pt-2="top" :data-original-title="__('Square')" v-if="item.square"> <img src="/themes/real-scout/images/area.svg" alt="icon">{{ item.square_text }}</span>-->
                                </div>

                            </div>
                            <div v-if="item.category_parent_id == '1'">
                                <div class="room-info mg-left pt-3"><i class="fa fa-building fa-2x bed-icon pr-2"
                                                                       aria-hidden="true" title="flats"></i><b
                                    class="bed-no pr-2">{{ item.flat }}</b><i
                                    class="fa fa-map fa-2x bath-icon pr-2" aria-hidden="true" title="floors"></i><b
                                    class="bed-no pr-2">{{ item.floor }}</b><i class="fa fa fa-columns  square-icon pr-2" aria-hidden="true" title="blocks"></i><b class="square-no pr-2 ">{{
                                        item.block
                                    }}</b></div>
                            </div>
                            <div v-else>
                                <div class="room-info mg-left pt-3"><i class="fa fa-map fa-2x bath-icon  pr-2"
                                                                       aria-hidden="true" title="floors"></i><b
                                    class="bed-no pr-2">{{ item.floor }}</b><i class="fa fa fa-columns  square-icon pr-2" aria-hidden="true" title="blocks"></i><b class="square-no pr-2 ">{{
                                        item.block
                                    }}</b></div>
                            </div>


                        </div>
                        <hr>
                    </div>
                    <pagination :data="links" @pagination-change-page="getProperties"></pagination>
                </div>


            </div>

            <div id="map-container" style="height:708.5px;position:relative;">
                <div id="property_search_map" style="height: 708.5px;position:relative;">

                </div>
            </div>
        </div>
        <div class="container porperties_list">
            <div class="bg-list">
                <h4 class="ml-3">Results: {{ links.total }} Listings</h4>
                <div class="row d-flex">
                    <div class="col-md-6">
                        <div class="row align-items-center d-flex">
                            <div class="col-md-2"><label>Sort By:</label></div>
                            <div class="col-md-10">
                                <select @change="getProperties" v-model="sort_by" style="width: 50% !important;"
                                        name="sort_by" class="bg-white p-1 sort_by p-select-list form-control">
                                    <option value="default_sorting">Default</option>
                                    <option value="date_asc">Oldest</option>
                                    <option value="date_desc">Newest</option>
                                    <option value="price_asc">Price: low to high</option>
                                    <option value="price_desc">Price: high to low</option>
                                    <option value="name_asc">Name: A-Z</option>
                                    <option value="name_desc">Name: Z-A</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="half-circle-spinner" v-if="isLoading">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div v-if="show_empty_string && !isLoading && !data.length" class="col-12 text-center">
                    <span>{{ __('No property found') }}!</span>
                </div>
                <div class="col-sm-4 col-md-3 colm10" v-for="item in data" :key="item.id"
                     v-show="!isLoading && data.length">
                    <div class="hourseitem">
                        <div class="blii">
                            <div class="img"><img class="thumb" :data-src="item.image" :src="item.image"
                                                  :alt="item.name">
                            </div>
                            <a :href="item.url" class="linkdetail"></a>
                            <div class="media-count-wrapper">
                                <div class="media-count">

                                    <span>{{ item.images.length }}</span>
                                </div>
                            </div>
                            <div class="status"
                                 v-html="$sanitize(item.status_html, {allowedTags: ['span'], allowedAttributes: {'span': ['class']}})"></div>
                            <ul class="item-price-wrap hide-on-list">
                                <li class="h-type"><span>{{ item.category_name }}</span></li>
                                <li class="item-price">{{ item.price_from }} to {{ item.price_to }}</li>
                            </ul>
                        </div>
                        <div class="info">
                            <div class="row">
                                <div class="col-md-10">
                                    <h3><a :href="item.url">{{ item.name }}</a></h3>

                                </div>
                                <div class="col-md-2" style="position: static !important;">
                                    <a href="#" class="text-orange heart add-to-wishlist" :data-id="item.id"
                                       :title="__('I care about this project!!!')"><i class="far fa-heart"></i></a>
                                </div>
                            </div>
                            <p class="city"><i class="fas fa-map-marker-alt" style="opacity: 0.7"></i>&nbsp;
                                {{ item.location }}</p>
                            <p class="threemt bold500">
                            <div v-if="item.category_parent_id == '1'">
                                <div class="room-info  pt-3"><i class="fa fa-building fa-2x bed-icon pr-2"
                                                                aria-hidden="true" title="flats"></i><b
                                    class="bed-no pr-2">{{ item.flat }}</b><i
                                    class="fa fa-map fa-2x pr-2 bath-icon" aria-hidden="true" title="floors"></i><b
                                    class="bed-no pr-2 ">{{ item.floor }}</b><i class="fa fa-columns  square-icon pr-2" aria-hidden="true" title="blocks"></i><b class="square-no pr-2 ">{{
                                        item.block
                                    }}</b></div>
                            </div>
                            <div v-else>
                                <div class="room-info  pt-3"><i class="fa fa-map fa-2x bath-icon pr-2"
                                                                aria-hidden="true" title="floors"></i><b
                                    class="bed-no pr-2">{{ item.floor }}</b><i class="fa fa-columns  square-icon pr-2"
                                                                               aria-hidden="true" title="blocks"></i><b
                                    class="square-no pr-2">{{ item.block }}</b></div>
                            </div>
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
export default {


    name: "Projects",

    data() {
        return {
            mapName: "property_search_map",

            // Create the estate object first, otherwise it will not be reactive
            estates: {},
            isLoading: true,
            showModal: false,
            // center: [33.74928730,72.78111600],
            location: this.getParamByName('location'),
            bathroom: "",
            bedroom: "",
            floor: "",
            flat: "",
            min_price: this.getParamByName("min_price") ? this.getParamByName("min_price"): 0,
            max_price: this.getParamByName("max_price") ? this.getParamByName("max_price"): "Any",
            sort_by: "default_sorting",
            category_id: (this.getParamByName('category_id') ? this.getParamByName('category_id') : 1),
            markers: [],
            search_data_chosen: [],
            square: "",
            themeUrl: "",
            city_id: (this.getParamByName('city_id') ? this.getParamByName('city_id') : 0),
            markersLayer: null,
            data: {},
            links: {},
            map: "",
            markerBounds: "",
            blocks: "",
            test: JSON.parse(this.chosenlist),
            options: [

                {text: JSON.parse(this.chosenlist)[0], value: JSON.parse(this.chosenlist)[0]},
                {text: JSON.parse(this.chosenlist)[1], value: JSON.parse(this.chosenlist)[1]},
                {text: JSON.parse(this.chosenlist)[2], value: JSON.parse(this.chosenlist)[2]},

                /*{ text: 'Two', value: '7 Street 57, F-10 Markaz F 10/3 F-10, Islamabad, Islamabad Capital Territory, Pakistan' },
                { text: 'Three', value: 'C' },
                { text: 'Any', value: ['A', 'B', 'C'] },*/

            ]

        }
    },

    mounted() {
        this.getProperties();
        this.getParse();
        console.log(this.max_price + ' max price');
        console.log(this.min_price + ' min price');
        //   this.initMap();
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
        city_name: {
            city_name: String,
            default: () => '',
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
        price_list:
            {
                type: []
            },

        cities: {
            type: []

        },
        chosenlist:
            {
                type: []
            },
        chosenfullist:
            {
                type: []
            },
        current_currency:
            {
                type: ""
            },
        parent_id: {
            type: Number
        },

    },

    methods: {
        populate(event) {
            const options = []
            // iterate over the selected options
            // event.target.selectedOptions is a HTMLCollection
            //alert("here");
            Array.from(event.target.selectedOptions).forEach(item => {
                // or whatever logic you need
                options.push({
                    value: item.value,
                    text: `You have selected ${item.text}`
                })
            })

            // update the options attribute to trigger re-rendering
            this.options = options
        },
        openModal: function () {
            this.showModal = true
        },
        getParse: function () {

            this.price_list = JSON.parse(this.price_list);
            this.chosenlist = JSON.parse(this.chosenlist);
            this.cities = JSON.parse(this.cities);

        },
        getLocationKeywordsArrayFromUrl: function (sParam = 'k[]') {
            let params = new URLSearchParams(window.location.search);
            let value = params.getAll(sParam);
            return value;
        },
        getParamByName: function (name, url = window.location.href) {

            name = name.replace(/[\[\]]/g, '\\$&');
            var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
                results = regex.exec(url);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, ' '));

        },
        getProperties: function (page = 1, is_ajax = false) {
            // var arr = $.parseJSON(this.chosenfullist);
            //console.log(arr);
            // let url = this.url + '?type=' + this.type;
            console.log(this.min_price + ' This is min price Danish');
            this.data = [];
            this.links = {};
            this.isLoading = true;
            /*var max_price = $("input[name='max_price']").val();
            var min_price = $("input[name='min_price']").val();
            //var city_name = $("input[name='location']").val();

            if (max_price != "" && max_price != 'Any') {
                this.max_price = max_price;
            }else{
                console.log(this.max_price + ' max else condition')
            }
            if (min_price != "" && min_price !=0 ){
                this.min_price = min_price;
            }else{
                console.log(this.min_price + ' min else condition')
            }*/
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

            /* if(this.location)
             {
                 url += '&location=' + this.location;
             }else{
                 this.location = '';
             }*/

            /*if (city_name) {
                url += '&location=' + city_name;
            }*/

            /*else {
                //  var url_location = new URL(location.href).searchParams.get('location')
                var url_location=location.href;
                var url_loc = url_location ? url_location.indexOf( "location" ) : -1;
                if (url_loc > 0) {
                    var url_locat = new URL(location.href).searchParams.get('location');
                    url += '&location=' + url_locat;
                    this.location = url_locat;
                }
            }*/
            if (this.flat) {
                url += '&flat=' + this.flat;
                this.flat = this.flat;
            } else {
                //  var url_location = new URL(location.href).searchParams.get('location')
                var url_location = location.href;
                var url_bedroom = url_location ? url_location.indexOf("flat") : -1;
                if (url_bedroom > 0) {
                    var url_bed = new URL(location.href).searchParams.get('flat');
                    url += '&flat=' + url_bed;
                    this.flat = url_bed;
                }
            }
            if (this.floor) {
                url += '&floor=' + this.floor;
                this.floor = this.floor;
            } else {
                var url_location = location.href;
                var url_bathroom = url_location ? url_location.indexOf("floor") : -1;
                if (url_bathroom > 0) {
                    var url_bath = new URL(location.href).searchParams.get('floor');
                    url += '&floor=' + url_bath;
                    this.floor = url_bath;
                }
            }
            /* if(this.floor) {
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
             }*/

            if(Number.isInteger(Number(this.min_price)) && Number(this.min_price) > 0){
                url += '&min_price=' + this.min_price;
            }

            if(Number.isInteger(Number(this.max_price)) && Number(this.max_price) > 0){
                url += '&max_price=' + this.max_price;
            }
           /* if (this.min_price != '' && this.min_price !=0 && is_ajax == true) {
                url += '&min_price=' + this.min_price;
                this.min_price = this.min_price;
            } else {

                var url_location = location.href;
                var url_min = url_location ? url_location.indexOf("min_price") : -1;
                if (url_min > 0) {
                    var url_mi = new URL(location.href).searchParams.get('min_price');

                    url += '&min_price=' + url_mi;
                    this.min_price = url_mi;
                }
            }
            if (this.max_price != '' && this.max_price != 'Any' && is_ajax == true) {
                url += '&max_price=' + this.max_price;
                this.max_price = this.max_price;
            } else {
                console.log(this.max_price + ' this is max price ' + 'this is ajax val: '+is_ajax )
                var url_location = location.href;
                var url_max = url_location ? url_location.indexOf("max_price") : -1;
                if (url_max > 0) {
                    var url_ma = new URL(location.href).searchParams.get('max_price');
                    url += '&max_price=' + url_ma;
                    this.max_price = url_ma;
                }
            }*/


                url += '&city_id=' + this.city_id;


            /*if(this.city_id)
            {
                url += '&city_id=' + this.city_id;

            }
            else
            {
                /!*var url_location=location.href;
                var url_max = url_location ? url_location.indexOf( "max_price" ) : -1;
                if (url_max > 0) {
                    var url_ma = new URL(location.href).searchParams.get('max_price');
                    url += '&max_price=' + url_ma;
                    this.max_price = url_ma;
                }*!/
            }*/

            /*var id_search_hidden = $("#id_search_hidden").val();
            if (id_search_hidden != "") {
                console.log(id_search_hidden);
                //arr.push(id_search_hidden);
            }*/
            this.search_data_chosen = this.chosenlist;
            var searchLocations = [];
            try {
                console.log(chipArray + ' this is chip array');
                searchLocations = chipArray;

            } catch (e) {
                searchLocations = this.getLocationKeywordsArrayFromUrl();
            }

            if (searchLocations.length > 0) {

                $.each(searchLocations, function (index, val) {
                    console.log(val + ' this is val');
                    var jsonObject;
                    try {
                        jsonObject = JSON.parse(val)
                    } catch (e) {
                        jsonObject = val;
                    }
                    url += '&keyword[]=' + jsonObject['id'];
                });
            } else {
                var url_location = location.href;
                var url_k = url_location ? url_location.indexOf("k") : -1;

                /* if (url_k > 0) {
                     var url_ka = new URL(location.href).searchParams.get('k');
                    /!* url += '&keyword=' + url_ka;*!/
                     $.each(url_ka , function(index, val) {
                         url += '&keyword[]=' +val;
                     });

                     //this.search_data_chosen =url_ka;

                 }*/
            }
            if (this.sort_by) {
                url += '&sort_by=' + this.sort_by;
            }
            /* if(this.category_id)
             {
                 url += '&category_id=' + this.category_id;
             }
             else
             {
                 var url_location=location.href;
                 var url_k = url_location ? url_location.indexOf("category_id") : -1;
                 if (url_k > 0) {
                     var url_ka = new URL(location.href).searchParams.get("category_id");
                     url += '&category_id=' + url_ka;
                     this.category_id =url_ka;

                 }
             }*/
            if (this.blocks) {
                url += '&blocks=' + this.blocks;
            }

            url += '&page=' + page;
            axios.get(url, {
                params: {
                    page
                }
            }).then((response) => {
                this.data = response.data.data;
                this.links = response.data.meta;
                this.isLoading = false;
                $("#filterModal").hide();
                $("#filterModal").removeClass('fade');
                // Once estates have been populated, we can insert markers
                this.insertMarkers();

                //pagination and stuff...
            });

        },
        addInfoWindow: function (marker, message) {

            var infoWindow = new google.maps.InfoWindow({
                content: message
            });

            google.maps.event.addListener(marker, 'click', function () {
                infoWindow.open(map, marker);
            });
        },
        getList: function () {
            var k = this.location;
            let url = '/properties?k=' + k;
            /*if(this.location)
            {
                url += '&k=' + k;
            }*/
            axios.get(url)
                .then(res => {
                    console.log(res);
                    //alert(res.data.data);
                    if (res.data) {
                        $(".list_suggest").html(res.data.data);
                        $(".list_suggest").css('display', 'block !important');
                    } else {
                        $(".list_suggest").css('display', 'none !important');
                    }
                });
        },
        resetFilters: function () {
            $(".filter-input").each(function () {
                $(this).val('').change();
            });
        },
        // Iniitialize map without creating markers
        initMap: function () {
            var mapOptions =
                {
                    zoom: 6,
                    center: {
                        lat: 33.74928730,
                        lng: 72.78111600
                    }
                };

            this.map = new google.maps.Map(document.getElementById(this.mapName), mapOptions);
            this.markerBounds = new google.maps.LatLngBounds();
        },

        // Helper method to insert markers
        insertMarkers: function () {

            var mapOptions =
                {
                    zoom: 8,
                    center: {
                        lat: 33.74928730,
                        lng: 72.78111600
                    }
                };

            var list_data = [];


            //  markers[i]= L.marker([data.latitude, +data.longitude]);
            var map = new google.maps.Map(document.getElementById(this.mapName), mapOptions);
            i = 0;

            this.data.forEach(data => {
                list_data[i] = {
                    name: data.name,
                    latlng: new google.maps.LatLng(data.latitude, data.longitude),
                    url: data.url,
                    location: data.location,
                    price_from: data.price_from,
                    price_to: data.price_to,
                    floor: data.floor,
                    flat: data.flat,
                    block: data.block,
                    image: data.image,
                    category_parent_id: data.category_parent_id,
                    name_short: data.name_short
                };
                i++;
            });
            console.log(list_data);

            var latlngbounds = new google.maps.LatLngBounds();
            var currentInfoWindow = null;
            for (var i = 0; i < list_data.length; i++) {

                var data = list_data[i];
                //  console.log(data);
                if (data.category_parent_id == 1)
                    var contentString = '<div class="infowindow-wrap"><div class="thumb img-fluid img-size pt-2"><a href="' + data.url + '"><img src="' + data.image + '"   style="width: 240px;"></a></div><div class="title-info pt-2"><a href="' + data.url + '"><b>' + data.name_short + '</b></a></div><div class="location-info pt-2"><a href="' + data.url + '"><b>' + data.location + '</b></a></div><div class="price-info pt-2"><b>' + data.price_from + ' to ' + data.price_to + '</b></div>  <div class="room-info mg-left pt-3" ><i class="fa fa-building fa-2x bed-icon pr-2" title="Flat" aria-hidden="true"></i><b class="bed-no pr-2">' + data.flat + '</b><i class="fa fa-building p-square-icon pr-2" title="Floor" aria-hidden="true"></i><b class="bed-no pr-2">' + data.floor + '</b><i class="fa fa-building  p-square-icon pr-2" title="block" aria-hidden="true"></i><b class="square-no pr-2">' + data.block + '</b></div></div>';
                else
                    var contentString = '<div class="infowindow-wrap"><div class="thumb img-fluid img-size pt-2"><a href="' + data.url + '"><img src="' + data.image + '"   style="width: 240px;"></a></div><div class="title-info pt-2"><a href="' + data.url + '"><b>' + data.name_short + '</b></a></div><div class="location-info pt-2"><a href="' + data.url + '"><b>' + data.location + '</b></a></div><div class="price-info pt-2"><b>' + data.price_from + ' to ' + data.price_to + '</b></div>  <div class="room-info mg-left pt-3" ><i class="fa fa-building p-square-icon pr-2" title="Floor" aria-hidden="true"></i><b class="bed-no pr-2">' + data.floor + '</b><i class="fa fa-building  p-square-icon pr-2" title="block" aria-hidden="true"></i><b class="square-no pr-2">' + data.block + '</b></div></div>';

                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                });
                var iconBase = '/themes/real-scout/images/generic-3.png';

                var icon = {
                    url: iconBase, // url
                    scaledSize: new google.maps.Size(32, 37), // scaled size
                    origin: new google.maps.Point(0, 0), // origin
                    anchor: new google.maps.Point(0, 0) // anchor
                };
                var marker = new google.maps.Marker({
                    position: list_data[i].latlng,
                    map: map,
                    title: list_data[i].name,
                    icon: icon
                });
                marker['infowindow'] = new google.maps.InfoWindow({
                    content: contentString
                });

                google.maps.event.addListener(marker, 'click', function () {
                    if (currentInfoWindow != null) {
                        currentInfoWindow.close();
                    }
                    this['infowindow'].open(map, this);
                    currentInfoWindow = this['infowindow'];
                });
                /* new google.maps.event.addListener(marker, 'click', function () {

                     /!*infowindow.open({
                         anchor: marker,
                         map,

                     })*!/;

                 });*/
                latlngbounds.extend(list_data[i].latlng);
            }
            if ( list_data[i].latlng == ""){
                list_data[i].latlng = new google.maps.LatLng(30.375320, 69.345116)
            }
            /*for (var i = 0; i < list_data.length; i++) {

            }*/
            map.fitBounds(latlngbounds);
            /*new google.maps.Rectangle({
                bounds: latlngbounds,
                map: map,
                fillColor: "#000000",
                fillOpacity: 0.2,
                strokeWeight: 0
            });*/


            /*var latlng = [
            new google.maps.LatLng(33.99, 85.56),
            new google.maps.LatLng(40.89, 50.01),
            // ...
        ];
        var latlngbounds = new google.maps.LatLngBounds();
        for (var i = 0; i < latlng.length; i++) {
            latlngbounds.extend(latlng[i]);
        }
        map.fitBounds(latlngbounds);*/
            //  var markerBounds = new google.maps.LatLngBounds();
            // Iterate through each individual estate
            // Each estate will create a new marker
            //this.data.forEach(data => {

            /*var marker = new google.maps.Marker({
                map:map,
              // icon: '/themes/real-scout/images/marker.png',
              //  url: "/pages/estates.id",

                position: {
                    lat: parseFloat(data.latitude),
                    lng: parseFloat(data.longitude)
                }
            });*/


            /* var contentString= '<div class="infowindow-wrap"><div class="img-thumbnail pt-2"><img src="'+data.url+'"></div><div class="title-info pt-2"><a href="'+data.url+'"><b>'+data.name+'</b></a></div><div class="location-info pt-2"><a href="'+data.url+'"><b>'+data.location+'</b></a></div><div class="price-info pt-2"><b>'+data.price+'</b></div><div class="room-info pt-2"><img src="/themes/real-scout/images/bed.svg" class="pr-2" alt="icon"><b class="pr-2">'+data.number_bedroom+'</b><img class="pr-2" src="/themes/real-scout/images/bath.svg" alt="icon"><bclass="pr-2">'+data.number_bathroom+'</b><img class="pr-2" src="/themes/real-scout/images/area.svg" alt="icon"><b class="pr-2">'+data.square_text+'</b></div></div>';
              const infowindow = new google.maps.InfoWindow({
                  content: contentString,
              });


              google.maps.event.addListener(marker, 'click', function () {
                  infowindow.close();
                  infowindow.open({
                      anchor: marker,
                      map,

                  });
              });*/

            /* google.maps.event.addListener(marker, 'mouseover', function () {
                 infowindow.open(map, marker);

             });*/

            /* google.maps.event.addListener(marker, 'mouseout', function () {


             })*/
            ;

            // });

            //   map.fitBounds(markerBounds);


        },
    }
}
</script>

<style scoped>
.toggleBtn {

    margin-right: 48px;
}
</style>
