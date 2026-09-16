@php Theme::layout('homepage') @endphp

{!! do_shortcode('[featured-projects][/featured-projects]') !!}

{!! do_shortcode('[properties-by-locations][/properties-by-locations]') !!}

{!! do_shortcode('[properties-for-sale][/properties-for-sale]') !!}

{!! do_shortcode('[properties-for-rent][/properties-for-rent]') !!}

{!! do_shortcode('[latest-news][/latest-news]') !!}

{{-- ================= BLOG SECTION ================= --}}

{!! do_shortcode('
    [blog-posts
        title="Latest Real Estate Insights"
        limit="3"
        show_excerpt="yes"
        show_meta="yes"
    ][/blog-posts]
') !!}

{{-- ================= END BLOG SECTION ================= --}}