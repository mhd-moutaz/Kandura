<!-- resources/views/layouts/sidebar.blade.php -->
<aside class="sidebar collapsed" id="sidebar">

    <!-- Pin Button -->
    <button class="pin-button" id="pinButton" title="Pin Sidebar">
        <i class="fas fa-thumbtack"></i>
    </button>
    <div class="logo" dir="ltr">
        <i class="fas fa-store"></i>
        <span class="logo-text">KANDURA</span>
    </div>

    <nav>
        <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}" title="{{ __('messages.dashboard') }}">
            <i class="fas fa-home"></i>
            <span>{{ __('messages.dashboard') }}</span>
        </a>

        @can('view all admin')
        <a href="{{ route('super-admin.admins.index') }}" class="menu-item {{ request()->routeIs('super-admin.admins.*') ? 'active' : '' }}" title="{{ __('messages.admin_management') }}">
            <i class="fas fa-user-shield"></i>
            <span>{{ __('messages.admin_management') }}</span>
        </a>
        @endcan
        @can('view all role')
        <a href="{{ route('super-admin.roles.index') }}" class="menu-item {{ request()->routeIs('super-admin.roles.*') ? 'active' : '' }}" title="{{ __('messages.roles_management') }}">
            <i class="fas fa-user-tag"></i>
            <span>{{ __('messages.roles_management') }}</span>
        </a>
        @endcan
        @can('view all user')
        <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}" title="{{ __('messages.users') }}">
            <i class="fas fa-users"></i>
            <span>{{ __('messages.users') }}</span>
        </a>
        @endcan
        @can('view all address')
        <a href="{{ route('addresses.index') }}" class="menu-item {{ request()->routeIs('addresses.*') ? 'active' : '' }}" title="{{ __('messages.addresses') }}">
            <i class="fas fa-map-marker-alt"></i>
            <span>{{ __('messages.addresses') }}</span>
        </a>
        @endcan
        @can('view design options')
        <a href="{{ route('designOptions.index') }}" class="menu-item {{ request()->routeIs('designOptions.*') ? 'active' : '' }}" title="{{ __('messages.design_options') }}">
            <i class="fas fa-palette"></i>
            <span>{{ __('messages.design_options') }}</span>
        </a>
        @endcan
        @can('view all designs')
        <a href="{{ route('designs.index') }}" class="menu-item {{ request()->routeIs('designs.*') ? 'active' : '' }}" title="{{ __('messages.designs') }}">
            <i class="fas fa-tshirt"></i>
            <span>{{ __('messages.designs') }}</span>
        </a>
        @endcan
        @can('view all order')
        <a href="{{ route('orders.index') }}" class="menu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}" title="{{ __('messages.orders') }}">
            <i class="fas fa-shopping-cart"></i>
            <span>{{ __('messages.orders') }}</span>
        </a>
        @endcan
        @can('view all coupon')
        <a href="{{ route('coupons.index') }}" class="menu-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}" title="{{ __('messages.coupons') }}">
            <i class="fas fa-percent"></i>
            <span>{{ __('messages.coupons') }}</span>
        </a>
        @endcan
        {{-- <a href="#" class="menu-item" title="{{ __('messages.settings') }}">
            <i class="fas fa-cog"></i>
            <span>{{ __('messages.settings') }}</span>
        </a> --}}
    </nav>

    <!-- Language Switcher -->
    <div class="language-switcher">
        @php $currentLocale = app()->getLocale(); @endphp
        <a href="{{ route('language.switch', 'en') }}"
           class="lang-btn {{ $currentLocale === 'en' ? 'active' : '' }}"
           title="English">
            <span>EN</span>
        </a>
        <a href="{{ route('language.switch', 'ar') }}"
           class="lang-btn {{ $currentLocale === 'ar' ? 'active' : '' }}"
           title="العربية">
            <span>AR</span>
        </a>
    </div>

    <div class="logout-container">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn" title="{{ __('messages.logout') }}">
                <i class="fas fa-sign-out-alt"></i>
                <span>{{ __('messages.logout') }}</span>
            </button>
        </form>
    </div>

</aside>

<!-- Overlay for pinned state on mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
