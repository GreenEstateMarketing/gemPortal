<h3 class="text-center pt-5 wanted">Wanted</h3>
<h4 class="text-center">Looking to Buy, Sell, Rent or Invest? We have you covered!</h4>

<!--<section class="contact">
    <div class="container">

        &lt;!&ndash; end row &ndash;&gt;
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="map">

                    &lt;!&ndash; end pattern-bg &ndash;&gt;
                    <div><img src="{{ Theme::asset()->url('images/wanted01.jpg')  }}" alt="Image"></div>
                    &lt;!&ndash; end holder &ndash;&gt;
                </div>
                &lt;!&ndash; end map &ndash;&gt;
            </div>

            &lt;!&ndash; end col-6 &ndash;&gt;
            <div class="col-lg-6 bg2">
                <div class="contact-form">
                    <form id="contact" name="contact" action="{{ route('public.send.wanted') }}" method="post" class="generic-form">
                    @csrf
&lt;!&ndash;                        <div class="form-group">
                            <label>Looking To</label>

                            <select name="Buy" id="cars">
                                <option placeholder="Buy">Buy</option>
                            </select>

                        </div>&ndash;&gt;
                        <div class="row mb-2 align-items-center">



                            <div class="col-md-2 control-label ">Type</div>



                            <input type="hidden" id="type" name="type" value="sale">



                            <div class="col-md-3"><button type="button" class="btn label-primary type_sale" data-id="sale" data-type-name="SALE" style="Width:100%" value="sale"><span class="tick-selected"><i class="fas fa-check"></i></span>   SALE</button></div>



                            <div class="col-md-3"><button type="button" class="btn label-secondary type_sale" data-id="rent" data-type-name="SALE" style="Width:100%" value="rent">RENT</button></div>



                        </div>


                            <input type="hidden" id="category_id" name="category_id" value="1">

                        <div class="row mt-4">
                            <div class="col-md-2 control-label ">Category</div>
                            {!!  $html!!}



                        </div>
                        <div class="row mb-1  align-items-center">
                            {!! $sub_category !!}
                            <div class="offset-md-2 col-md-12 p">
                                <div class="category-li pcateory_data">
                                </div>
                            </div>
                        </div>
                        &lt;!&ndash; end form-group &ndash;&gt;
                        <div class="form-group">
                            <label>City</label>
                            <select id="city" name="city_id" class="selectWanted" required>
                                <option value="" >City</option>
                                @foreach($city as $key=>$val)
                                    <option value="{{$key}}">{{$val}}</option>
                                @endforeach
                            </select>
                        </div>
&lt;!&ndash;                       <div class="form-group">
                            <label>Area</label>

                            <select name="location" id="location" class="selectWanted">
                                <option placeholder="">Select Area</option>
                            </select>

                        </div>&ndash;&gt;
&lt;!&ndash;                        <div class="form-group">
                            <select id="choices-multiple-remove-button" placeholder="Select upto 5 tags" multiple>
                                    <option value="HTML">HTML</option>
                                    <option value="Jquery">Jquery</option>
                                    <option value="CSS">CSS</option>
                                    <option value="Bootstrap 3">Bootstrap 3</option>
                                    <option value="Bootstrap 4">Bootstrap 4</option>
                                    <option value="Java">Java</option>
                                    <option value="Javascript">Javascript</option>
                                    <option value="Angular">Angular</option>
                                    <option value="Python">Python</option>
                                    <option value="Hybris">Hybris</option>
                                    <option value="SQL">SQL</option>
                                    <option value="NOSQL">NOSQL</option>
                                    <option value="NodeJS">NodeJS</option>
                            </select>
                        </div>&ndash;&gt;
&lt;!&ndash;                        <div class="form-group">
                            <label>Area</label>
                            <input class="selectWanted form-control" name="area"   id="search_data" rows="6"
                                      placeholder="G-11,F-11 " required="">
                        </div>&ndash;&gt;
                            <div class="input-group mb-5">
                                <input type="text" id="search_data" style="height: 54px !important;" placeholder="G-11,F-11"  name="area"  autocomplete="off" class="form-control bg-white input-lg" />

                            </div>

 &lt;!&ndash; end form-group &ndash;&gt;
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="number" name="mobile_no" id="phone" autocomplete="off" required placeholder="Phone">

                        </div>
                        &lt;!&ndash; end form-group &ndash;&gt;


                        &lt;!&ndash; end form-group &ndash;&gt;
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" id="name" autocomplete="off" style="height: 54px" class="form-control" required placeholder="Name">

                        </div>
                        &lt;!&ndash; end form-group &ndash;&gt;


                        &lt;!&ndash; end form-group &ndash;&gt;
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" id="email" autocomplete="off" required placeholder="Eamil Address">

                        </div>
                        &lt;!&ndash; end form-group &ndash;&gt;


                        <div class="form-group mb-0">
                            <label>Comments</label>
                            <textarea name="comments" id="message" autocomplete="off" autocomplete="off" required></textarea>

                        </div>
                        <br>
                        <div class="alert alert-success text-success text-left" style="display: none;">
                            <span></span>
                        </div>
                        <div class="alert alert-danger text-danger text-left" style="display: none;">
                            <span></span>
                        </div>
                        <br>
                        &lt;!&ndash; end form-group &ndash;&gt;

                            <button id="submit" type="submit" name="submit">
                                Submit
                            </button>

                        &lt;!&ndash; end form-group &ndash;&gt;
                    </form>
                    &lt;!&ndash; end form &ndash;&gt;
                    <div class="form-group">
                        <div id="success" class="alert alert-success wow fadeInUp" role="alert"> Your message was sent successfully! We will be in touch as soon as we can. </div>
                        &lt;!&ndash; end success &ndash;&gt;
                        <div id="error" class="alert alert-danger wow fadeInUp" role="alert"> Something went wrong, try refreshing and submitting the form again. </div>
                        &lt;!&ndash; end error &ndash;&gt;
                    </div>
                    &lt;!&ndash; end form-group &ndash;&gt;
                </div>
                &lt;!&ndash; end contact-form &ndash;&gt;
            </div>
            &lt;!&ndash; end col-6 &ndash;&gt;
        </div>
        &lt;!&ndash; end row &ndash;&gt;
    </div>
    &lt;!&ndash; end container &ndash;&gt;
</section>-->
<section class="contact">
    <div class="container">

        <!-- end row -->
        <div class="row align-items-center">


            <div class="col-lg-12">
                <div class="wanted">

                    <!-- end pattern-bg -->
                    <div><img src="{{ Theme::asset()->url('images/wanted.jpg')  }}" alt="Image"></div>
                    <!-- end holder -->
                </div>
                <!-- end map -->
            </div>

            <!-- end col-6 -->
            <div class="col-sm-12 bg_box">
                <div class="contact-form">
                    <form id="contact" name="contact" action="{{ route('public.send.wanted') }}" method="post"
                        class="generic-form">
                        @csrf

                        <!-- <div class="form-group">
                                <label>Looking To</label>

                                <select name="Buy" id="cars">
                                    <option placeholder="Buy">Buy</option>
                                </select>

                            </div>-->

                        <div class="row md-1 align-items-center">



                            <div class="col-md-1 control-label control-label_wanted ">Type</div>



                            <input type="hidden" id="type" name="type" value="buy">


                            <div class="col-md-11">
                                <div class="row">
                                    <div class="col-md-4 pr-1">
                                        <button style="height: 35px;" type="button"
                                            class="btn d-block w-100 label-primary type_sale" data-id="sale"
                                            data-type-name="BUY" style="Width:10rem" value="buy">
                                            <span class="tick-selected"><i class="fas fa-check"></i></span> BUY
                                        </button>
                                    </div>
                                    <div class="col-md-4 pl-1">
                                        <button style="height: 35px;" type="button"
                                            class="btn d-block w-100 label-secondary type_rent" data-id="rent"
                                            data-type-name="RENT" style="Width:100%" value="rent">RENT
                                        </button>
                                    </div>
                                    <div class="col-md-4 pl-1">
                                        <button style="height: 35px;" type="button" id="project"
                                            class="btn d-block w-100 label-secondary type_project" data-id="project"
                                            data-type-name="PROJECT" style="Width:100%" value="project">INVEST
                                        </button>
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="col-md-5">--}}

                                {{-- </div>--}}



                            {{-- <div class="col-md-5">--}}

                                {{-- </div>--}}



                        </div>


                        <input type="hidden" id="category_id" name="category_id" value="1">

                        <div class="row mt-4 align-items-center">
                            <div class="col-md-1 control-label control-label_wanted  ">Category</div>
                            {!!  $html!!}



                        </div>
                        <div class="row mb-2  align-items-center ">
                            {!! $sub_category !!}
                            <div class="offset-md-2 col-md-12" style="padding-left: 56px !important;">
                                <div class="category-li pcateory_data">
                                </div>
                            </div>
                        </div>


                        <!-- end form-group -->


                        <!-- end form-group -->
                        <div class="form-group  ">
                            <label class="control-label_wanted">Name &nbsp;<i class="text-danger">*</i></label>
                            <input type="text" name="name" id="name" autocomplete="off"
                                title="Name must be atleast 3 characters." class="selectWanted required" required
                                placeholder="Name">

                        </div>
                        <!-- end form-group -->


                        <!-- end form-group -->
                        <div class="form-group  ">
                            <label class="control-label_wanted">Email &nbsp;<i class="text-danger">*</i></label>
                            <input type="text" name="email" id="email" autocomplete="off" class="selectWanted" required
                                placeholder="Eamil Address">

                        </div>
                        <!-- end form-group -->



                        <!-- end form-group -->
                        <div class="form-group">
                            <label class="control-label_wanted">Phone &nbsp;<i class="text-danger">*</i></label>
                            <input type="text" name="mobile_no" id="mobile_no"
                                title="Must be in valid format (03xxxxxxxxx)." autocomplete="off" class="selectWanted"
                                required placeholder="Phone">

                        </div>

                        <!-- end form-group -->

                        <div><label class="control-label_wanted">City &nbsp;<i class="text-danger">*</i></label></div>
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
                                                <option value="" style="margin-top: 4px !important;">Select project...
                                                </option>
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

                            <!-- end form-group -->
                            <div class="buttons">
                                <button id="submit_Btn" type="button" class="float-right btn-lg btn-primary mb-5 mt-5"
                                    name="submit">
                                    Submit <i class="fa fa-spinner d-none" aria-hidden="true"></i>
                                </button>
                            </div>

                        </div>



                        <!-- end form-group -->
                    </form>
                    <!-- end form -->

                    <!-- end form-group -->
                </div>
                <!-- end contact-form -->
            </div>
            <!-- end col-6 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>