<section class="sales-team">
    <div class="container">
        <h4 class="heading-center"><span> </span>Search Results</h4>
        <div class="row">
            @if ($agents->count())
                @foreach($agents as $key => $val)
                    <div class="col-md-6">
                        <figure>
                            @if($val->image_path)
                                <img src="storage/{{ $val->image_path }}"
                                     style="border: 1px solid #fff;border-radius: 50%;" alt="Image">
                            @else
                                <img src="{{ $val->avatar_url }}" alt="Image">
                            @endif
                            <figcaption>
                                <h4>
                                    <a href="{{ route('public.agent.detail', $val->username) }}"><span>{{$val->first_name}}</span>
                                        {{$val->last_name}} </a></h4>
                                <small>{{$val->description}}</small>
                                <ul>
                                    <li><a href="#"><i class="fab fa-linkedin-in"></i>LINKEDIN</a></li>
                                    <li><a href="#"><i class="fab fa-facebook-f"></i>FACEBOOK</a></li>
                                    @if($val->phone != "")

                                        <button type="button" class="showContact btn btn-primary pt-1 pb-1"
                                                data-id="{{$val->id}}">Show Contact
                                        </button>
                                    @endif
                                    <div class="contactInfo-{{ $val->id  }} d-none" data-id="{{ $val->id  }}">
                                        <div class="phone"><i class="fa fa-phone"></i> {{$val->phone}}</div>
                                    </div>
                                    @if ($val->username)
                                        <p class="mt-2"><span class="fas fa-arrow-circle-right"></span>
                                            @if(no_of_listings($val->id) > 0)
                                                <a
                                                        href="{{ route('public.agent.detail', $val->username) }}">{{ __('properties by this agent') }}</a>
                                            @else
                                                {{ __('properties by this agent') }}
                                            @endif<i class="fa fa-home"></i> {{no_of_listings($val->id)}}</p>
                                    @endif
                                </ul>
                            </figcaption>
                        </figure>
                    </div>
                @endforeach
            @else
                <p class="heading-center">No Record found</p>
            @endif
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>