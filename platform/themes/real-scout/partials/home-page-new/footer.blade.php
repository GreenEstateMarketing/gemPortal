{{--
    Path in theme: platform/themes/real-scout/partials/home-page-new/footer.blade.php

    The actual visible footer markup lives in home-page-new/site-footer.blade.php,
    rendered separately in homepagenew.blade.php right before this partial. This
    file only closes out the page: Theme::footer() (registered JS/scripts),
    this page's own script tags, then </body></html>.
--}}
{!! Theme::footer() !!} {{-- outputs the theme's registered JS/scripts --}}

 <script src="{{ Theme::asset()->url('js/new-home-page/header.js') }}"></script>
 <script src="{{ Theme::asset()->url('js/new-home-page/how-it-works.js') }}"></script>
 <script src="{{ Theme::asset()->url('js/new-home-page/property-categories.js') }}"></script>

</body>
</html>