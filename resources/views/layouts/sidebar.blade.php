<aside class="sidebar">
    <div class="logo">
        <i class="fas fa-store"></i>
        KANDURA
    </div>
    <nav>
        <a href="{{ route('home') }}" class="menu-item {{ request()->routeIs('home') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('users.index') }}" class="menu-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <i class="fas fa-users"></i>
            <span>Users</span>
        </a>
        <a href="{{ route('addresses.index') }}" class="menu-item {{ request()->routeIs('addresses.*') ? 'active' : '' }}" class="menu-item">
            <i class="fas fa-map-marker-alt"></i>
            <span>Address</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-shopping-cart"></i>
            <span>Orders</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-box"></i>
            <span>Products</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-tags"></i>
            <span>Categories</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-percent"></i>
            <span>Discounts</span>
        </a>
        <a href="#" class="menu-item">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </nav>
    <div class="logout-container">
        <form action="{{ route('logout') }}" method="POST" class="logout-form">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </button>
        </form>
    </div>
</aside>
