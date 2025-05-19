<h3 class="text-center pt-5 wanted">Wanted</h3>
<h4 class="text-center">Looking to Buy, Sell, Rent or Invest? We have you covered!</h4>

<section class="contact">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="wanted">
                    <div><img src="{{ Theme::asset()->url('images/wanted.jpg')  }}" alt="Image"></div>
                </div>
            </div>
            <div class="col-sm-12 bg_box">
                <div class="contact-form">
                    <form id="contact" name="contact" action="{{ route('public.send.wanted') }}" method="post"
                          class="generic-form">
                        @csrf
                        <div class="row md-1 align-items-center">
                            <div class="col-md-1 control-label control-label_wanted ">Type</div>
                            <input type="hidden" id="type" name="type" value="buy">
                            <div class="col-md-11">
                                <div class="row">
                                    <div class="col-md-4">
                                        <button style="height: 35px;" type="button"
                                                class="btn badge d-block w-100 label-primary type_sale" data-id="sale"
                                                data-type-name="BUY" style="Width:10rem" value="buy">
                                            <span class="tick-selected"><i class="fas fa-check"></i></span> BUY
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button style="height: 35px;" type="button"
                                                class="btn badge d-block w-100 label-secondary type_rent" data-id="rent"
                                                data-type-name="RENT" style="Width:100%" value="rent">RENT
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button style="height: 35px;" type="button" id="project"
                                                class="btn badge d-block w-100 label-secondary type_project" data-id="project"
                                                data-type-name="PROJECT" style="Width:100%" value="project">INVEST
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="category_id" name="category_id" value="1">

                        {{-- CATEGORIES --}}
                        <div class="row mt-4 align-items-center">
                            <div class="col-md-1 control-label control-label_wanted">Category</div>
                            <div class="col-md-11">
                                <ul class="list-inline m-0 parent-category text-uppercase">
                                    @foreach($categories as $key => $cat)
                                        <li
                                                class="list-inline-item badge {{ $key == 0 ? 'badge-primary' : 'badge-secondary' }} p-category"
                                                data-id="{{ $cat->id }}"
                                                data-category_name="{{ $cat->name }}"
                                                style="cursor:pointer"
                                        >
                                            {{ $cat->name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        {{-- /CATEGORIES --}}

                        {{-- SUBCATEGORIES --}}
                        <div class="row mb-2 align-items-center ">
                            @foreach($categories as $cat)
                                <div class="p{{ $cat->id }}" style="display:none">
                                    <ul class="sub-category text-uppercase">
                                        @foreach($cat->subcategories as $subcat)
                                            <li class="list-inline-item badge label-sub-category p-subcategory"
                                                data-parent-name="{{ $cat->name }}"
                                                data-id="{{ $subcat->id }}"
                                                data-category_name="{{ $subcat->name }}"
                                                style="cursor:pointer">
                                                {{ $subcat->name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                            <div class="offset-md-2 col-md-12" style="padding-left: 56px !important;">
                                <div class="category-li pcateory_data">
                                    @if($categories->count())
                                        <ul class="sub-category text-uppercase">
                                            @foreach($categories->first()->subcategories as $subcat)
                                                <li class="list-inline-item badge label-sub-category p-subcategory"
                                                    data-parent-name="{{ $categories->first()->name }}"
                                                    data-id="{{ $subcat->id }}"
                                                    data-category_name="{{ $subcat->name }}"
                                                    style="cursor:pointer">
                                                    {{ $subcat->name }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- /SUBCATEGORIES --}}

                        <div class="form-group">
                            <label class="control-label_wanted">Name &nbsp;<i class="text-danger">*</i></label>
                            <input type="text" name="name" id="name" autocomplete="off"
                                   title="Name must be atleast 3 characters." class="selectWanted required" required
                                   placeholder="Name">
                        </div>
                        <div class="form-group">
                            <label class="control-label_wanted">Email &nbsp;<i class="text-danger">*</i></label>
                            <input type="text" name="email" id="email" autocomplete="off" class="selectWanted" required
                                   placeholder="Eamil Address">
                        </div>
                        <div class="form-group">
                            <label class="control-label_wanted">Phone &nbsp;<i class="text-danger">*</i></label>
                            <input type="text" name="mobile_no" id="mobile_no"
                                   title="Must be in valid format (03xxxxxxxxx)." autocomplete="off" class="selectWanted"
                                   required placeholder="Phone">
                        </div>
                        <div>
                            <label class="control-label_wanted">City &nbsp;<i class="text-danger">*</i></label>
                        </div>
                        <div class="form-row">
                            <div class="col 7" style="padding-left: 0px;">
                                <select class="select-city-state select_Wanted1 city_id" id='city_id' name="city_id"
                                        required>
                                    <option value="" style="margin-top: 4px !important;">Select city...</option>
                                    @foreach($city as $key => $val)
                                        <option value="{{$key}}">{{$val}}</option>
                                    @endforeach
                                </select>
                                <span id="error_city" class="error-own"></span>
                            </div>
                        </div>
                        <div class="row" style="float:left">
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="control-label_wanted" style="margin-left: 3%">City Area &nbsp;<i
                                                class="text-danger">*</i></label>
                                </div>
                                <div class="row">
                                    <div class="form">
                                        <div class="col 6">
                                            <select style="width: 531px; !important;"
                                                    class="select-city-state form-control" name="city_area_id"
                                                    id='city_area_id' required>
                                                <option value="">Select city area...</option>
                                            </select>
                                            <span id="error_city_area" class="error-own"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6" id="proj-1" style="display:none">
                                <div class="row">
                                    <div class="col-md-6" style="margin-left: -10px;">
                                        <label class="control-label_wanted">Project&nbsp;<i
                                                    class="text-danger">*</i></label>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="form row col-md-6" style="padding-left: 0px;">
                                        <div class="col 6">
                                            <select style="width: 531px; !important;"
                                                    class="select-city-state form-control" name="project_select" id='project-select'
                                                    required>
                                                <option value="" style="margin-top: 4px !important;">Select project...</option>
                                                @foreach($projects as $key => $val)
                                                    <option value="{{$val}}">{{$val}}</option>
                                                @endforeach
                                            </select>
                                            <span id="error_project_select" class="error-own"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="float:left;width:100%;margin-top:2%;display:none" id="proj-2">
                            <div class="col-md-6" style="padding-left:3%">
                                <div class="row">
                                    <label class="control-label_wanted">Amount&nbsp;<i
                                                class="text-danger">*</i></label>
                                </div>
                                <div class="row">
                                    <input type="number" name="amount" id="amount" autocomplete="off"
                                           class="selectWanted" required placeholder="Amount">
                                    <span id="error_amount" class="error-own"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <label class="control-label_wanted">New Project&nbsp;</label>
                                </div>
                                <div class="row">
                                    <input style="margin-right: 1%;" type="checkbox" name="new_project"
                                           id="new-project" />
                                    <input type="text" name="new_project_value" id="new-project-value"
                                           autocomplete="off" class="selectWanted" disabled="disabled" placeholder="New Project">
                                    <span id="error_new_project_value" class="error-own"></span>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class=" textarea1" style="margin-top: 17%;">
                            <label class="control-label_wanted">Message &nbsp;<i class="text-danger">*</i></label>
                            <textarea name="comments" id="message" placeholder="Message" autocomplete="off"
                                      class="selectWanted"></textarea>
                            <br>
                            <div class="alert alert-success text-success text-left mt-2" style="display: none;">
                                <span></span>
                            </div>
                            <div class="alert validation-error text-danger text-left mt-2" style="display: none;">
                                <span></span>
                            </div>
                            <div class="alert alert-danger text-danger print-error-msg mt-2" style="display:none">
                                <ul class="mb-0"></ul>
                            </div>
                            <div class="buttons">
                                <button id="submit_Btn" type="button" class="float-right btn-lg btn-primary mb-5 mt-5"
                                        name="submit">
                                    Submit <i class="fa fa-spinner d-none" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
