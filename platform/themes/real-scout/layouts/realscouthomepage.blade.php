{!! Theme::partial('header') !!}

<!--<div id="app">
    <div id="ismain-homes">
        {!! Theme::content() !!}
    </div>
</div> -->

<section class="intro">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <figure>
                    <div class="pattern-bg" data-stellar-ratio="1.07"></div>
                    <!-- end pattern-bg -->
                    <div class="holder" data-stellar-ratio="1.10"> <img
                            src="{{ Theme::asset()->url('images/side-image01.jpg') }}" alt="Image"></div>
                    <!-- end holder -->
                </figure>
            </div>
            <!-- end col-6 -->
            <div class="col-lg-6 wow fadeInUp">
                <div class="content-box">
                    <h4><span>GEM</span> Consultancy</h4>
                    <h3>Best Investment in Pakistan</h3>
                    <p>100% refundable, safe and secure investments with GEM

                        Our criteria to choose best investment projects</p>
                    <a href="/projects" class="link">SEE OUR PROJECTS <i class="fas fa-caret-right"></i></a>
                </div>
            </div>
            <!-- end content-box -->
        </div>
        <!-- edn col-6 -->
    </div>
    <!-- end row -->

    <!-- end container -->
</section>
<!-- end intro -->
<section class="logos">
    <div class="container">
        <div class="row">
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0s">
                <figure> <img src="{{ Theme::asset()->url('images/logo01.png') }}" alt="Image">
                    <h6>Jadid Group</h6>
                </figure>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.05s">
                <figure> <img src="{{ Theme::asset()->url('images/logo02.png') }}" alt="Image">
                    <h6>Real Estate</h6>
                </figure>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.10s">
                <figure> <img src="{{ Theme::asset()->url('images/logo03.png') }}" alt="Image">
                    <h6>jadid Group</h6>
                </figure>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.15s">
                <figure> <img src="{{ Theme::asset()->url('images/logo04.png') }}" alt="Image">
                    <h6>SP Brothers</h6>
                </figure>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.20s">
                <figure> <img src="{{ Theme::asset()->url('images/logo05.png') }}" alt="Image">
                    <h6>Real Estate</h6>
                </figure>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-2 col-md-4 col-sm-6 col-6 wow fadeInUp" data-wow-delay="0.25s">
                <figure> <img src="{{ Theme::asset()->url('images/logo06.png') }}" alt="Image">
                    <h6>SP Brothers</h6>
                </figure>
            </div>
            <!-- end col-2 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>
<!-- end logos -->
<section class="properties-by-location">
    {!! do_shortcode('[properties-by-locations][/properties-by-locations]') !!}
</section>
<section class="benefits">
    <div class="container">
        <div class="row">
            <div class="col-12 wow fadeInUp">
                <h4><span>GEM</span> Property</h4>
                <h3>Decorated Flats in Pakistan</h3>
            </div>
            <!-- end col-12 -->
            <div class="col wow fadeInUp" data-wow-delay="0s">
                <figure> <img src="{{ Theme::asset()->url('images/icon-benefits01.png') }}" alt="Image"> <b></b>
                </figure>
                <h6>Near to Subway</h6>
                <span class="odometer" data-count="28" data-status="yes">0</span> <span class="extra">min</span>
            </div>
            <!-- end col -->
            <div class="col wow fadeInUp" data-wow-delay="0.05s">
                <figure> <img src="{{ Theme::asset()->url('images/icon-benefits02.png') }}" alt="Image"> <b></b>
                </figure>
                <h6>Spaces in Flats</h6>
                <span class="odometer" data-count="32" data-status="yes">0</span> <span class="extra">+</span>
            </div>
            <!-- end col -->
            <div class="col wow fadeInUp" data-wow-delay="0.10s">
                <figure> <img src="{{ Theme::asset()->url('images/icon-benefits03.png') }}" alt="Image"> <b></b>
                </figure>
                <h6>Spaces in Islamabad</h6>
                <span class="odometer" data-count="15" data-status="yes">0</span> <span class="extra">%</span>
            </div>
            <!-- end col -->
            <div class="col wow fadeInUp" data-wow-delay="0.15s">
                <figure> <img src="{{ Theme::asset()->url('images/icon-benefits04.png') }}" alt="Image"> <b></b>
                </figure>
                <h6>Spaces in Islamabad</h6>
                <span class="odometer" data-count="3" data-status="yes">0</span> <span class="extra">years</span>
            </div>
            <!-- end col -->
            <div class="col wow fadeInUp" data-wow-delay="0.20s">
                <figure> <img src="{{ Theme::asset()->url('images/icon-benefits05.png') }}" alt="Image"> <b></b>
                </figure>
                <h6>Spaces in Islamabad</h6>
                <span class="odometer" data-count="79" data-status="yes">0</span> <span class="extra">m²</span>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>
<!-- end benefits -->
<section class="recent-gallery">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-5 wow fadeInUp">
                <h4><span>Property</span> Inner Gallery</h4>
                <h3>Picture Gallery of Projects</h3>
                <a href="#" class="link">SEE ALL GALLERY <i class="fas fa-caret-right"></i></a>
            </div>
            <!-- end col-5 -->
            <div class="col-lg-7">
                <div class="row inner">
                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0s">
                        <figure data-stellar-ratio="1.07"> <a
                                href="{{ Theme::asset()->url('images/gallery-thumb01.jpg') }}" data-fancybox><img
                                    src="{{ Theme::asset()->url('images/gallery-thumb01.jpeg') }}"
                                    alt="Image"></a> </figure>
                    </div>
                    <!-- end col-4 -->
                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0.05s">
                        <figure data-stellar-ratio="1.15"> <a
                                href="{{ Theme::asset()->url('images/gallery-thumb02.jpg') }}" data-fancybox><img
                                    src="{{ Theme::asset()->url('images/gallery-thumb02.jpeg') }}"
                                    alt="Image"></a> </figure>
                    </div>
                    <!-- end col-4 -->
                    <div class="col-md-4 wow fadeInUp" data-wow-delay="0.10s">
                        <figure data-stellar-ratio="1.04"> <a
                                href="{{ Theme::asset()->url('images/gallery-thumb03.jpg') }}" data-fancybox><img
                                    src="{{ Theme::asset()->url('images/gallery-thumb03.jpeg') }}"
                                    alt="Image"></a> </figure>
                    </div>
                    <!-- end col-4 -->
                </div>
                <!-- end row -->
            </div>
            <!-- end col-7 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>
<!-- end recent-gallery -->
<section class="property-calculator">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <figure>
                    <div class="pattern-bg" data-stellar-ratio="1.03"></div>
                    <!-- end pattern-bg -->
                    <div class="holder" data-stellar-ratio="1.07"> <img
                            src="{{ Theme::asset()->url('images/side-image02.jpg') }}" alt="Image"></div>
                    <!-- end holder -->
                </figure>
            </div>
            <!-- end col-6 -->
            <div class="col-lg-6 wow fadeInUp">
                <div class="content-box">
                    <h4><span>GEM</span> Property Living Spaces</h4>
                    <h3>Decorated flats in Pakistan-ISB</h3>
                    <p>Golf Floras - Your gateway to ultimate luxury! Located at Bahria Town (Garden City) Islamabad.
                    </p>

                    <a href="/projects" class="link">SEE OUR PROJECTS <i class="fas fa-caret-right"></i></a>
                </div>
                <!-- end content-box -->
            </div>
            <!-- end col-6 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</section>
<!-- for vue components -->
<div id="app">

    <blog url="{{ route('public.ajax.posts') }}"></blog>

</div>
<!--
<section  class="recent-posts swiper-slide swiper-wrapper mb-5">
    <div class="container">
        <div class="row">


            <div class="col-12 wow fadeInUp">
                <h4><span>Recent from the</span> Blog</h4>
            </div>



           &lt;!&ndash; end col-12 &ndash;&gt;
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0s">
                <div class="post-box">
                    <figure> <img src="{{ Theme::asset()->url('images/recent-news01.jpg') }}" alt="Image"> </figure>
                    <span>24, September 2021</span>
                    <h6><a href="#">50th Anniversary of the Turner School of Construction Management </a></h6>
                    <p>The smaller male cones release pollen,
                        which fertilizes the female </p>

                    <a href="#" class="link">READ MORE <i class="fas fa-caret-right"></i></a>
                </div>
               &lt;!&ndash; end post-box &ndash;&gt;
            </div>


           &lt;!&ndash; end col-4 &ndash;&gt;
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.10s">
                <div class="post-box">
                    <figure> <img src="{{ Theme::asset()->url('images/recent-news02.jpg') }}" alt="Image"> </figure>
                    <span>06, November 2021</span>
                    <h6><a href="#">The Center for Construction Research and Training to Receive 2019 Award</a></h6>
                    <p>The smaller male cones release pollen,
                        which fertilizes the female </p>
                    <a href="#" class="link">READ MORE <i class="fas fa-caret-right"></i></a>
                </div>
               &lt;!&ndash; end post-box &ndash;&gt;
            </div>

           &lt;!&ndash; end col-4 &ndash;&gt;
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.20s">
                <div class="post-box">
                    <figure> <img src="{{ Theme::asset()->url('images/recent-news03.jpg') }}" alt="Image"> </figure>
                    <span>31, April 2021</span>
                    <h6><a href="#">Henry C. Turner Prize for Innovation in Construction Company</a></h6>
                    <p>The smaller male cones release pollen,
                        which fertilizes the female </p>

                    <a href="#" class="link">READ MORE <i class="fas fa-caret-right"></i></a>
                </div>
               &lt;!&ndash; end post-box &ndash;&gt;
            </div>



        </div>

       &lt;!&ndash; end col &ndash;&gt;

       &lt;!&ndash; end col &ndash;&gt;
    </div>
   &lt;!&ndash; end row &ndash;&gt;


   &lt;!&ndash; end container &ndash;&gt;
</section>-->
<!-- property-plans -->

{!! Theme::partial('footer') !!}
