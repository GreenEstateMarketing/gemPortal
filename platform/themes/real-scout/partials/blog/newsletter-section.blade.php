{{--
    Newsletter signup band. Posts to public.blog.subscribe (Botble
    BaseHttpResponse - redirects back with a flash message for this plain,
    non-ajax form). "newsletter_subscribed" is a dedicated flash flag set in
    PublicController::postSubscribe() so this message only shows after an
    actual subscribe, not any other page's unrelated success_msg.
--}}
<section class="blog-listing-newsletter">
    <div class="blog-listing-newsletter__inner">
        <span class="blog-listing-newsletter__eyebrow">{{ __('Stay Connected') }}</span>
        <h2 class="blog-listing-newsletter__heading">{{ __('Get Property Insights in Your Inbox') }}</h2>
        <p class="blog-listing-newsletter__text">
            {{ __('Subscribe to receive new property guides, market insights and real-estate tips from :brand.', ['brand' => theme_option('seo_title', 'GEMlisting')]) }}
        </p>

        <form action="{{ route('public.blog.subscribe') }}" method="POST" class="blog-listing-newsletter__form">
            @csrf
            <input type="email" name="email" placeholder="{{ __('Enter your email address') }}" required>
            <button type="submit">{{ __('Subscribe') }}</button>
        </form>

        @if (session('newsletter_subscribed') && session('success_msg'))
            <p class="blog-listing-newsletter__message">{{ session('success_msg') }}</p>
        @endif
        @if (session('newsletter_unsubscribed') && session('success_msg'))
            <p class="blog-listing-newsletter__message">{{ session('success_msg') }}</p>
        @endif
        @error('email')
            <p class="blog-listing-newsletter__message blog-listing-newsletter__message--error">{{ $message }}</p>
        @enderror
    </div>
</section>
