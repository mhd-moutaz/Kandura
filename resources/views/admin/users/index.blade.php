@extends('layouts.admin')

@section('title', 'Users Management')

@push('styles')
<link href="{{ asset('css/admin/users.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="users-container">

        @if (session('success'))
            <div class="alert-auto-hide">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        <!-- Search and Filter Section -->
        <div class="search-filter-card">
            <form method="GET" action="{{ route('users.index') }}" id="searchFilterForm">
                <div class="search-filter-grid">

                    <!-- Search Input -->
                    <div class="search-group">
                        <label for="search">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by name or email...">
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-group">
                        <label for="is_active">Status</label>
                        <select name="is_active" id="status">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="sort-group">
                        <label for="sort_dir">Sort Direction</label>
                        <select name="sort_dir" id="sort_by">
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </form>
        </div>

        <!-- Users Grid -->
        <div class="users-grid">
            @forelse($users as $user)
                <div class="user-card">
                    <!-- Card Header -->
                    <div class="card-header">
                        <!-- User Avatar -->
                        <div class="user-avatar">
                            @if ($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}"
                                    alt="{{ $user->name }}">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>

                        <!-- Status Badge -->
                        <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}">
                            <i class="fas fa-{{ $user->is_active ? 'check-circle' : 'times-circle' }}"></i>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- User Name -->
                        <h3 class="user-name">{{ $user->name }}</h3>
                        <p class="user-id">ID: #USR-{{ $user->id }}</p>

                        <!-- User Details -->
                        <div class="user-details">
                            <div class="detail-item">
                                <label>Email</label>
                                <p>{{ $user->email }}</p>
                            </div>

                            <div class="detail-item">
                                <label>Phone</label>
                                <p>{{ $user->phone ?? 'N/A' }}</p>
                            </div>

                            <div class="detail-item">
                                <label>Wallet Balance</label>
                                <p class="wallet-balance">${{ number_format($user->wallet->balance ?? 0, 2) }}</p>
                            </div>

                            <div class="detail-item">
                                <label>Joined</label>
                                <p>{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer">
                        <a href="{{ route('users.edit', $user) }}" class="btn-action edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <a href="{{ route('admin.wallet.show', $user) }}" class="btn-action wallet">
                            <i class="fas fa-wallet"></i> Wallet
                        </a>

                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action delete"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <h3>No Users Found</h3>
                    <p>No users match your search criteria</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        @endif

    </div>

@endsection
