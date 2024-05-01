
<div class="widget meta-boxes agent-boxes">
    <div class="widget-title">
        <h4>
            <span> Agent Assignment</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="row">
            <div class="col-md-6">
                <input type="hidden" id="agent_list" name="agent_list" />
                <div class="dropdown">
                     <button class="btn btn-success
                    dropdown-toggle" type="button"
                        id="dropdownMenuButton"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false" style="width:50% !important;border: none !important;">
                         <span id="text_agent">Agent List</span>
                     </button>
                    <ul class="dropdown-menu"
                    aria-labelledby="dropdownMenuButton" id="dropdown-menu-member" style="width:50% !important;">



                </ul>
                    <span id="close_btn"><i style="cursor: pointer" title="unselect" class="fas fa-times pl2 pr-2 d-none custom-select-agent"></i></span>
                </div>

<!--                <select class="form-control" id="agent_list" name="agent_list">

                </select>-->
                <div class="row pt-4 agent-detail d-none">
                    <div class="col-md-2"> <!-- need to dynamic image -->
                        <img src="" class="agent-image" alt="Image">
                    </div>
                    <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-6"><b id="agent_name">Muhammad Aslam</b></div>
                            <div class="col-md-6 "><button type="button" class="showContact btn float-right btn-info pt-1 pb-1">Show Contact</button></div>
                        </div>
                        <div class="row"><div class="col-md-2 label-grey" id="agent_email">CEO</div><div class="show_contact_detail col-md-10 d-none"><span class="float-right" ><i class="fa fa-phone" aria-hidden="true"></i>&nbsp;&nbsp;&nbsp;&nbsp;<span class="label-grey" id="agent_no"> &emsp;3315678921</span></span></div></div>
                        <div class="row"><div class="col label-grey" id="agent_desc">5111 New Street, Suite 101 Burlington, Ontario L7L1V2</div></div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<div class="widget meta-boxes agent-boxes">
    <div class="widget-title">
        <h4>
            <span> Contact Details</span>
        </h4>
    </div>
    <div class="widget-body">
        <div class="row">

            <div class="col-md-6">


                <div class="divrow loginTpe mt-2">
                    <label class="pr-3">Membership Status</label>
                    <label class="pr-3">
                        <input type="radio" class="member_status pr-3 pl-3 ml-2" onclick="open_div(this)" checked="" value="existing_user" name="member_status">Existing Member	</label>
                    <label class="pr-3">
                        <input type="radio" class="member_status pr-3 pl-3" onclick="open_div(this)" value="new_user" name="member_status">New Member	</label>

                </div>
                <div class="existing_user">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email Adddress:</label><span class="red-required"> *</span>

                                <input type="email" class="form-control" placeholder="Enter email" id="email" name="email" required="" >
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pwd">Password:</label><span class="red-required"> *</span>
                                <input type="password" class="form-control" placeholder="Enter password" id="password" name="password" required="">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="new_user" style="display: none">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="full_name">Full Name:</label><span class="red-required"> *</span>
                                <input type="text" class="form-control" placeholder="Enter Full Name" id="full_name" name="full_name" required disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_email">Email Address:</label><span class="red-required"> *</span>
                                <input type="email" class="form-control" placeholder="Enter email" id="new_email" name="new_email" required disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="mobile_number">Mobile:</label><span class="red-required"> *</span>
                                <input type="text" class="form-control" placeholder="Enter Mobile" id="mobile_number" name="mobile_number" required disabled>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="new_password">Password:</label><span class="red-required"> *</span>
                                <input type="password" class="form-control" placeholder="Enter password" id="new_password" name="new_password" required disabled>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="">
                                <input type="checkbox"
                                       name="terms" id="terms" value="1" required /><label>I accept</label>
                                <span  style="cursor: pointer" class="red" data-toggle="modal" data-target="#exampleModal">GEM Terms & Conditions
                                    </span>
                                {{--{{ trans('plugins/real-estate::dashboard.gem-terms') }}--}}

                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" name="submit" id="btnSave" value="save" class="btn  btn-info">
                            Submit Property <i class="fa fa-spinner d-none" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-12">
                        <div class="alert alert-danger print-error-msg" style="display:none">
                            <ul class="mb-0"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
