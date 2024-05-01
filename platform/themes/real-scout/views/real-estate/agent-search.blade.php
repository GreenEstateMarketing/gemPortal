

<section class="sales-team pt-0">
    <div style="" class="img-agents">

    </div>
    <div class="container">

        <form id="agent_form" method="post" action="{{route('public.agent.search.post')}}">
            @csrf
        <h4 class="mb-5">Agent Search</h4>
        <div class="row align-items-center" >
            <div class="col-md-6">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" id="fist_name" name="first_name" placeholder="First Name" class="form-control"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" id="last_name" name="last_name" placeholder="Last Name" class="form-control"/>
                </div>
            </div>
<!--            <div class="col-md-4">
                <div class="form-group">
                    <label>Location</label>
                    <input type="text" id="location" name="location" placeholder="Location" class="form-control"/>
                </div>
            </div>-->



        </div>
            <div class="row" >
                <div class="col-md-3 ml-auto text-right">
                    <button type="submit" id="btnSubmit" class="btn btn-primary ">Search</button>
                </div>
            </div>
        <!-- end row -->
        </form>
        <h4 class="heading-center mt-5"><span>Looking</span> to Buy a Home?</h4>
        <div id="agent_search_desc">
        Home buying can be a daunting and complex process, which is why you should always have the help of a AGENT ® at your side. Discover all of the ways a AGENT ® can help you succeed with the biggest purchase of your life.
        </div>
    </div>
    <!-- end container -->
</section>
