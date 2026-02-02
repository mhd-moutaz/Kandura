<!-- resources/views/layouts/sidebar.blade.php -->
<aside class="sidebar collapsed" id="sidebar">
    <!-- Pin Button -->
    <button class="pin-button" id="pinButton" title="Pin Sidebar">
        <i class="fas fa-thumbtack"></i>
    </button>

    <div class="logo">
        <i class="fas fa-store"></i>
        <span class="logo-text">KANDURA</span>
    </div>

    <nav>
        <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}" title="Dashboard">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>

        @can('view all admin')
        <a href="{{ route('super-admin.admins.index') }}" class="menu-item {{ request()->routeIs('super-admin.admins.*') ? 'active' : '' }}" title="Admin Management">
            <i class="fas fa-user-shield"></i>
            <span>Admin Management</span>
        </a>
        @endcan
        @can('view all role')
        <a href="{{ route('super-admin.roles.index') }}" class="menu-item {{ request()->routeIs('super-admin.roles.*') ? 'active' : '' }}" title="Roles Management">
            <i class="fas fa-user-tag"></i>
            <span>Roles Management</span>
        </a>
        @endcan
        @can('view all user')
        <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Users">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        @endcan
        @can('view all address')
        <a href="{{ route('addresses.index') }}" class="menu-item {{ request()->routeIs('addresses.*') ? 'active' : '' }}" title="Address">
            <i class="fas fa-map-marker-alt"></i>
            <span>Address</span>
        </a>
        @endcan
        @can('view design options')
        <a href="{{ route('designOptions.index') }}" class="menu-item {{ request()->routeIs('designOptions.*') ? 'active' : '' }}" title="Design Options">
            <i class="fas fa-palette"></i>
            <span>Design Options</span>
        </a>
        @endcan
        @can('view all designs')
        <a href="{{ route('designs.index') }}" class="menu-item {{ request()->routeIs('designs.*') ? 'active' : '' }}" title="Designs">
            <i class="fas fa-tshirt"></i>
            <span>Designs</span>
        </a>
        @endcan
        @can('view all order')
        <a href="{{ route('orders.index') }}" class="menu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}" title="Orders">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
        </a>
        @endcan
        @can('view all coupon')
        <a href="{{ route('coupons.index') }}" class="menu-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}" title="Coupons">
            <i class="fas fa-percent"></i>
            <span>Coupons</span>
        </a>
        @endcan
        {{-- <a href="#" class="menu-item" title="Settings">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a> --}}
    </nav>
    <div class="logout-container">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn" title="Logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>

</aside>

<!-- Overlay for pinned state on mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>
