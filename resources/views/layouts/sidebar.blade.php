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
        <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}" title="Users">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        <a href="{{ route('addresses.index') }}" class="menu-item {{ request()->routeIs('addresses.*') ? 'active' : '' }}" title="Address">
            <i class="fas fa-map-marker-alt"></i>
            <span>Address</span>
        </a>
        <a href="{{ route('designOptions.index') }}" class="menu-item {{ request()->routeIs('designOptions.*') ? 'active' : '' }}" title="Design Options">
            <i class="fas fa-palette"></i>
            <span>Design Options</span>
        </a>
        <a href="{{ route('designs.index') }}" class="menu-item {{ request()->routeIs('designs.*') ? 'active' : '' }}" title="Designs">
            <i class="fas fa-tshirt"></i>
            <span>Designs</span>
        </a>
        <a href="{{ route('orders.index') }}" class="menu-item {{ request()->routeIs('orders.*') ? 'active' : '' }}" title="Orders">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
        </a>
        <a href="{{ route('coupons.index') }}" class="menu-item {{ request()->routeIs('coupons.*') ? 'active' : '' }}" title="Coupons">
            <i class="fas fa-percent"></i>
            <span>Coupons</span>
        </a>
        <a href="#" class="menu-item" title="Settings">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
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
