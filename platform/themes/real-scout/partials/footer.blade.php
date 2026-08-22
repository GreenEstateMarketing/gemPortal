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
                    <li><a href="/gem-portal-disclaimer">Disclaimer</a></li>
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
                        <!-- <li><a href="{{ theme_option('facebook') }}"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="{{ theme_option('twitter') }}"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="{{ theme_option('youtube') }}"><i class="fab fa-youtube"></i></a></li>
                        <li><a href="{{ theme_option('linkedin') }}"><i class="fab fa-linkedin"></i></a></li> -->
                        <li>
    <a href="https://www.facebook.com/profile.php?id=61573161165755" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-facebook-f"></i>
    </a>
</li>

<li>
    <a href="https://x.com/Greens_GEM" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-twitter"></i>
    </a>
</li>

<li>
    <a href="https://www.youtube.com/@GreensGEM" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-youtube"></i>
    </a>
</li>

<li>
    <a href="https://g.co/kgs/o1qyAyp" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-google"></i>
    </a>
</li>
                        <li>
    <a href="https://wa.me/923068675133" target="_blank">
        <i class="fab fa-whatsapp"></i>
    </a>
</li>
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
<!-- WhatsApp Floating Button -->
<a href="https://wa.me/923068675133"
   class="gem-whatsapp"
   target="_blank"
   aria-label="Chat on WhatsApp">
    <svg xmlns="http://www.w3.org/2000/svg"
         width="32"
         height="32"
         fill="white"
         viewBox="0 0 24 24">
        <path d="M20.52 3.48A11.82 11.82 0 0012.06 0C5.5 0 .16 5.34.16 11.9c0 2.1.55 4.15 1.6 5.96L0 24l6.33-1.66a11.9 11.9 0 005.73 1.46h.01c6.56 0 11.9-5.34 11.9-11.9a11.8 11.8 0 00-3.45-8.42zM12.07 21.8a9.82 9.82 0 01-5.01-1.37l-.36-.21-3.76.99 1-3.67-.23-.38a9.83 9.83 0 01-1.5-5.25c0-5.42 4.42-9.83 9.86-9.83 2.63 0 5.1 1.03 6.95 2.88a9.77 9.77 0 012.89 6.95c0 5.43-4.43 9.84-9.84 9.84zm5.39-7.37c-.29-.14-1.72-.85-1.99-.95-.27-.1-.47-.14-.67.15-.2.29-.77.95-.95 1.15-.17.2-.35.22-.64.07-.29-.14-1.24-.45-2.36-1.44-.87-.77-1.46-1.71-1.63-2-.17-.29-.02-.45.13-.6.14-.14.29-.35.43-.52.14-.17.19-.29.29-.48.1-.2.05-.36-.02-.5-.07-.14-.67-1.62-.92-2.22-.24-.58-.48-.5-.67-.5h-.57c-.2 0-.5.07-.77.36-.27.29-1.04 1.02-1.04 2.48 0 1.46 1.07 2.87 1.22 3.07.14.2 2.1 3.21 5.08 4.5.71.31 1.27.49 1.71.63.72.23 1.38.2 1.9.12.58-.09 1.72-.7 1.96-1.37.24-.67.24-1.24.17-1.37-.07-.12-.26-.2-.55-.34z"/>
    </svg>
</a>

<style>
.gem-whatsapp{
    position:fixed;
    bottom:25px;
    right:25px;
    width:60px;
    height:60px;
    border-radius:50%;
    background:#25D366;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:0 6px 20px rgba(0,0,0,.35);
    z-index:99999;
    transition:.3s;
}

.gem-whatsapp:hover{
    transform:scale(1.1);
    background:#20ba5a;
}
</style>
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