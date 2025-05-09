<div class="page-header navbar navbar-static-top test">
    <div class="page-header-inner">

        <div class="page-logo">
            @if (setting('admin_logo') || config('core.base.general.logo'))
                <a href="{{ route('dashboard.index') }}">
                    <img src="{{ setting('admin_logo') ? RvMedia::getImageUrl(setting('admin_logo')) : url(config('core.base.general.logo')) }}"
                         alt="logo" class="logo-default"/>
                </a>
                <span id="admin-panel" class="text-white primary-font" style="font-style: italic">Admin Panel</span>
            @endif

            @auth
                <div class="menu-toggler sidebar-toggler">
                    <span></span>
                </div>
            @endauth
        </div>

        @auth
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
               data-target=".navbar-collapse">
                <span></span>
            </a>
        @endauth

        @include('core/base::layouts.partials.top-menu')
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const toggleBtn = document.querySelector('.sidebar-toggler');
        const adminPanel = document.getElementById('admin-panel');
        const body = document.body;

        if (toggleBtn && adminPanel) {
            toggleBtn.addEventListener('click', function () {
                setTimeout(function () {
                    // Botble uses 'page-sidebar-closed' to indicate closed state
                    if (body.classList.contains('page-sidebar-closed')) {
                        adminPanel.style.display = 'none';
                    } else {
                        adminPanel.style.display = 'inline';
                    }
                }, 100); // small delay to wait for class toggle
            });
        }
    });
</script>
