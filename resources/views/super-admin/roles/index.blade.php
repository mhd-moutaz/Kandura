@extends('layouts.admin')

@section('title', __('messages.roles_management'))

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

    @if(session('error'))
        <div class="alert-auto-hide" style="background:#fee2e2;color:#991b1b;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="search-filter-card">
        <form method="GET" action="{{ route('super-admin.roles.index') }}" id="searchFilterForm">
            <div class="search-filter-grid">

                <!-- Search Input -->
                <div class="search-group">
                    <label for="search">{{ __('messages.search') }}</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}"
                        placeholder="{{ __('messages.search_by_role_name') }}">
                </div>

                <!-- Guard Filter -->
                <div class="filter-group">
                    <label for="guard_name">{{ __('messages.guard_type') }}</label>
                    <select name="guard_name" id="guard_name">
                        <option value="">{{ __('messages.all_guards') }}</option>
                        <option value="web" {{ request('guard_name') == 'web' ? 'selected' : '' }}>{{ __('messages.web_admin_panel') }}</option>
                        <option value="api" {{ request('guard_name') == 'api' ? 'selected' : '' }}>{{ __('messages.api_mobile') }}</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div class="sort-group">
                    <label for="sort_dir">{{ __('messages.sort_direction') }}</label>
                    <select name="sort_dir" id="sort_dir">
                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>A-Z</option>
                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Z-A</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                </button>
                <a href="{{ route('super-admin.roles.index') }}" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> {{ __('messages.reset') }}
                </a>
                <a href="{{ route('super-admin.roles.create') }}" class="btn btn-primary" style="margin-left: auto;">
                    <i class="fas fa-plus"></i> {{ __('messages.create_new_role') }}
                </a>
            </div>
        </form>
    </div>

    <!-- Roles Grid -->
    <div class="users-grid">
        @forelse($roles as $role)
            <div class="user-card">
                <!-- Card Header -->
                {{-- <div class="card-header">
                    <!-- Role Avatar -->
                    <div class="user-avatar">
                        <div class="avatar-placeholder">
                            {{ strtoupper(substr($role->name, 0, 1)) }}
                        </div>
                    </div>

                    <!-- Guard Badge -->
                    @if($role->guard_name == 'web')
                        <span class="status-badge active">
                            <i class="fas fa-shield-alt"></i> Web Guard
                        </span>
                    @else
                        <span class="status-badge inactive">
                            <i class="fas fa-mobile-alt"></i> API Guard
                        </span>
                    @endif
                </div> --}}

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Role Name -->
                    <h3 class="user-name">{{ ucwords(str_replace('_', ' ', $role->name)) }}</h3>
                    <p class="user-id">ID: #ROLE-{{ $role->id }}</p>

                    <!-- Role Details -->
                    <div class="user-details">
                        <div class="detail-item">
                            <label>{{ __('messages.role_type') }}</label>
                            @if(in_array($role->name, ['user', 'admin', 'super_admin']))
                                <p><span style="color:#f59e0b;">●</span> {{ __('messages.system_role') }}</p>
                            @else
                                <p><span style="color:#3b82f6;">●</span> {{ __('messages.custom_role') }}</p>
                            @endif
                        </div>

                        <div class="detail-item">
                            <label>{{ __('messages.guard') }}</label>
                            <p>{{ $role->guard_name }}</p>
                        </div>

                        <div class="detail-item">
                            <label>{{ __('messages.permissions') }}</label>
                            <p class="wallet-balance">{{ $role->permissions->count() }} {{ __('messages.permissions') }}</p>
                        </div>

                        <div class="detail-item">
                            <label>{{ __('messages.users') }}</label>
                            <p>{{ $role->users->count() }} {{ __('messages.users') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card Footer -->
                <div class="card-footer">
                    <a href="{{ route('super-admin.roles.show', $role) }}" class="btn-action edit">
                        <i class="fas fa-eye"></i> {{ __('messages.view') }}
                    </a>

                    <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn-action wallet">
                        <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                    </a>

                    @if(!in_array($role->name, ['user', 'admin', 'super_admin']) && $role->users->count() == 0)
                    <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action delete"
                            onclick="return confirm('{{ __('messages.delete_role_confirm') }}')">
                            <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-user-tag"></i>
                <h3>{{ __('messages.no_roles_found') }}</h3>
                <p>{{ __('messages.no_roles_match') }}</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($roles->hasPages())
        <div class="pagination-wrapper">
            {{ $roles->links() }}
        </div>
    @endif

</div>

@endsection
