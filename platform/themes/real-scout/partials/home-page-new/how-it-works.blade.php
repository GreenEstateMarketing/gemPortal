{{--
    Path in theme:  platform/themes/real-scout/partials/home-page-new/how-it-works.blade.php
    Rendered via:   {!! Theme::partial('home-page-new/how-it-works') !!}
    Included from:  layouts/homepagenew.blade.php, between why-choose and the footer partials

    Only the Landlord/Seller flow was in the design. The Tenant/Buyer flow's
    steps/copy below are a reasonable placeholder so the toggle has real
    content to switch to - swap the text in that panel for the real steps
    whenever you have them, no markup changes needed.

    "View Complete Seller Process" links to "#" - there's no dedicated page/
    route for it yet, point it wherever that content ends up living.
--}}
<section class="how-it-works" id="how-it-works">
    <div class="container how-it-works__inner">

        <div class="how-it-works__intro">
            <span class="how-it-works__eyebrow">{{ __('Simple & Transparent') }}</span>
            <h2 class="how-it-works__heading">{{ __('How It Works?') }}</h2>
            <p class="how-it-works__text">
                {{ __('Whether you are looking to buy, rent, sell or list a property, GEM keeps the process simple.') }}
            </p>

            <div class="how-it-works__toggle" role="tablist">
                <button type="button" class="how-it-works__toggle-btn active" data-panel="buyer" role="tab" aria-selected="true">
                    <i class="fas fa-home"></i> {{ __('Tenant / Buyer') }}
                </button>
                <button type="button" class="how-it-works__toggle-btn" data-panel="seller" role="tab" aria-selected="false">
                    <i class="fas fa-key"></i> {{ __('Landlord / Seller') }}
                </button>
            </div>
        </div>

        {{-- ============ TENANT / BUYER ============ --}}
        <div class="how-it-works__panel active" data-panel="buyer">
            <span class="how-it-works__eyebrow">{{ __('For Tenant / Buyer') }}</span>
            <h3 class="how-it-works__subheading">{{ __('Find. Explore. Connect. Move.') }}</h3>

            <div class="how-it-works__steps">
                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">01</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-search"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Search') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Search properties based on location, type, price and your requirements.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">02</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-home"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Explore') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Review property details, images, features and available facilities.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">03</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-comments"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Connect') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Contact the owner or connect with a professional GEM agent.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">04</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-key"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Move Forward') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Complete your transaction and take the next step toward your property.') }}</p>
                </div>
            </div>

            <a href="#" class="how-it-works__cta">{{ __('View Complete Buyer Process') }} <i class="fas fa-arrow-right"></i></a>
        </div>

        {{-- ============ LANDLORD / SELLER ============ --}}
        <div class="how-it-works__panel" data-panel="seller">
            <span class="how-it-works__eyebrow">{{ __('For Landlord / Seller') }}</span>
            <h3 class="how-it-works__subheading">{{ __('List Your Property With Confidence.') }}</h3>

            <div class="how-it-works__steps">
                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">01</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-file-medical"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Submit Ad') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Fill in your ad content, upload property pictures and provide ownership documents.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">02</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-user-check"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Choose Agent') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Pick your favorite agent or let GEM choose the right professional for you.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">03</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-clipboard-check"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Ad Verification') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Verification of ad content, pictures, location and ownership documentation.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">04</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-file-signature"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Sign Contract') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Sign the Letter of Representation with GEM Properties.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">05</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-credit-card"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Listing Payment') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Pay the required fee for your property advertisement listing.') }}</p>
                </div>

                <i class="fas fa-long-arrow-alt-right how-it-works__arrow"></i>

                <div class="how-it-works__step">
                    <span class="how-it-works__step-number">06</span>
                    <span class="how-it-works__step-icon"><i class="fas fa-clipboard-list"></i></span>
                    <h4 class="how-it-works__step-title">{{ __('Ad Listing') }}</h4>
                    <p class="how-it-works__step-text">{{ __('Your property becomes live for 03 / 06 months according to the package.') }}</p>
                </div>
            </div>

            <a href="#" class="how-it-works__cta">{{ __('View Complete Seller Process') }} <i class="fas fa-arrow-right"></i></a>
        </div>

    </div>
</section>
