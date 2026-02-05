@extends('layouts.admin')

@section('title', __('messages.admin_management'))

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
                        <label for="search">{{ __('messages.search') }}</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="{{ __('messages.search_by_name_email') }}">
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-group">
                        <label for="is_active">{{ __('messages.status') }}</label>
                        <select name="is_active" id="status">
                            <option value="">{{ __('messages.all_status') }}</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>{{ __('messages.active') }}</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>{{ __('messages.inactive') }}</option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="sort-group">
                        <label for="sort_dir">{{ __('messages.sort_direction') }}</label>
                        <select name="sort_dir" id="sort_by">
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>{{ __('messages.newest_first') }}</option>
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>{{ __('messages.oldest_first') }}</option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="filter-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                    </button>
                    <a href="{{ route('super-admin.admins.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> {{ __('messages.reset') }}
                    </a>
                    <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary" style="margin-left: auto;">
                        <i class="fas fa-plus"></i> {{ __('messages.create_admin') }}
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
                            {{ $admin->is_active ? __('messages.active') : __('messages.inactive') }}
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
                                <label>{{ __('messages.email') }}</label>
                                <p>{{ $admin->email }}</p>
                            </div>

                            <div class="detail-item">
                                <label>{{ __('messages.phone') }}</label>
                                <p>{{ $admin->phone ?? 'N/A' }}</p>
                            </div>

                            <div class="detail-item">
                                <label>{{ __('messages.roles_permissions') }}</label>
                                @if($admin->roles->count() > 0)
                                    @foreach($admin->roles as $role)
                                        <p class="wallet-balance">
                                            <strong>{{ $role->name }}:</strong> {{ $role->permissions->count() }} {{ __('messages.permissions') }}
                                        </p>
                                    @endforeach
                                @else
                                    <p>{{ __('messages.no_roles_assigned') }}</p>
                                @endif
                            </div>

                            <div class="detail-item">
                                <label>{{ __('messages.joined') }}</label>
                                <p>{{ $admin->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer">
                        <a href="{{ route('super-admin.admins.show', $admin) }}" class="btn-action edit">
                            <i class="fas fa-eye"></i> {{ __('messages.view') }}
                        </a>

                        <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn-action wallet">
                            <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                        </a>

                        <form action="{{ route('super-admin.admins.destroy', $admin) }}" method="POST" class="delete-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-action delete"
                                onclick="return confirm('{{ __('messages.delete_admin_confirm') }}')">
                                <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-user-shield"></i>
                    <h3>{{ __('messages.no_admins_found') }}</h3>
                    <p>{{ __('messages.no_admins_match') }}</p>
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
