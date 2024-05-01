<template>
    <div class="Properties search-component">
        <!-- Change Area unit modal-->
<div class="modal" id="area_modal" tabindex="-2">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Change Area Unit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select class="form-control" id="area_units-val" @change="selectedUnit($event)">
                    <option value="m²">Square Meter</option>
                    <option value="ft²" selected>Square Feet</option>
                    <option value="yards">Yards</option>
                    <option value="marla">Marla</option>
                    <option value="kanal">Kanal</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">Close</button>
                <button type="button" data-dismiss="modal" id="save-changes" class="btn btn-primary btn-sm" style="height:31px;">Save changes</button>
            </div>
        </div>
    </div>
</div>

        <!-- The Modal -->

        <div class="modal" tabindex="-1" id="filterModal" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">

                    <p type="hidden" class="close" data-dismiss="modal" id="close" aria-label="Close">
                        <span aria-hidden="true" style="display:none">&times;</span>
                    </p>
                    <h3 class="ml-4">Advance Search</h3>
                    <div class="modal-body">
                        <center>
                            <div class="row">
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
                                    <div class="price-placeholder">
                                        <a class="form-control-2 dropdown-toggle border-0 text-left" href="#"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Price <span
                                            class="currency">({{ current_currency }})</span><strong
                                            class="caret"></strong>
                                        </a>
                                        <div class="row  price-from-to-vue modalclass p-0">
                                            <div class="col-md-5"><span class="min_price_text">0</span></div>
                                            <div class="col-md-2 price_to_text">to</div>
                                            <div class="col-md-5"><span class="max_price_text">Any</span></div>
                                        </div>
                                        <div class="dropdown-menu dropdown-menu-2" style="padding:10px;width:100%">
                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <input class="form-control price-label filter-input"
                                                           style="border:1px solid #a0a0a0 !important" name="min_price"
                                                           v-model="min_price" placeholder="Min"
                                                           data-dropdown-id="price-min"  value=""/>
                                                </div>

                                                <div class="col-6">
                                                    <input class="form-control price-label filter-input"
                                                           style="border:1px solid #a0a0a0 !important" name="max_price"
                                                           v-model="max_price" placeholder="Max"
                                                           data-dropdown-id="price-max"  value=""/>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row mt-2 justify-content-center">
                                                <div class="col-md-6">
                                                    <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                        style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden;margin-left: 1rem; cursor:pointer">


                                                        <li class="price-li-item" :data-value="index"
                                                            v-for="(item,index)  in price_list">{{ item }}
                                                        </li>

                                                    </ul>
                                                </div>

                                                <div class="col-md-6">

                                                    <ul class="price-range col-md-12 price-max-ul list-unstyled"
                                                        style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden;margin-left: 1rem; cursor:pointer">

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
                                    <div class="col-md-6 mt-3" v-if="parent_id==1 || ( parent_id==0 && category_id==1)">
                                        <label class="mx-left">Beds</label>
                                        <select name="bedroom" v-model="bedroom"
                                                class="form-control select-bedroom filter-input">
                                            <option value="">Any</option>
                                            <option value="1">1 room</option>
                                            <option value="2">2 rooms</option>
                                            <option value="3">3 rooms</option>
                                            <option value="4">4 rooms</option>
                                            <option value="5">5+ rooms</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mt-3" v-if="parent_id==1 || ( parent_id==0 && category_id==1)">
                                        <label class="mx-left">Baths</label>
                                        <select name="bathroom" v-model="bathroom" class="form-control filter-input">
                                            <option value="">Any</option>
                                            <option value="1">1 room</option>
                                            <option value="2">2 rooms</option>
                                            <option value="3">3 rooms</option>
                                            <option value="4">4 rooms</option>
                                            <option value="5">5+ rooms</option>
                                        </select>
                                    </div>
                                    <!-- <div class="col-md-6 mt-3"> -->
                                        <!-- <label class="mx-left">Land Area</label>
                                        <select name="square" v-model="square" class="form-control filter-input">
                                            <option value="">Any</option>
                                            <option value="5000">5,000</option>
                                            <option value="6000">6000</option>
                                            <option value="7000">7000</option>
                                            <option value="8000">8000</option>
                                            <option value="9000">9000</option>
                                            <option value="10000">10000</option>
                                        </select> -->

                                    <!-- </div> -->
                                    <div class="price-dropdown pl-auto  col-md-6 mt-3 border-0">
                                    <label>Area</label>
                                    <div class="price-placeholder">
                                        <a class="form-control-2 dropdown-toggle border-0 text-left" href="#"
                                           data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Units <span
                                            class="currency">{{ current_unit ? current_unit : "(Square feet)" }}</span><strong
                                            class="caret"></strong>
                                        </a>
                                        <div class="row  price-from-to-vue modalclass p-0">
                                            <div class="col-md-5"><span class="min_unit_text" style="margin-right: 4rem !important;">0</span></div>
                                            <div class="col-md-2 price_to_text">to</div>
                                            <div class="col-md-5"><span class="max_unit_text">Any</span></div>
                                        </div>
                                        <div class="dropdown-menu dropdown-menu-2" style="padding:10px;width:100%">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <small style="color:#363666;text-decoration:underline;margin-left:5px;margin-bottom:5px;"><a href="#" class="area-unit" id="changeAreaUnitlabel"
                                                   data-toggle="modal"
                                                   data-target="#area_modal">Change Area Unit</a></small>
                                                </div>
                                            </div>
                                            <div class="row justify-content-center">
                                                <div class="col-6">
                                                    <input class="form-control price-label filter-input"
                                                            id="input_min_unit"
                                                           style="border:1px solid #a0a0a0 !important" name="min_unit"
                                                           v-model="min_unit" placeholder="Min"
                                                           data-dropdown-id="unit-min" value=""/>
                                                </div>

                                                <div class="col-6">
                                                    <input class="form-control price-label filter-input"
                                                            id="input_max_unit"
                                                           style="border:1px solid #a0a0a0 !important" name="max_unit"
                                                           v-model="max_unit" placeholder="Max"
                                                           data-dropdown-id="unit-max" value=""/>
                                                </div>
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="row mt-2 justify-content-center">
                                                <div class="col-md-6">
                                                    <ul class="units-range col-md-12 unit-min-ul list-unstyled"
                                                        style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden;margin-left: 1rem; cursor:pointer">


                                                        <li class="unit-li-item" :data-value="units.key"
                                                            v-for="units  in area_units">{{ units.value }}
                                                        </li>

                                                    </ul>
                                                </div>

                                                <div class="col-md-6">
                                                    <ul class="units-range col-md-12 unit-max-ul   list-unstyled"
                                                        style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden;margin-left: 1rem; cursor:pointer">

                                                        <li class="unit-li-item" :data-value="units.key"
                                                            v-for="units  in area_units">{{ units.value }}
                                                        </li>


                                                    </ul>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-primary btn-reset-unit" style="margin: 10px; height: 35px !important;">Reset</button>
                                        </div>
                                        </div>
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
                                    <input placeholder="Keyword" class="form-control projects-keyword" type="text"
                                           name=""
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
                            <select class="form-control getlocation" v-model="city_id" id='city_id' name="city_id"
                                    style="visibility: hidden">
                                <option value="">Select city...</option>
                                <option v-for="(city,index) in cities" :key="index" :value="city.id">
                                    {{ city.name }}
                                </option>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-auto">
                        <div class="price-dropdown layout-col ml-0"  style="font-weight: 400 !important;">
                            <div class="dropdown input-group">
                                <!--                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                            Dropdown button
                                                        </button>-->
                                <a class="form-control-2 dropdown-toggle border-0 text-left" href="#"
                                   data-toggle="dropdown"
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
                                            <input class="form-control price-label filter-input"
                                                   style="border:1px solid #a0a0a0 !important" name="min_price"
                                                   id="input_min_price"
                                                   v-model="min_price" placeholder="Min"
                                                   data-dropdown-id="price-min"  value=""/>
                                        </div>

                                        <div class="col-6">
                                            <input class="form-control price-label filter-input"
                                                   style="border:1px solid #a0a0a0 !important" name="max_price"
                                                   id="input_max_price"
                                                   v-model="max_price" placeholder="Max"
                                                   data-dropdown-id="price-max"  value=""/>
                                        </div>

                                        <div class="clearfix"></div>
                                        <div class="row mt-2 justify-content-center">
                                            <div class="col-md-6">
                                                <ul class="price-range col-md-12 price-min-ul list-unstyled"
                                                    style="width: 250px;height:150px; overflow-y: auto;overflow-x:hidden;margin-left: 1rem; cursor:pointer">


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

                                    </div>

                                    <button type="button" class="btn btn-primary btn-reset-price" style="margin: 10px; height: 35px !important;">Reset</button>


                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-auto">
                        <i class="fa fa-search active-icon" v-on:click="getProperties"></i>
                    </div>
                    <div class="col-md-auto">
                        <div class="filter-wrap align-self-center filter-btn">
                            <button class="btn btn-primary" data-toggle="modal" data-target="#filterModal"
                                    type="button">
                                Filter <i class="fa fa-list pl-2"></i></button>

                        </div>
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
                                <a href="#" :data-id="item.id" :title="__('I care about this property!!!')"
                                   class="text-orange heart add-to-wishlist">
                                    <i class="far fa-heart"></i>
                                </a>
                            </div>
                            <div class="d-flex justify-content-around ">
                                <a :href="item.url"> <img :data-src="item.image" :src="item.image" :alt="item.name"
                                                          class="thumb img-fluid img-size"></a>

                                <div class="listing-type-side">

                                    <a :href="item.url"><h6 class="mb-0">{{ item.name_short }}</h6></a>
                                    <p class="mb-0">{{ item.price }}</p>
                                    <p class="mb-0">{{ item.city }}</p>
                                    <p class="mb-0">{{ item.location }}</p>
                                    <!--                                    <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Number of rooms')" v-if="item.number_bedroom"> <img src="/themes/real-scout/images/bed.svg" alt="icon">{{ item.number_bedroom }} </span>
                                                                      <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Number of rest rooms')" v-if="item.number_bathroom">  <img src="/themes/real-scout/images/bath.svg" alt="icon"> {{ item.number_bathroom }}</span>
                                                                      <span data-toggle="tooltip" class="ml-1 d-inline" data-placement="top" :data-original-title="__('Square')" v-if="item.square"> <img src="/themes/real-scout/images/area.svg" alt="icon">{{ item.square_text }}</span>-->


                                </div>

                            </div>
                            <div v-if="item.category_parent_id == '1'">
                                <div class="room-info mg-left pt-3"><i class="fa fa-bed fa-2x bed-icon pr-2"
                                                                       aria-hidden="true"></i><b
                                    class="bed-no pr-2">{{ item.number_bedroom }}</b><i
                                    class="fa fa-bath fa-2x bath-icon pr-2" aria-hidden="true"></i><b
                                    class="bed-no pr-2">{{ item.number_bathroom }}</b><i
                                    class="fas fa-ruler-combined  fa-2x pr-2 square-icon" aria-hidden="true"></i><b
                                    class="square-no pr-2">{{ item.square_text }}</b></div>
                            </div>
                            <div v-else>
                                <div class="room-info mg-left pt-3"><i class="fas fa-ruler-combined  square-icon pr-2"
                                                                       aria-hidden="true"></i><b class="square-no pr-2">{{
                                        item.square_text
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
                <div class="col-sm-4 col-md-3 colm10" v-for="item in data" :key="item.id">
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
                                <li class="item-price">{{ item.price }}</li>
                            </ul>
                        </div>
                        <div class="info">
                            <div class="row">
                                <div class="col-md-10">
                                    <h3><a :href="item.url">{{ item.name }}</a></h3>

                                </div>
                                <div class="col-md-2" style="position: static !important;">
                                    <a href="#" class="text-orange heart add-to-wishlist" :data-id="item.id"
                                       :title="__('I care about this property!!!')"><i class="far fa-heart"></i></a>
                                </div>
                            </div>
                            <p class="city"><i class="fas fa-map-marker-alt" style="opacity: 0.7"></i>&nbsp;
                                {{ item.location }}</p>
                            <p class="threemt bold500">
                            <div v-if="item.category_parent_id == '1'">
                                <div class="room-info   pt-3"><i class="fa fa-bed fa-2x pr-2 bed-icon"
                                                                 aria-hidden="true"></i><b
                                    class="bed-no pr-2">{{ item.number_bedroom }}</b><i
                                    class="fa fa-bath fa-2x pr-2 bath-icon" aria-hidden="true"></i><b
                                    class="bed-no pr-2">{{ item.number_bathroom }}</b><i
                                    class="fas fa-ruler-combined  pr-2 fa-2x square-icon" aria-hidden="true"></i><b
                                    class="square-no pr-2">{{ item.square_text }}</b></div>
                            </div>
                            <div v-else>
                                <div class="room-info text-left pt-3"><i class="fas fa-ruler-combined fa-2x pr-2  square-icon"
                                                                         aria-hidden="true"></i><b
                                    class="square-no pr-2">{{ item.square_text }}</b></div>


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

    name: "Properties",

    data() {
        return {
            mapName: "property_search_map",

            // Create the estate object first, otherwise it will not be reactive
            estates: {},
            isLoading: true,
            showModal: false,
            // center: [33.74928730,72.78111600],
            location: this.getParamByName('location'),
            bathroom: this.getParamByName("bathroom") ? this.getParamByName("bathroom") : "Any",
            bedroom: this.getParamByName("bedroom") ? this.getParamByName("bedroom") : "Any",
            floor: "",
            min_price: this.getParamByName("min_price") ? this.getParamByName("min_price") : 0,
            max_price: this.getParamByName("max_price") ? this.getParamByName("max_price") : "Any",
            min_unit: this.getParamByName("min_unit") ? this.getParamByName("min_unit") : 0,
            max_unit: this.getParamByName("max_unit") ? this.getParamByName("max_unit") : "Any",
            sort_by: "default_sorting",
            category_id: new URL(location.href).searchParams.get("category_id"),
            markers: [],
            search_data_chosen: [],
            // square: "",
            themeUrl: "",
            city_id: (this.getParamByName('city_id') ? this.getParamByName('city_id') : 0),
            markersLayer: null,
            data: {},
            links: {},
            map: "",
            unit: "",
            markerBounds: "",
            current_unit: "",
            property_type: "",

            test: JSON.parse(this.chosenlist),
            options: [

                {text: JSON.parse(this.chosenlist)[0], value: JSON.parse(this.chosenlist)[0]},
                {text: JSON.parse(this.chosenlist)[1], value: JSON.parse(this.chosenlist)[1]},
                {text: JSON.parse(this.chosenlist)[2], value: JSON.parse(this.chosenlist)[2]},

                /*{ text: 'Two', value: '7 Street 57, F-10 Markaz F 10/3 F-10, Islamabad, Islamabad Capital Territory, Pakistan' },
                { text: 'Three', value: 'C' },
                { text: 'Any', value: ['A', 'B', 'C'] },*/

            ],
            area_units: [
                {key: 450, value:450},
                {key: 675, value:675},
                {key: 1125, value:1125},
                {key: 1800, value:1800},
                {key: 2250, value:2250},
                {key: 3375, value:3375},
                {key: 4500, value:4500},
                {key: 6750, value:6750},
                {key: 9000, value:9000},
                {key: 11250, value:11250}
            ]
        }
    },

    mounted() {
        this.getProperties();
        this.getParse();
        this.changeAreaUnit();
    },
    props: {
        url: {
            type: String,
            default: () => null,
            required: true
        },
        type: {
            type: String,
            default: () => null,
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
        chosenlist:
            {
                type: []
            },
        cities: {
            type: []

        },
        chosenfullist:
            {
                type: []
            },
        price_list:
            {
                type: []
            },
        current_currency:
            {
                type: ""
            },
        parent_id: {
            type: Number
        }
    },
    methods: {
        openModal: function () {

            $(".modal").css('display', 'block !important');
        },
        getParse: function () {

            this.price_list = JSON.parse(this.price_list);
            this.chosenlist = JSON.parse(this.chosenlist);
            this.cities = JSON.parse(this.cities);
            console.log(this.chosenlist);
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
        getProperties: function (e, page = 1) {
            var arr = $.parseJSON(this.chosenfullist);
            console.log(arr);
            var max_price = $("input[name='max_price']").val();
            var min_price = $("input[name='min_price']").val();
            var max_unit = $("input[name='max_unit']").val();
            var min_unit = $("input[name='min_unit']").val();
            var city_name = $("input[name='location']").val();

            if (max_price != "")
                this.max_price = max_price;
            if (min_price != "")
                this.min_price = min_price;
            if (max_unit != "")
                this.max_unit = max_unit;
            if (min_unit != "")
                this.min_unit = min_unit;
            let url = this.url + '?type=' + this.type;
            this.data = [];
            this.links = {};
            this.isLoading = true;

            console.log("CATEGORY_ID : "+this.category_id)

            if (this.property_type != "") {
                url += '?property_type=' + this.property_type;

            } else {
                //  var url_location = new URL(location.href).searchParams.get('location')
                var url_type = location.href;
                var url_ty = url_type ? url_type.indexOf("type") : -1;
                if (url_ty > 0) {
                    var url_ty = new URL(location.href).searchParams.get('type');
                    url += '&property_type=' + url_ty;

                }
            }
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
            if (city_name) {
                url += '&location=' + city_name;
            }

            /* if(this.location)
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
             }*/
            if (Number.isInteger(Number(this.bedroom)) && Number(this.bedroom) > 0) {
                url += '&bedroom=' + this.bedroom;
            }

            if (Number.isInteger(Number(this.bathroom)) && Number(this.bathroom) > 0) {
                url += '&bathroom=' + this.bathroom;
            }
            /*else
            {
                //  var url_location = new URL(location.href).searchParams.get('location')
                var url_location=location.href;
                var url_bedroom = url_location ? url_location.indexOf( "bedroom" ) : -1;
                if (url_bedroom > 0) {
                    var url_bed = new URL(location.href).searchParams.get('bedroom');
                    url += '&bedroom=' + url_bed;
                    this.bedroom = url_bed;
                }
            }*/

            /*else
            {
                var url_location=location.href;
                var url_bathroom = url_location ? url_location.indexOf( "bathroom" ) : -1;
                if (url_bathroom > 0) {
                    var url_bath = new URL(location.href).searchParams.get('bathroom');
                    url += '&bathroom=' + url_bath;
                    this.bathroom = url_bath;
                }
            }*/
            if (this.floor) {
                url += '&floor=' + this.floor;
                this.floor = this.floor;
            } else {
                var url_location = location.href;
                var url_floor = url_location ? url_location.indexOf("floor") : -1;
                if (url_floor > 0) {
                    var url_f = new URL(location.href).searchParams.get('floor');
                    url += '&floor=' + url_f;
                    this.floor = url_f;
                }
            }

            if (Number.isInteger(Number(this.min_price)) && Number(this.min_price) > 0) {
                url += '&min_price=' + this.min_price;
            }

            if (Number.isInteger(Number(this.max_price)) && Number(this.max_price) > 0) {
                url += '&max_price=' + this.max_price;
            }

            if (Number(this.min_unit) && Number(this.min_unit) > 0) {
                url += '&min_square=' + this.min_unit;
            }

            if (Number(this.max_unit) && Number(this.max_unit) > 0) {
                url += '&max_square=' + this.max_unit;
            }
            /*if(this.min_price)
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
            }*/
            this.current_unit = this.current_unit ? this.current_unit:"(Square feet)";
            this.unit = this.unit ? this.unit:'ft²';

            if (this.current_unit == "(Square feet)" ) {
                url += '&unit=' + 'ft²';
            }
            if (this.current_unit == "(Square meter)"){
                url += '&unit=' + 'm²';
            }
            if (this.current_unit == "(Yards)"){
                url += '&unit=' + 'yard';
            }
            if (this.current_unit == "(Marla)"){
                url += '&unit=' + 'marla';
            }
            if(this.current_unit == "(Kanal)"){
                url += '&unit=' + 'kanal';
            }

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
            /*var id_search_hidden=$("#id_search_hidden").val();
            if(id_search_hidden!="")
            {
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
            if(this.category_id)
            {
                url += '&category_id=' + this.category_id;
            }
            // else
            // {
            //     var url_location=location.href;
            //     var url_k = url_location ? url_location.indexOf("category_id") : -1;
            //     if (url_k > 0) {
            //         var url_ka = new URL(location.href).searchParams.get("category_id");
            //         url += '&category_id=' + url_ka;
            //         this.category_id =url_ka;
            //
            //     }
            // }

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
                // for price range list
                $('input[data-dropdown-id="price-max"]').attr("placeholder","Max");
                $('input[data-dropdown-id="price-min"]').attr("placeholder","Min");
                $('.min_price_text').html("0");
                $('.max_price_text').html("Any");
                $(".price-min-ul li").removeClass("category-li-item-active");
                $(".price-max-ul li").removeClass("category-li-item-active");
                // for units range list
                $('[data-dropdown-id="unit-max"]').attr("placeholder","Max");
                $('[data-dropdown-id="unit-min"]').attr("placeholder","Min");
                $('.min_unit_text').html("0");
                $('.max_unit_text').html("Any");
                $(".unit-min-ul li").removeClass("category-li-item-active");
                $(".unit-max-ul li").removeClass("category-li-item-active");
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
        changeAreaUnit: function () {
            $( ".area-unit" ).on('click',function() {
                $("#filterModal").hide();
                $("#filterModal").removeClass('fade');
            });
            $( "#save-changes" ).on('click',function() {
                $("#filterModal").show();
                $("#filterModal").addClass('fade');
                // remove previous values
                $('[data-dropdown-id="unit-max"]').val("");
                $('[data-dropdown-id="unit-min"]').val("");
                $('[data-dropdown-id="unit-max"]').attr("placeholder","Max");
                $('[data-dropdown-id="unit-min"]').attr("placeholder","Min");
                $('.min_unit_text').html("0");
                $('.max_unit_text').html("Any");
                $(".unit-min-ul li").removeClass("category-li-item-active");
                $(".unit-max-ul li").removeClass("category-li-item-active");
            });
        },
        selectedUnit(event) {
            this.current_unit = "(Square feet)";
            if(event.target.value == 'ft²'){
                this.current_unit = "(Square feet)";
                $.each(this.area_units, function (index, val) {
                    if (this.unit == 'marla') {
                        val.value  = Math.ceil(val.value *  225);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'm²') {
                        val.value  = Math.ceil(val.value *  10.764);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'yards') {
                        val.value = Math.ceil(val.value *  9);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                    if (this.unit == 'kanal') {
                        val.value = Math.ceil(val.value *  4500);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                });
            }
            else if(event.target.value == 'm²'){
                this.current_unit = "(Square meter)";
                $.each(this.area_units, function (index, val) {
                     if (this.unit == 'ft²' || this.unit == undefined) {
                        val.value  = Math.ceil(val.value /  10.764);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'marla') {
                        val.value  = Math.ceil((val.value *  225) /  10.764);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'yards') {
                        val.value = Math.ceil((val.value *  9) /  10.764);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                    if (this.unit == 'kanal') {
                        val.value = Math.ceil((val.value *  4500) /  10.764);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                });
            }else if (event.target.value == 'marla') {
                this.current_unit = "(Marla)";
                $.each(this.area_units, function (index, val) {
                     if (this.unit == 'ft²' || this.unit == undefined) {
                        val.value  = Math.ceil(val.value /  225);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'm²') {
                        val.value  = Math.ceil((val.value *  10.764) /  225);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'yards') {
                        val.value = Math.ceil((val.value *  9) /  225);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                    if (this.unit == 'kanal') {
                        val.value = Math.ceil((val.value *  4500) /  225);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                });
            }else if(event.target.value == 'yards'){
                this.current_unit = "(Yards)";
                $.each(this.area_units, function (index, val) {
                     if (this.unit == 'ft²' || this.unit == undefined) {
                        val.value  = Math.ceil(val.value /  9);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'm²') {
                        val.value  = Math.ceil((val.value *  10.764) /  9);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'marla') {
                        val.value = Math.ceil((val.value *  225) /  9);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                    if (this.unit == 'kanal') {
                        val.value = Math.ceil((val.value *  4500) /  9);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                });
            }
            else if(event.target.value == 'kanal'){
                this.current_unit = "(Kanal)";
                $.each(this.area_units, function (index, val) {
                    if (this.unit == 'ft²' || this.unit == undefined) {
                        val.value  = val.value /  4500;
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'm²') {
                        val.value  = Math.round((val.value *  10.764) /  4500);
                        val.key = val.value;
                        this.unit = event.target.value;
                    }
                    if (this.unit == 'yards') {
                        val.value = (val.value *  9) /  4500;
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                    if (this.unit == 'marla') {
                        val.value = (val.value *  225) /  4500;
                        val.key = val.value;
                        this.unit = event.target.value;
                    }

                });


            }
            else{
                console.log("No unit selected");
            }

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
                    price: data.price,
                    number_bedroom: data.number_bedroom,
                    number_bathroom: data.number_bathroom,
                    square_text: data.square_text,
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
                    var contentString = '<div class="infowindow-wrap"><div class="thumb img-fluid img-size pt-2"><a href="' + data.url + '"><img src="' + data.image + '"   style="width: 240px;"></a></div><div class="title-info pt-2"><a href="' + data.url + '"><b>' + data.name_short + '</b></a></div><div class="location-info pt-2"><a href="' + data.url + '"><b>' + data.location + '</b></a></div><div class="price-info pt-2"><b>' + data.price + '</b></div>  <div class="room-info  pt-3" ><i class="fa fa-bed fa-2x bed-icon pr-2" aria-hidden="true"></i><b class="bed-no pr-2">' + data.number_bedroom + '</b><i class="fa fa-bath fa-2x bath-icon pr-2" aria-hidden="true"></i><b class="bed-no pr-2">' + data.number_bathroom + '</b><i class="fas fa-ruler-combined  pr-2 fa-2x square-icon" aria-hidden="true"></i><b class="square-no pr-2">' + data.square_text + '</b></div></div>';
                else
                    var contentString = '<div class="infowindow-wrap"><div class="thumb img-fluid img-size pt-2"><a href="' + data.url + '"><img src="' + data.image + '"   style="width: 240px;"></a></div><div class="title-info pt-2"><a href="' + data.url + '"><b>' + data.name_short + '</b></a></div><div class="location-info pt-2"><a href="' + data.url + '"><b>' + data.location + '</b></a></div><div class="price-info pt-2"><b>' + data.price + '</b></div> <div class="room-info text-left pt-3" ><i class="fas fa-ruler-combined fa-2x pr-2 square-icon" aria-hidden="true"></i><b class="square-no pr-2">' + data.square_text + '</b></div> </div>';

                const infowindow = new google.maps.InfoWindow({
                    content: contentString,
                });
                var iconBase = '/themes/real-scout/images/generic.png';

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
