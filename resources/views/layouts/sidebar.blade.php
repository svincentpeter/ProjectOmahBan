<div id="sidebar"
     class="c-sidebar c-sidebar-dark c-sidebar-fixed c-sidebar-lg-show c-sidebar-unfoldable {{ request()->routeIs('app.pos.*') ? 'c-sidebar-minimized' : '' }}">
    <div class="c-sidebar-brand d-md-down-none px-3">
        <a href="{{ route('home') }}" class="d-flex align-items-center text-decoration-none">
            <img class="c-sidebar-brand-full" src="{{ asset('images/logo.png') }}" alt="Omah Ban" height="32">
            <img class="c-sidebar-brand-minimized" src="{{ asset('images/logo.png') }}" alt="Omah Ban" height="32">
        </a>
    </div>

    <ul class="c-sidebar-nav">
    @include('layouts.menu')
</ul>


    <button class="c-sidebar-minimizer c-class-toggler" type="button"
            data-target="_parent" data-class="c-sidebar-minimized"></button>
</div>
