<section class="footer-bar">
    <div class="container">
        <div class="inner wow fadeIn">
            <div class="row">
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.05s">
                    <figure><img src="{{ Theme::asset()->url('images/footer-icon01.png')  }}" alt="Image"></figure>
                    <h3>Address Info</h3>
                    <p>{{ theme_option('address') }}</p>
                </div>
                <!-- end col-4 -->
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.10s">
                    <figure><img src="{{ Theme::asset()->url('images/footer-icon02.png')  }}" alt="Image"></figure>
                    <h3>Working Hours</h3>
                    <p>Monday to Friday <strong>09:00</strong> to <strong>18:30</strong> <br>
                        Saturday we work until <strong>15:30</strong></p>
                </div>
                <!-- end col-4 -->
                <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.15s">
                    <figure><img src="{{ Theme::asset()->url('images/footer-icon03.png')  }}" alt="Image"></figure>
                    <h3>Sales Office</h3>
                    <p># 23 Block - A North Avenue, Gulberg
                        Greens, Islamabad</p>
                </div>
                <!-- end col-4 -->
            </div>
            <!-- end row -->
        </div>
        <!-- end inner -->
    </div>
    <!-- end container -->
</section>
<!-- end footer-bar -->
<footer class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.05s">
                @if (theme_option('logo'))
                    <a class="navbar-brand" href="{{ route('public.single') }}">
                        <img src="{{ RvMedia::getImageUrl(theme_option('logo')) }}" class="logo" height="40"
                            alt="{{ theme_option('site_name') }}">
                    </a>
                @endif
                <p>GEM has been established in 2020 to
                    introduce novelty and modernity to the real
                    estate sector of Pakistan.</p>

                <!-- end select-box -->
            </div>
            <!-- end col-4 -->
            <div class="col-lg-2 col-md-6 wow fadeInUp" data-wow-delay="0.10s">
                <ul class="footer-menu">
                    <li><a href="/">Home</a></li>
                    <li><a href="/pricing">Pricing</a></li>
                    <li><a href="/contact">Contact</a></li>
                    <li><a href="/faq">FAQ</a></li>
                    <li><a href="/return-policy">Return Policy</a></li>
                    <li><a href="/shipping-policy">Shipping/Delivery Policy</a></li>
                </ul>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-2 col-md-6 wow fadeInUp" data-wow-delay="0.15s">
                <ul class="footer-menu">
                    <li><a href="#">Suites</a></li>
                    <li><a href="#">Apartments</a></li>
                    <li><a href="#">Villas & Houses</a></li>
                    <li><a href="#">Butique Room</a></li>
                    <li><a href="#">Buildings</a></li>
                </ul>
            </div>
            <!-- end col-2 -->
            <div class="col-lg-4 wow fadeInUp" data-wow-delay="0.20s">
                <div class="contact-box">
                    <h5>CALL CENTER</h5>
                    <h3>{{ theme_option('hotline') }}</h3>
                    <p><a href="#">{{ theme_option('email') }}</a></p>
                    <ul>
                        <li><a href="{{ theme_option('facebook') }}"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="{{ theme_option('twitter') }}"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="{{ theme_option('youtube') }}"><i class="fab fa-youtube"></i></a></li>
                        <li><a href="{{ theme_option('linkedin') }}"><i class="fab fa-linkedin"></i></a></li>
                    </ul>
                </div>
                <!-- end contact-box -->
            </div>
            <!-- end col-4 -->
            <div class="col-12"><span class="copyright">© {{ date('Y') }}
                    {!! clean(theme_option('copyright')) !!}</span> <span class="creation">Site created by <a
                    href="https://linesquaretech.com/" target="_blank">LineSquare Technologies</a></span></div>
            <!-- end col-12 -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container -->
</footer>
<!-- end footer -->

<!--FOOTER-->
<!--
<footer>
    <br>
    <div class="container-fluid w90">
        <div class="row">
            <div class="col-sm-3">
                @if (theme_option('logo'))
    <p>
        <a href="{{ route('public.single') }}">
                        <img src="{{ RvMedia::getImageUrl(theme_option('logo'))  }}" style="max-height: 38px" alt="{{ theme_option('site_name') }}">
                    </a>
                </p>
                @endif
    <p><i class="fas fa-map-marker-alt"></i> &nbsp;{{ theme_option('address') }}</p>
                <p><i class="fas fa-phone-square"></i> {{ __('Hotline') }}: &nbsp;<a href="tel:{{ theme_option('hotline') }}">{{ theme_option('hotline') }}</a></p>
                <p><i class="fas fa-envelope"></i> {{ __('Email') }}: &nbsp;<a href="mailto:{{ theme_option('email') }}">{{ theme_option('email') }}</a>
                </p>
            </div>
            <div class="col-sm-9 padtop10">
                <div class="row">
                    {!! dynamic_sidebar('footer_sidebar') !!}
    </div>
</div>
</div>
<div class="row">
<div class="col-12">
{!! Theme::partial('language-switcher') !!}
    </div>
</div>
<div class="copyright">
    <div class="col-sm-12">
        <p class="text-center">
{!! clean(theme_option('copyright')) !!}
    </p>
</div>
</div>
</div>
</footer>
-->
<!--FOOTER-->

<script>
    window.trans = {
        "Price": "{{ __('Price') }}",
        "Number of rooms": "{{ __('Number of rooms') }}",
        "Number of rest rooms": "{{ __('Number of rest rooms') }}",
        "Square": "{{ __('Square') }}",
        "No property found": "{{ __('No property found') }}",
        "million": "{{ __('million') }}",
        "billion": "{{ __('billion') }}",
        "in": "{{ __('in') }}",
        "Added to wishlist successfully!": "{{ __('Added to wishlist successfully!') }}",
        "Removed from wishlist successfully!": "{{ __('Removed from wishlist successfully!') }}",
        "I care about this property!!!": "{{ __('I care about this property!!!') }}",
    }
    window.themeUrl = '{{ Theme::asset()->url('') }}';
    window.siteUrl = '{{ url('') }}';
    window.currentLanguage = '{{ App::getLocale() }}';
    //////////////select category change//////////
    $(".select-category").change(function () {
        var value = $(this).val();
        switch (value) {
            case '1':  //apartment
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '2': //villa
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '3': //condo
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '4': //house
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");
                break;
            case '5': //land
                $(".bedrooms").hide();
                $(".bathrooms").hide();
                $(".floors").hide();
                $(".prices").removeClass("mt-3");
                break;
            default: //commercial property
                $(".bedrooms").show();
                $(".bathrooms").show();
                $(".floors").show();
                $(".prices").addClass("mt-3");

        }
    });
    $("#submitBtn").click(function () {
        // executes when complete page is fully loaded, including all frames, objects and images
        var type = $("#txttypesearch").val();
        if (type == "project") {
            $(".property-advanced-search :input").prop("disabled", true);
            $('div.property-advanced-search').find('select').hide();
            $(".project-advanced-search :input").prop("disabled", false);
        } else if (type == "sale") {
            $(".property-advanced-search :input").prop("disabled", false);
            $(".project-advanced-search :input").prop("disabled", true);
            $('div.property-advanced-search').find('select').show();
        } else if (type == "rent") {
            $(".property-advanced-search :input").prop("disabled", false);
            $(".project-advanced-search :input").prop("disabled", true);
            $('div.project-advanced-search').find('select').show();
        } else {
        }
        var myForm = document.getElementById('frmhomesearch');

        chipArray.forEach(function (value) {
            var hiddenInput = document.createElement('input');

            hiddenInput.type = 'hidden';
            hiddenInput.name = 'k[]';
            hiddenInput.value = JSON.stringify(value);
            myForm.appendChild(hiddenInput);
        });
    });
    $(".typesearch a").click(function () {

        $('[data-dropdown-id="price-max"]').val("");
        $('[data-dropdown-id="price-min"]').val("");
        $('[data-dropdown-id="price-max"]').attr("placeholder", "Max");
        $('[data-dropdown-id="price-min"]').attr("placeholder", "Min");
        $('.min_price_text').html("0");
        $('.max_price_text').html("Any");

        //change for units as well
        $('[data-dropdown-id="unit-max"]').val("");
        $('[data-dropdown-id="unit-min"]').val("");
        $('[data-dropdown-id="unit-max"]').attr("placeholder", "Max");
        $('[data-dropdown-id="unit-min"]').attr("placeholder", "Min");
        $('.min_unit_text').html("0");
        $('.max_unit_text').html("Any");
    });
    window.addEventListener("pageshow", function (event) {
        var historyTraversal = event.persisted ||
            (typeof window.performance != "undefined" &&
                window.performance.navigation.type === 2);
        if (historyTraversal) {
            // Handle page restore.
            window.location.reload();
        }
    });

</script>

<!--END FOOTER-->
<!--
<div class="action_footer">
    <a href="#" class="cd-top"><i class="fas fa-arrow-up"></i></a>
    <a href="tel:{{ theme_option('hotline') }}" style="color: white;font-size: 17px;"><i class="fas fa-phone"></i> <span>  &nbsp;{{ theme_option('hotline') }}</span></a>
</div>
<div id="loading">
    <div class="lds-hourglass">
    </div>
</div> -->

{!! Theme::footer() !!}
</body>

</html>