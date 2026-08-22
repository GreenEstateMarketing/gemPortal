<template>
  <div class="Properties search-component">
    <!-- Change Area unit modal-->
    <div class="modal" id="area_modal" tabindex="-2">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Change Area sdfsUnit</h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <select
              class="form-control"
              id="area_units-val"
              @change="selectedUnit($event)"
            >
              <option value="m²">Square Meter</option>
              <option value="ft²" selected>Square Feet</option>
              <option value="yards">Yards</option>
              <option value="marla">Marla</option>
              <option value="kanal">Kanal</option>
            </select>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary btn-sm"
              data-dismiss="modal"
            >
              Close
            </button>
            <button
              type="button"
              data-dismiss="modal"
              id="save-changes"
              class="btn btn-primary btn-sm"
              style="height: 31px"
            >
              Save changes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- The Modal -->

    <div class="modal" tabindex="-1" id="filterModal" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <p
            type="hidden"
            class="close"
            data-dismiss="modal"
            id="close"
            aria-label="Close"
          >
            <span aria-hidden="true" style="display: none">&times;</span>
          </p>
          <h3 class="ml-4">Advance Search</h3>
          <div class="modal-body">
            <center>
              <div class="row">
                <div class="col-md-6 pl-auto col-md-6 mt-3 border-0">
                  <label class="mx-left">Property Type</label>
                  <select
                    name="category_id"
                    v-model="category_id"
                    class="form-control filter-input"
                    @change="
                      getChildCategories(
                        $event.target.options[$event.target.selectedIndex]
                          .dataset.value,
                      )
                    "
                  >
                    <option
                      :key="index"
                      :data-value="index"
                      :value="index"
                      v-for="(item, index) in parent_categories"
                    >
                      {{ item }}
                    </option>
                  </select>
                </div>
                <div class="col-md-6 pl-auto col-md-6 mt-3 border-0">
                  <label class="mx-left">Sub Type</label>
                  <select
                    name="sub_category_id"
                    v-model="child_category_id"
                    class="form-control filter-input"
                  >
                    <option
                      :data-value="index"
                      :value="index"
                      v-for="(item, index) in child_categories"
                    >
                      {{ item }}
                    </option>
                  </select>
                </div>
                <div class="price-dropdown pl-auto col-md-6 mt-3 border-0">
                  <label>Price</label>
                  <div class="price-placeholder">
                    <a
                      class="form-control-2 dropdown-toggle border-0 text-left"
                      href="#"
                      data-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                      >Price
                      <span class="currency">({{ current_currency }})</span
                      ><strong class="caret"></strong>
                    </a>
                    <div class="row price-from-to-vue modalclass p-0">
                      <div class="col-md-5">
                        <span class="min_price_text">0</span>
                      </div>
                      <div class="col-md-2 price_to_text">to</div>
                      <div class="col-md-5">
                        <span class="max_price_text">Any</span>
                      </div>
                    </div>
                    <div
                      class="dropdown-menu dropdown-menu-2"
                      style="padding: 10px; width: 100%"
                    >
                      <div class="row justify-content-center">
                        <div class="col-6">
                          <input
                            class="form-control price-label filter-input"
                            style="border: 1px solid #a0a0a0 !important"
                            name="min_price"
                            v-model="min_price"
                            placeholder="Min"
                            data-dropdown-id="price-min"
                            value=""
                          />
                        </div>

                        <div class="col-6">
                          <input
                            class="form-control price-label filter-input"
                            style="border: 1px solid #a0a0a0 !important"
                            name="max_price"
                            v-model="max_price"
                            placeholder="Max"
                            data-dropdown-id="price-max"
                            value=""
                          />
                        </div>
                      </div>
                      <div class="clearfix"></div>
                      <div class="row mt-2 justify-content-center">
                        <div class="col-md-6">
                          <ul
                            class="price-range col-md-12 price-min-ul list-unstyled"
                            style="
                              width: 250px;
                              height: 150px;
                              overflow-y: auto;
                              overflow-x: hidden;
                              margin-left: 1rem;
                              cursor: pointer;
                            "
                          >
                            <li
                              class="price-li-item"
                              :data-value="index"
                              v-for="(item, index) in price_list"
                            >
                              {{ item }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-md-6">
                          <ul
                            class="price-range col-md-12 price-max-ul list-unstyled"
                            style="
                              width: 250px;
                              height: 150px;
                              overflow-y: auto;
                              overflow-x: hidden;
                              margin-left: 1rem;
                              cursor: pointer;
                            "
                          >
                            <li
                              class="price-li-item"
                              :data-value="index"
                              v-for="(item, index) in price_list"
                            >
                              {{ item }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <button
                        type="button"
                        class="btn btn-primary btn-reset-price"
                        @click="resetPriceFilters"
                        style="margin: 10px; height: 35px !important"
                      >
                        Reset
                      </button>
                    </div>
                  </div>
                </div>

                <!--                        </div>
                                                            <div class="row mt-3">-->
                <div
                  class="col-md-6 mt-3"
                  v-if="parent_id == 1 || (parent_id == 0 && category_id == 1)"
                >
                  <label class="mx-left">Beds</label>
                  <select
                    name="bedroom"
                    v-model="bedroom"
                    class="form-control select-bedroom filter-input"
                  >
                    <option value="">Any</option>
                    <option value="1">1 room</option>
                    <option value="2">2 rooms</option>
                    <option value="3">3 rooms</option>
                    <option value="4">4 rooms</option>
                    <option value="5">5+ rooms</option>
                  </select>
                </div>
                <div
                  class="col-md-6 mt-3"
                  v-if="parent_id == 1 || (parent_id == 0 && category_id == 1)"
                >
                  <label class="mx-left">Baths</label>
                  <select
                    name="bathroom"
                    v-model="bathroom"
                    class="form-control filter-input"
                  >
                    <option value="">Any</option>
                    <option value="1">1 room</option>
                    <option value="2">2 rooms</option>
                    <option value="3">3 rooms</option>
                    <option value="4">4 rooms</option>
                    <option value="5">5+ rooms</option>
                  </select>
                </div>
                <div class="price-dropdown pl-auto col-md-6 mt-3 border-0">
                  <label>Area</label>
                  <div class="price-placeholder">
                    <a
                      class="form-control-2 dropdown-toggle border-0 text-left"
                      href="#"
                      data-toggle="dropdown"
                      aria-haspopup="true"
                      aria-expanded="false"
                      >Unit
                      <span class="currency">{{
                        current_unit ? current_unit : "(Square feet)"
                      }}</span
                      ><strong class="caret"></strong>
                    </a>
                    <div class="row price-from-to-vue modalclass p-0">
                      <div class="col-md-5">
                        <span
                          class="min_unit_text"
                          style="margin-right: 4rem !important"
                          >{{ this.min_unit }}</span
                        >
                      </div>
                      <div class="col-md-2 price_to_text">to</div>
                      <div class="col-md-5">
                        <span class="max_unit_text">{{ this.max_unit }}</span>
                      </div>
                    </div>
                    <div
                      class="dropdown-menu dropdown-menu-2"
                      style="padding: 10px; width: 100%"
                    >
                      <div class="row">
                        <div class="col-md-12">
                          <small
                            style="
                              color: #363666;
                              text-decoration: underline;
                              margin-left: 5px;
                              margin-bottom: 5px;
                            "
                            ><a
                              href="#"
                              class="area-unit"
                              id="changeAreaUnitlabel"
                              data-toggle="modal"
                              data-target="#area_modal"
                              >Change Area Unit</a
                            ></small
                          >
                        </div>
                      </div>
                      <div class="row justify-content-center">
                        <div class="col-6">
                          <input
                            class="form-control price-label filter-input"
                            id="input_min_unit"
                            style="border: 1px solid #a0a0a0 !important"
                            name="min_unit"
                            v-model="min_unit"
                            placeholder="Min"
                            data-dropdown-id="unit-min"
                            value=""
                          />
                        </div>

                        <div class="col-6">
                          <input
                            class="form-control price-label filter-input"
                            id="input_max_unit"
                            style="border: 1px solid #a0a0a0 !important"
                            name="max_unit"
                            v-model="max_unit"
                            placeholder="Max"
                            data-dropdown-id="unit-max"
                            value=""
                          />
                        </div>
                      </div>
                      <div class="clearfix"></div>
                      <div class="row mt-2 justify-content-center">
                        <div class="col-md-6">
                          <ul
                            class="units-range col-md-12 unit-min-ul list-unstyled"
                            style="
                              width: 250px;
                              height: 150px;
                              overflow-y: auto;
                              overflow-x: hidden;
                              margin-left: 1rem;
                              cursor: pointer;
                            "
                          >
                            <li
                              class="unit-li-item"
                              :data-value="units.key"
                              v-for="units in area_units"
                            >
                              {{ units.value }}
                            </li>
                          </ul>
                        </div>

                        <div class="col-md-6">
                          <ul
                            class="units-range col-md-12 unit-max-ul list-unstyled"
                            style="
                              width: 250px;
                              height: 150px;
                              overflow-y: auto;
                              overflow-x: hidden;
                              margin-left: 1rem;
                              cursor: pointer;
                            "
                          >
                            <li
                              class="unit-li-item"
                              :data-value="units.key"
                              v-for="units in area_units"
                            >
                              {{ units.value }}
                            </li>
                          </ul>
                        </div>
                      </div>
                      <button
                        type="button"
                        class="btn btn-primary btn-reset-unit"
                        @click="resetUnitFilters"
                        style="margin: 10px; height: 35px !important"
                      >
                        Reset
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </center>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-reset-modal"
              @click="resetFilters"
            >
              Reset
            </button>
            <button
              type="button"
              class="btn btn-info-modal"
              @click="getProperties"
            >
              Search
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="search-background">
      <div class="container">
        <div class="row no-gutters search-wrap justify-content-between">
          <div class="col">
            <div id="parentChipContainer" class="parent-chip-container mr-0">
              <div id="chipContainer">
                <div
                  class="position-relative input-field-container"
                  style="max-height: 32px; max-width: 60%"
                >
                  <input
                    placeholder="Location"
                    class="form-control projects-keyword"
                    type="text"
                    name=""
                    id="autocomplete-ajax"
                    style="
                      position: absolute;
                      z-index: 2;
                      background: transparent;
                      width: auto !important;
                    "
                  />
                  <input
                    class="form-control projects-keyword"
                    type="text"
                    name=""
                    id="autocomplete-ajax-x"
                    disabled="disabled"
                    style="color: #ccc; background: transparent; z-index: 1"
                  />
                </div>
                <div id="chipViewMore" class="chip" style="display: none">
                  <div class="chip-content"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-auto">
            <div class="" style="background-color: white; height: 100%">
              <input
                id="city-name-from-map"
                type="hidden"
                class="select-city-state form-control"
                autocomplete="off"
              />
              <select
                class="form-control getlocation"
                v-model="city_id"
                id="city_id"
                name="city_id"
                style="visibility: hidden"
              >
                <option value="">Select city...</option>
                <option
                  v-for="(city, index) in cities"
                  :key="index"
                  :value="city.id"
                >
                  {{ city.name }}
                </option>
              </select>
            </div>
          </div>
          <div class="col-md-auto">
            <div
              class="price-dropdown layout-col ml-0"
              style="font-weight: 400 !important"
            >
              <div class="dropdown input-group">
                <a
                  class="form-control-2 dropdown-toggle border-0 text-left"
                  href="#"
                  data-toggle="dropdown"
                  aria-haspopup="true"
                  aria-expanded="false"
                  >Price <span class="currency">({{ current_currency }})</span
                  ><strong class="caret"></strong>
                </a>
                <div class="row price-from-to-vue modalclass p-0">
                  <div class="col-md-4">
                    <span class="min_price_text">{{ min_price }}</span>
                  </div>
                  <div class="col-md-2 price_to_text">to</div>
                  <div class="col-md-4">
                    <span class="max_price_text">{{ max_price }}</span>
                  </div>
                </div>
                <div
                  class="dropdown-menu dropdown-menu-2"
                  style="padding: 10px; width: 100%"
                >
                  <div class="row justify-content-center">
                    <div class="col-6">
                      <input
                        class="form-control price-label filter-input"
                        style="border: 1px solid #a0a0a0 !important"
                        name="min_price"
                        id="input_min_price"
                        v-model="min_price"
                        placeholder="Min"
                        data-dropdown-id="price-min"
                        value=""
                      />
                    </div>

                    <div class="col-6">
                      <input
                        class="form-control price-label filter-input"
                        style="border: 1px solid #a0a0a0 !important"
                        name="max_price"
                        id="input_max_price"
                        v-model="max_price"
                        placeholder="Max"
                        data-dropdown-id="price-max"
                        value=""
                      />
                    </div>

                    <div class="clearfix"></div>
                    <div class="row mt-2 justify-content-center">
                      <div class="col-md-6">
                        <ul
                          class="price-range col-md-12 price-min-ul list-unstyled"
                          style="
                            width: 250px;
                            height: 150px;
                            overflow-y: auto;
                            overflow-x: hidden;
                            margin-left: 1rem;
                            cursor: pointer;
                          "
                        >
                          <li
                            class="price-li-item"
                            :data-value="index"
                            v-for="(item, index) in price_list"
                          >
                            {{ item }}
                          </li>
                        </ul>
                      </div>

                      <div class="col-md-6">
                        <ul
                          class="price-range col-md-12 price-max-ul list-unstyled"
                          style="
                            width: 250px;
                            height: 150px;
                            overflow-y: auto;
                            overflow-x: hidden;
                            cursor: pointer;
                          "
                        >
                          <li
                            class="price-li-item"
                            :data-value="index"
                            v-for="(item, index) in price_list"
                          >
                            {{ item }}
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>

                  <button
                    type="button"
                    class="btn btn-primary btn-reset-price"
                    @click="resetPriceFilters"
                    style="margin: 10px; height: 35px !important"
                  >
                    Reset
                  </button>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-auto">
            <i class="fa fa-search active-icon" v-on:click="getProperties"></i>
          </div>
          <div class="col-md-auto">
            <div class="filter-wrap align-self-center filter-btn">
              <button
                class="btn btn-primary"
                data-toggle="modal"
                data-target="#filterModal"
                type="button"
              >
                Filter <i class="fa fa-list pl-2"></i>
              </button>
            </div>
          </div>
        </div>

        <!--</form>-->
      </div>
    </div>

    <div class="bs-example">
      <div class="btn-group btn-group-toggle toggleBtn" data-toggle="buttons">
        <label
          class="btn btn-primary active clickBtn"
          data-attr="map"
          style="cursor: pointer"
        >
          Map
        </label>
        <label
          class="btn btn-primary clickBtn"
          data-attr="list"
          style="cursor: pointer"
        >
          List
        </label>
      </div>
    </div>
    <div class="layout-properties">
      <div class="properties_side_list">
        <div class="bg-gray">
          <div class="results">
            <h4 class="ml-3">{{ resultTitle }}: {{ links.total }} Listing</h4>
          </div>
          <hr />
          <div class="sort-by">
            <span class="ml-3 bold">Sort By</span>

            <select
              @change="getProperties"
              v-model="sort_by"
              name="sort_by"
              id="sort_by"
              class="p-1 bg-white"
            >
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
        <hr />
        <div class="side-listing">
          <div class="half-circle-spinner" v-if="isLoading">
            <div class="circle circle-1"></div>
            <div class="circle circle-2"></div>
          </div>
          <div
            v-show="show_empty_string && !isLoading && !data.length"
            class="col-12 text-center"
          >
            <span>{{ __("No property found") }}!</span>
          </div>
          <!--                    v-favorites-->
          <div
            v-for="item in data"
            :key="item.id"
            v-show="!isLoading && data.length"
          >
            <div class="pl-0">
              <div class="wishlist-wrap">
                <a
                  href="#"
                  :data-id="item.id"
                  :title="__('I care about this property!!!')"
                  class="text-orange heart add-to-wishlist"
                >
                  <i class="far fa-heart"></i>
                </a>
              </div>
              <div class="d-flex justify-content-around">
                <a :href="item.url">
                  <img
                    :data-src="item.image"
                    :src="item.image"
                    :alt="item.name"
                    class="thumb img-fluid img-size"
                /></a>

                <div class="listing-type-side">
                  <a :href="item.url">
                    <h6 class="mb-0">{{ item.name_short }}</h6>
                  </a>
                  <p class="mb-0">{{ item.price }}</p>
                  <p class="mb-0">{{ item.city }}</p>
                  <p class="mb-0">{{ item.location }}</p>
                </div>
              </div>
              <div v-if="item.category_parent_id == '1'">
                <div class="room-info mg-left pt-3">
                  <i
                    class="fa fa-bed fa-2x bed-icon pr-2"
                    aria-hidden="true"
                  ></i
                  ><b class="bed-no pr-2">{{ item.number_bedroom }}</b
                  ><i
                    class="fa fa-bath fa-2x bath-icon pr-2"
                    aria-hidden="true"
                  ></i
                  ><b class="bed-no pr-2">{{ item.number_bathroom }}</b
                  ><i
                    class="fas fa-ruler-combined fa-2x pr-2 square-icon"
                    aria-hidden="true"
                  ></i
                  ><b class="square-no pr-2">{{ item.square_text }}</b>
                </div>
              </div>
              <div v-else>
                <div class="room-info mg-left pt-3">
                  <i
                    class="fas fa-ruler-combined square-icon pr-2"
                    aria-hidden="true"
                  ></i
                  ><b class="square-no pr-2">{{ item.square_text }}</b>
                </div>
              </div>
            </div>
            <hr />
          </div>
          <pagination
            :data="links"
            @pagination-change-page="getProperties"
          ></pagination>
        </div>
      </div>

      <div id="map-container" style="height: 708.5px; position: relative">
        <div
          id="property_search_map"
          style="height: 708.5px; position: relative"
        ></div>
      </div>
    </div>
    <div class="container porperties_list">
      <div class="bg-list">
        <h4 class="ml-3">{{ resultTitle }}: {{ links.total }} Listings</h4>
        <div class="row d-flex">
          <div class="col-md-6">
            <div class="row align-items-center d-flex">
              <div class="col-md-2"><label>Sort By:</label></div>
              <div class="col-md-10">
                <select
                  @change="getProperties"
                  v-model="sort_by"
                  style="width: 50% !important"
                  name="sort_by"
                  class="bg-white p-1 sort_by p-select-list form-control"
                >
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
        <div
          v-if="show_empty_string && !isLoading && !data.length"
          class="col-12 text-center"
        >
          <span>{{ __("No property found") }}!</span>
        </div>
        <div
          class="col-sm-4 col-md-3 colm10"
          v-for="item in data"
          :key="item.id"
        >
          <div class="hourseitem">
            <div class="blii">
              <div class="img">
                <img
                  class="thumb"
                  :data-src="item.image"
                  :src="item.image"
                  :alt="item.name"
                />
              </div>
              <a :href="item.url" class="linkdetail"></a>
              <div class="media-count-wrapper">
                <div class="media-count">
                  <span>{{ item.images.length }}</span>
                </div>
              </div>
              <div
                class="status"
                v-html="
                  $sanitize(item.status_html, {
                    allowedTags: ['span'],
                    allowedAttributes: { span: ['class'] },
                  })
                "
              ></div>
              <ul class="item-price-wrap hide-on-list">
                <li class="h-type">
                  <span>{{ item.category_name }}</span>
                </li>
                <li class="item-price">{{ item.price }}</li>
              </ul>
            </div>
            <div class="info">
              <div class="row">
                <div class="col-md-10">
                  <h3>
                    <a :href="item.url">{{ item.name }}</a>
                  </h3>
                </div>
                <div class="col-md-2" style="position: static !important">
                  <a
                    href="#"
                    class="text-orange heart add-to-wishlist"
                    :data-id="item.id"
                    :title="__('I care about this property!!!')"
                    ><i class="far fa-heart"></i
                  ></a>
                </div>
              </div>
              <p class="city">
                <i class="fas fa-map-marker-alt" style="opacity: 0.7"></i>&nbsp;
                {{ item.location }}
              </p>
              <div class="threemt bold500"></div>
              <div v-if="item.category_parent_id == '1'">
                <div class="room-info pt-3">
                  <i
                    class="fa fa-bed fa-2x pr-2 bed-icon"
                    aria-hidden="true"
                  ></i
                  ><b class="bed-no pr-2">{{ item.number_bedroom }}</b
                  ><i
                    class="fa fa-bath fa-2x pr-2 bath-icon"
                    aria-hidden="true"
                  ></i
                  ><b class="bed-no pr-2">{{ item.number_bathroom }}</b
                  ><i
                    class="fas fa-ruler-combined pr-2 fa-2x square-icon"
                    aria-hidden="true"
                  ></i
                  ><b class="square-no pr-2">{{ item.square_text }}</b>
                </div>
              </div>
              <div v-else>
                <div class="room-info text-left pt-3">
                  <i
                    class="fas fa-ruler-combined fa-2x pr-2 square-icon"
                    aria-hidden="true"
                  ></i
                  ><b class="square-no pr-2">{{ item.square_text }}</b>
                </div>
              </div>
            </div>
          </div>
        </div>
        <pagination
          :data="links"
          @pagination-change-page="getProperties"
        ></pagination>
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
      location: this.getParamByName("location"),
      bathroom: this.getParamByName("bathroom")
        ? this.getParamByName("bathroom")
        : "Any",
      bedroom: this.getParamByName("bedroom")
        ? this.getParamByName("bedroom")
        : "Any",
      floor: "",
      min_price: this.getParamByName("min_price")
        ? this.getParamByName("min_price")
        : 0,
      max_price: this.getParamByName("max_price")
        ? this.getParamByName("max_price")
        : "Any",
      min_unit: this.getParamByName("min_unit")
        ? this.getParamByName("min_unit")
        : 0,
      max_unit: this.getParamByName("max_unit")
        ? this.getParamByName("max_unit")
        : "Any",
      sort_by: "default_sorting",
      category_id: new URL(location.href).searchParams.get("category_id"),
      child_category_id: "",
      markers: [],
      search_data_chosen: [],
      // square: "",
      themeUrl: "",
      city_id: this.getParamByName("city_id")
        ? this.getParamByName("city_id")
        : 0,
      markersLayer: null,
      data: {},
      links: {},
      map: "",
      unit: "",
      markerBounds: "",
      current_unit:
        this.getParamByName("selected-unit") !== null
          ? "(" + this.getParamByName("selected-unit") + ")"
          : "(Square feet)",
      property_type: "",
      parent_categories: [],
      selected_parent_category: "",
      child_categories: [],
      coordinates: [],

      test: JSON.parse(this.chosenlist),
      options: [
        {
          text: JSON.parse(this.chosenlist)[0],
          value: JSON.parse(this.chosenlist)[0],
        },
        {
          text: JSON.parse(this.chosenlist)[1],
          value: JSON.parse(this.chosenlist)[1],
        },
        {
          text: JSON.parse(this.chosenlist)[2],
          value: JSON.parse(this.chosenlist)[2],
        },
      ],
      area_units: [
        { key: 450, value: 450 },
        { key: 675, value: 675 },
        { key: 1125, value: 1125 },
        { key: 1800, value: 1800 },
        { key: 2250, value: 2250 },
        { key: 3375, value: 3375 },
        { key: 4500, value: 4500 },
        { key: 6750, value: 6750 },
        { key: 9000, value: 9000 },
        { key: 11250, value: 11250 },
      ],
    };
  },

  mounted() {
    this.changeAreaUnit();
    this.getProperties();
    this.getParse();
    this.getParentCategories();
  },
  props: {
    testProp: {
      type: String,
      default: "This is default",
    },
    url: {
      type: String,
      default: () => null,
      required: true,
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
      default: () => false,
    },
    chosenlist: {
      type: [],
    },
    cities: {
      type: [],
    },
    chosenfullist: {
      type: [],
    },
    price_list: {
      type: [],
    },
    current_currency: {
      type: "",
    },
    parent_id: {
      type: Number,
    },
  },

  computed: {
    resultTitle() {
      const type = new URLSearchParams(window.location.search).get("type");

      if (type === "sale") {
        return "Selling";
      }

      if (type === "rent") {
        return "Renting";
      }

      return "Results";
    },
  },
  methods: {
    openModal: function () {
      $(".modal").css("display", "block !important");
    },
    getParse: function () {
      this.price_list = JSON.parse(this.price_list);
      this.chosenlist = JSON.parse(this.chosenlist);
      this.cities = JSON.parse(this.cities);
    },
    getLocationKeywordsArrayFromUrl: function (sParam = "k[]") {
      let params = new URLSearchParams(window.location.search);
      let value = params.getAll(sParam);
      return value;
    },
    getParamByName: function (name, url = window.location.href) {
      name = name.replace(/[\[\]]/g, "\\$&");
      var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return "";
      return decodeURIComponent(results[2].replace(/\+/g, " "));
    },
    getParentCategories: function () {
      let url = "ajax/get-parent-categories";
      axios.get(url).then((res) => {
        this.parent_categories = res.data;
      });
    },
    getChildCategories: function (id) {
      this.category_id = id;
      this.child_categories = [];
      this.child_category_id = "";
      this.parent_id = id;
      let url = "ajax/get-child-categories?id=" + id;
      axios.get(url).then((res) => {
        this.child_categories = res.data;
      });
    },
    getProperties: function (e, page = 1) {
      var arr = $.parseJSON(this.chosenfullist);
      var max_price = $("input[name='max_price']").val();
      var min_price = $("input[name='min_price']").val();
      var max_unit = $("input[name='max_unit']").val();
      var min_unit = $("input[name='min_unit']").val();
      var city_name = $("input[name='location']").val();

      if (max_price != "") this.max_price = max_price;
      if (min_price != "") this.min_price = min_price;
      if (max_unit != "") this.max_unit = max_unit;
      if (min_unit != "") this.min_unit = min_unit;

      let url = this.url + "?type=" + this.type;
      if (this.coordinates && this.coordinates.length > 0) {
        this.coordinates.forEach((point, index) => {
          url += `&coordinates[${index}][lat]=${point.lat}&coordinates[${index}][lng]=${point.lng}`;
        });
      }
      this.data = [];
      this.links = {};
      this.isLoading = true;

      if (this.property_type != "") {
        url += "?property_type=" + this.property_type;
      } else {
        var url_type = location.href;
        var url_ty = url_type ? url_type.indexOf("type") : -1;
        if (url_ty > 0) {
          var url_ty = new URL(location.href).searchParams.get("type");
          url += "&property_type=" + url_ty;
        }
      }
      if (page == "") {
        page = 1;
      }
      if (this.property_id) {
        url += "&property_id=" + this.property_id;
      }
      if (this.project_id) {
        url += "&project_id=" + this.project_id;
      }
      if (city_name) {
        url += "&location=" + city_name;
      }

      if (Number.isInteger(Number(this.bedroom)) && Number(this.bedroom) > 0) {
        url += "&bedroom=" + this.bedroom;
      }

      if (
        Number.isInteger(Number(this.bathroom)) &&
        Number(this.bathroom) > 0
      ) {
        url += "&bathroom=" + this.bathroom;
      }

      if (this.floor) {
        url += "&floor=" + this.floor;
        this.floor = this.floor;
      } else {
        var url_location = location.href;
        var url_floor = url_location ? url_location.indexOf("floor") : -1;
        if (url_floor > 0) {
          var url_f = new URL(location.href).searchParams.get("floor");
          url += "&floor=" + url_f;
          this.floor = url_f;
        }
      }

      if (
        Number.isInteger(Number(this.min_price)) &&
        Number(this.min_price) > 0
      ) {
        url += "&min_price=" + this.min_price;
      }

      if (
        Number.isInteger(Number(this.max_price)) &&
        Number(this.max_price) > 0
      ) {
        url += "&max_price=" + this.max_price;
      }

      if (Number(this.min_unit) && Number(this.min_unit) > 0) {
        url += "&min_square=" + this.min_unit;
      }

      if (Number(this.max_unit) && Number(this.max_unit) > 0) {
        url += "&max_square=" + this.max_unit;
      }

      this.current_unit = this.current_unit
        ? this.current_unit
        : "(Square feet)";
      this.unit = this.unit ? this.unit : "ft²";

      if (this.current_unit == "(Square feet)") {
        url += "&unit=" + "ft²";
      }
      if (this.current_unit == "(Square meter)") {
        url += "&unit=" + "m²";
      }
      if (this.current_unit == "(Yards)") {
        url += "&unit=" + "yard";
      }
      if (this.current_unit == "(Marla)") {
        url += "&unit=" + "marla";
      }
      if (this.current_unit == "(Kanal)") {
        url += "&unit=" + "kanal";
      }

      url += "&city_id=" + this.city_id;

      this.search_data_chosen = this.chosenlist;
      var searchLocations = [];
      try {
        searchLocations = chipArray;
      } catch (e) {
        searchLocations = this.getLocationKeywordsArrayFromUrl();
      }

      if (searchLocations.length > 0) {
        $.each(searchLocations, function (index, val) {
          var jsonObject;
          try {
            jsonObject = JSON.parse(val);
          } catch (e) {
            jsonObject = val;
          }
          url += "&keyword[]=" + jsonObject["id"];
        });
      } else {
        var url_location = location.href;
        var url_k = url_location ? url_location.indexOf("k") : -1;
      }
      if (this.sort_by) {
        url += "&sort_by=" + this.sort_by;
      }

      if (this.category_id && !this.child_category_id) {
        url += "&category_id=" + this.category_id;
      } else {
        url += "&category_id=" + this.child_category_id;
      }

      axios
        .get(url, {
          params: {
            page,
          },
        })
        .then((response) => {
          this.data = response.data.data;
          this.links = response.data.meta;
          this.isLoading = false;

          $("#filterModal").hide();
          $("#filterModal").removeClass("fade");

          this.$nextTick(() => {
            if (window.setWishListCount) {
              window.setWishListCount();
            }
          });

          this.insertMarkers();
        });
    },
    addInfoWindow: function (marker, message) {
      var infoWindow = new google.maps.InfoWindow({
        content: message,
      });

      google.maps.event.addListener(marker, "click", function () {
        infoWindow.open(map, marker);
      });
    },
    getList: function () {
      var k = this.location;
      let url = "/properties?k=" + k;
      /*if(this.location)
            {
                url += '&k=' + k;
            }*/
      axios.get(url).then((res) => {
        //alert(res.data.data);
        if (res.data) {
          $(".list_suggest").html(res.data.data);
          $(".list_suggest").css("display", "block !important");
        } else {
          $(".list_suggest").css("display", "none !important");
        }
      });
    },
    resetFilters: function () {
      $(".filter-input").each(function () {
        $(this).val("").change();
        // for price range list
        $('input[data-dropdown-id="price-max"]').attr("placeholder", "Max");
        $('input[data-dropdown-id="price-min"]').attr("placeholder", "Min");
        $(".min_price_text").html("0");
        $(".max_price_text").html("Any");
        $(".price-min-ul li").removeClass("category-li-item-active");
        $(".price-max-ul li").removeClass("category-li-item-active");
        // for units range list
        $('[data-dropdown-id="unit-max"]').attr("placeholder", "Max");
        $('[data-dropdown-id="unit-min"]').attr("placeholder", "Min");
        $(".min_unit_text").html("0");
        $(".max_unit_text").html("Any");
        $(".unit-min-ul li").removeClass("category-li-item-active");
        $(".unit-max-ul li").removeClass("category-li-item-active");
      });

      this.bedroom = "Any";
      this.bathroom = "Any";
      this.min_price = 0;
      this.max_price = "Any";
      this.min_unit = 0;
      this.max_unit = "Any";
      this.parent_id = 1;
      this.category_id = "";
      this.child_category_id = "";
    },

    resetPriceFilters: function () {
      $('input[data-dropdown-id="price-max"]').attr("placeholder", "Max");
      $('input[data-dropdown-id="price-min"]').attr("placeholder", "Min");
      $(".min_price_text").html("0");
      $(".max_price_text").html("Any");
      $(".price-min-ul li").removeClass("category-li-item-active");
      $(".price-max-ul li").removeClass("category-li-item-active");
      this.min_price = 0;
      this.max_price = "Any";
    },
    resetUnitFilters: function () {
      $('[data-dropdown-id="unit-max"]').attr("placeholder", "Max");
      $('[data-dropdown-id="unit-min"]').attr("placeholder", "Min");
      $(".min_unit_text").html("0");
      $(".max_unit_text").html("Any");
      $(".unit-min-ul li").removeClass("category-li-item-active");
      $(".unit-max-ul li").removeClass("category-li-item-active");

      this.min_unit = "0";
      this.max_unit = "Any";
    },
    // Iniitialize map without creating markers
    initMap: function () {
      var mapOptions = {
        zoom: 6,
        center: {
          lat: 33.7492873,
          lng: 72.781116,
        },
      };

      this.map = new google.maps.Map(
        document.getElementById(this.mapName),
        mapOptions,
      );
      this.markerBounds = new google.maps.LatLngBounds();
    },
    changeAreaUnit: function () {
      if (this.current_unit === "(Square feet)") {
        let area_unit = "ft²";
        let url = "ajax/area_unit_update?area_unit=" + area_unit;

        axios.get(url).then((res) => {
          console.log("area unit updated system wide");
        });
      }

      $(".area-unit").on("click", function () {
        $("#filterModal").hide();
        $("#filterModal").removeClass("fade");
      });
      $("#save-changes").on("click", function () {
        $("#filterModal").show();
        $("#filterModal").addClass("fade");
        // remove previous values
        $('[data-dropdown-id="unit-max"]').val("");
        $('[data-dropdown-id="unit-min"]').val("");
        $('[data-dropdown-id="unit-max"]').attr("placeholder", "Max");
        $('[data-dropdown-id="unit-min"]').attr("placeholder", "Min");
        $(".min_unit_text").html("0");
        $(".max_unit_text").html("Any");
        $(".unit-min-ul li").removeClass("category-li-item-active");
        $(".unit-max-ul li").removeClass("category-li-item-active");

        this.min_unit = "0";
        this.max_unit = "Any";
      });
    },
    selectedUnit(event) {
      let url = "ajax/area_unit_update?area_unit=" + event.target.value;

      axios.get(url).then((res) => {
        console.log("area unit updated system wide");
      });

      if (event.target.value == "ft²") {
        this.current_unit = "(Square feet)";
        $.each(this.area_units, function (index, val) {
          if (this.unit == "marla") {
            val.value = Math.ceil(val.value * 225);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "m²") {
            val.value = Math.ceil(val.value * 10.764);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "yards") {
            val.value = Math.ceil(val.value * 9);
            val.key = val.value;
            this.unit = event.target.value;
          }

          if (this.unit == "kanal") {
            val.value = Math.ceil(val.value * 4500);
            val.key = val.value;
            this.unit = event.target.value;
          }
        });
      } else if (event.target.value == "m²") {
        this.current_unit = "(Square meter)";
        $.each(this.area_units, function (index, val) {
          if (this.unit == "ft²" || this.unit == undefined) {
            val.value = Math.ceil(val.value / 10.764);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "marla") {
            val.value = Math.ceil((val.value * 225) / 10.764);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "yards") {
            val.value = Math.ceil((val.value * 9) / 10.764);
            val.key = val.value;
            this.unit = event.target.value;
          }

          if (this.unit == "kanal") {
            val.value = Math.ceil((val.value * 4500) / 10.764);
            val.key = val.value;
            this.unit = event.target.value;
          }
        });
      } else if (event.target.value == "marla") {
        this.current_unit = "(Marla)";
        $.each(this.area_units, function (index, val) {
          if (this.unit == "ft²" || this.unit == undefined) {
            val.value = Math.ceil(val.value / 225);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "m²") {
            val.value = Math.ceil((val.value * 10.764) / 225);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "yards") {
            val.value = Math.ceil((val.value * 9) / 225);
            val.key = val.value;
            this.unit = event.target.value;
          }

          if (this.unit == "kanal") {
            val.value = Math.ceil((val.value * 4500) / 225);
            val.key = val.value;
            this.unit = event.target.value;
          }
        });
      } else if (event.target.value == "yards") {
        this.current_unit = "(Yards)";
        $.each(this.area_units, function (index, val) {
          if (this.unit == "ft²" || this.unit == undefined) {
            val.value = Math.ceil(val.value / 9);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "m²") {
            val.value = Math.ceil((val.value * 10.764) / 9);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "marla") {
            val.value = Math.ceil((val.value * 225) / 9);
            val.key = val.value;
            this.unit = event.target.value;
          }

          if (this.unit == "kanal") {
            val.value = Math.ceil((val.value * 4500) / 9);
            val.key = val.value;
            this.unit = event.target.value;
          }
        });
      } else if (event.target.value == "kanal") {
        this.current_unit = "(Kanal)";
        $.each(this.area_units, function (index, val) {
          if (this.unit == "ft²" || this.unit == undefined) {
            val.value = val.value / 4500;
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "m²") {
            val.value = Math.round((val.value * 10.764) / 4500);
            val.key = val.value;
            this.unit = event.target.value;
          }
          if (this.unit == "yards") {
            val.value = (val.value * 9) / 4500;
            val.key = val.value;
            this.unit = event.target.value;
          }

          if (this.unit == "marla") {
            val.value = (val.value * 225) / 4500;
            val.key = val.value;
            this.unit = event.target.value;
          }
        });
      } else {
        console.log("No unit selected");
      }
    },

    insertMarkers: function () {
      var mapOptions = {
        zoom: 8,
        center: {
          lat: 33.7492873,
          lng: 72.781116,
        },
      };

      var list_data = [];

      var map = new google.maps.Map(
        document.getElementById(this.mapName),
        mapOptions,
      );

      const drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: google.maps.drawing.OverlayType.MARKER,
        drawingControl: true,
        drawingControlOptions: {
          position: google.maps.ControlPosition.TOP_CENTER,
          drawingModes: [google.maps.drawing.OverlayType.POLYGON],
        },
        markerOptions: {
          icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
        },
        circleOptions: {
          fillColor: "#ffff00",
          fillOpacity: 1,
          strokeWeight: 5,
          clickable: false,
          editable: true,
          zIndex: 1,
        },
        polygonOptions: {
          fillColor: "#ffff00",
          fillOpacity: 1,
          strokeWeight: 5,
          clickable: false,
          editable: true,
          zIndex: 1,
        },
      });

      drawingManager.setMap(map);

      google.maps.event.addListener(
        drawingManager,
        "overlaycomplete",
        (event) => {
          if (event.type === google.maps.drawing.OverlayType.POLYGON) {
            const polygon = event.overlay;
            const path = polygon.getPath();
            const coordinates = [];

            for (let i = 0; i < path.getLength(); i++) {
              const point = path.getAt(i);
              coordinates.push({
                lat: point.lat(),
                lng: point.lng(),
              });
            }

            this.coordinates = coordinates;
            this.getProperties();

            // Optional: remove the drawing tools after drawing one polygon
            // drawingManager.setDrawingMode(null);
            // drawingManager.setOptions({
            //     drawingControl: false
            // });

            // Now call your method to fetch filtered properties
            // Example: this.fetchPropertiesWithinPolygon(coordinates);
          }
        },
      );

      i = 0;

      this.data.forEach((data) => {
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
          name_short: data.name_short,
        };
        i++;
      });

      var latlngbounds = new google.maps.LatLngBounds();
      var currentInfoWindow = null;
      for (var i = 0; i < list_data.length; i++) {
        var data = list_data[i];
        if (data.category_parent_id == 1)
          var contentString =
            '<div class="infowindow-wrap"><div class="thumb img-fluid img-size pt-2"><a href="' +
            data.url +
            '"><img src="' +
            data.image +
            '"   style="width: 240px;"></a></div><div class="title-info pt-2"><a href="' +
            data.url +
            '"><b>' +
            data.name_short +
            '</b></a></div><div class="location-info pt-2"><a href="' +
            data.url +
            '"><b>' +
            data.location +
            '</b></a></div><div class="price-info pt-2"><b>' +
            data.price +
            '</b></div>  <div class="room-info  pt-3" ><i class="fa fa-bed fa-2x bed-icon pr-2" aria-hidden="true"></i><b class="bed-no pr-2">' +
            data.number_bedroom +
            '</b><i class="fa fa-bath fa-2x bath-icon pr-2" aria-hidden="true"></i><b class="bed-no pr-2">' +
            data.number_bathroom +
            '</b><i class="fas fa-ruler-combined  pr-2 fa-2x square-icon" aria-hidden="true"></i><b class="square-no pr-2">' +
            data.square_text +
            "</b></div></div>";
        else
          var contentString =
            '<div class="infowindow-wrap"><div class="thumb img-fluid img-size pt-2"><a href="' +
            data.url +
            '"><img src="' +
            data.image +
            '"   style="width: 240px;"></a></div><div class="title-info pt-2"><a href="' +
            data.url +
            '"><b>' +
            data.name_short +
            '</b></a></div><div class="location-info pt-2"><a href="' +
            data.url +
            '"><b>' +
            data.location +
            '</b></a></div><div class="price-info pt-2"><b>' +
            data.price +
            '</b></div> <div class="room-info text-left pt-3" ><i class="fas fa-ruler-combined fa-2x pr-2 square-icon" aria-hidden="true"></i><b class="square-no pr-2">' +
            data.square_text +
            "</b></div> </div>";

        const infowindow = new google.maps.InfoWindow({
          content: contentString,
        });
        var iconBase = "/themes/real-scout/images/generic-3.png";

        var icon = {
          url: iconBase, // url
          scaledSize: new google.maps.Size(32, 37), // scaled size
          origin: new google.maps.Point(0, 0), // origin
          anchor: new google.maps.Point(0, 0), // anchor
        };
        var marker = new google.maps.Marker({
          position: list_data[i].latlng,
          map: map,
          title: list_data[i].name,
          icon: icon,
        });
        marker["infowindow"] = new google.maps.InfoWindow({
          content: contentString,
        });

        google.maps.event.addListener(marker, "click", function () {
          if (currentInfoWindow != null) {
            currentInfoWindow.close();
          }
          this["infowindow"].open(map, this);
          currentInfoWindow = this["infowindow"];
        });

        latlngbounds.extend(list_data[i].latlng);

        if (list_data[i].latlng == "") {
          list_data[i].latlng = new google.maps.LatLng(30.37532, 69.345116);
        }
      }

      // map.fitBounds(latlngbounds);
    },
  },
};
</script>

<style scoped>
.toggleBtn {
  margin-right: 48px;
}

.gmnoprint,
.gmnoprint * {
  display: block !important;
  opacity: 1 !important;
  z-index: 99999 !important;
}
</style>
