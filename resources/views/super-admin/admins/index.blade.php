@extends('layouts.admin')

@section('title', 'Admins Management')

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
            <form method="GET" action="{{ route('super-admin.admins.index') }}" id="searchFilterForm">
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
                    <a href="{{ route('super-admin.admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                    <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary" style="margin-left: auto;">
                        <i class="fas fa-plus"></i> Create Admin
                    </a>
                </div>
            </form>
        </div>

        <!-- Admins Grid -->
        <div class="users-grid">
            @forelse($admins as $admin)
                <div class="user-card">
                    <!-- Card Header -->
                    <div class="card-header">
                        <!-- Admin Avatar -->
                        <div class="user-avatar">
                            <div class="avatar-placeholder">
                                {{ strtoupper(substr($admin->name, 0, 1)) }}
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <span class="status-badge {{ $admin->is_active ? 'active' : 'inactive' }}">
                            <i class="fas fa-{{ $admin->is_active ? 'check-circle' : 'times-circle' }}"></i>
                            {{ $admin->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Admin Name -->
                        <h3 class="user-name">{{ $admin->name }}</h3>
                        <p class="user-id">ID: #ADM-{{ $admin->id }}</p>

                        <!-- Admin Details -->
                        <div class="user-details">
                            <div class="detail-item">
                                <label>Email</label>
                                <p>{{ $admin->email }}</p>
                            </div>

                            <div class="detail-item">
                                <label>Phone</label>
                                <p>{{ $admin->phone ?? 'N/A' }}</p>
                            </div>

                            <div class="detail-item">
                                <label>Roles & Permissions</label>
                                @if($admin->roles->count() > 0)
                                    @foreach($admin->roles as $role)
                                        <p class="wallet-balance">
                                            <strong>{{ $role->name }}:</strong> {{ $role->permissions->count() }} permissions
                                        </p>
                                    @endforeach
                                @else
                                    <p>No roles assigned</p>
                                @endif
                            </div>

                            <div class="detail-item">
                                <label>Joined</label>
                                <p>{{ $admin->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer">
                        <a href="{{ route('super-admin.admins.show', $admin) }}" class="btn-action edit">
                            <i class="fas fa-eye"></i> View
                        </a>

                        <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn-action wallet">
                            <i class="fas fa-edit"></i> Edit
                        </a>

                        <form action="{{ route('super-admin.admins.destroy', $admin) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action delete"
                                onclick="return confirm('Are you sure you want to delete this admin?')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-user-shield"></i>
                    <h3>No Admins Found</h3>
                    <p>No admins match your search criteria</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if ($admins->hasPages())
            <div class="pagination-wrapper">
                {{ $admins->links() }}
            </div>
        @endif

    </div>

@endsection
