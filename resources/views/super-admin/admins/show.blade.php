@extends('layouts.admin')

@section('title', __('messages.admin_details'))

@push('styles')
    <link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <h2>{{ __('messages.admin_details') }}</h2>
        </div>
        <div class="header-right">
            <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
            </a>
        </div>
    </div>

    <!-- Admin Info Card -->
    <div class="table-card" style="margin-bottom:20px;">
        <div
            style="display:flex;align-items:center;gap:20px;margin-bottom:20px;padding-bottom:20px;border-bottom:2px solid #e5e7eb;">
            <div class="admin-avatar" style="width:80px;height:80px;font-size:32px;">
                {{ strtoupper(substr($admin->name, 0, 1)) }}
            </div>
            <div style="flex:1;">
                <h2 style="margin:0 0 8px 0;color:#1f2937;">{{ $admin->name }}</h2>
                <div style="display:flex;gap:15px;flex-wrap:wrap;">
                    @if ($admin->is_active)
                        <span class="badge success" style="background:#d1fae5;color:#065f46;">
                            <i class="fas fa-check-circle"></i> {{ __('messages.active') }}
                        </span>
                    @else
                        <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                            <i class="fas fa-pause-circle"></i> {{ __('messages.inactive') }}
                        </span>
                    @endif
                    <span class="badge" style="background:#dbeafe;color:#1e40af;">
                        <i class="fas fa-user-shield"></i> {{ __('messages.admin') }}
                    </span>
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> {{ __('messages.edit_admin') }}
                </a>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
            <div>
                <h3 style="margin:0 0 15px 0;color:#1f2937;font-size:16px;">
                    <i class="fas fa-info-circle" style="color:#3b82f6;"></i> {{ __('messages.basic_information') }}
                </h3>
                <div style="background:#f8fafc;padding:15px;border-radius:8px;">
                    <div class="admin-detail">
                        <i class="fas fa-envelope"></i>
                        <span class="label">{{ __('messages.email') }}:</span>
                        <span class="value">{{ $admin->email }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-phone"></i>
                        <span class="label">{{ __('messages.phone') }}:</span>
                        <span class="value">{{ $admin->phone }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-calendar-plus"></i>
                        <span class="label">{{ __('messages.created_at') }}:</span>
                        <span class="value">{{ $admin->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-calendar-check"></i>
                        <span class="label">{{ __('messages.last_updated') }}:</span>
                        <span class="value">{{ $admin->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="margin:0 0 15px 0;color:#1f2937;font-size:16px;">
                    <i class="fas fa-chart-line" style="color:#10b981;"></i> {{ __('messages.statistics') }}
                </h3>
                <div style="background:#f8fafc;padding:15px;border-radius:8px;">
                    <div class="admin-detail">
                        <i class="fas fa-user-tag"></i>
                        <span class="label">{{ __('messages.roles') }}:</span>
                        <span class="value">
                            <strong style="color:#3b82f6;">{{ $admin->roles->count() }}</strong> {{ __('messages.assigned') }}
                        </span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-check-circle"></i>
                        <span class="label">{{ __('messages.email_verified') }}:</span>
                        <span class="value">
                            @if ($admin->email_verified_at)
                                <span style="color:#10b981;">
                                    <i class="fas fa-check"></i> {{ __('messages.yes') }}
                                </span>
                            @else
                                <span style="color:#ef4444;">
                                    <i class="fas fa-times"></i> {{ __('messages.no') }}
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Roles Card -->
    <div class="table-card" style="margin-bottom:20px;">
        <div style="margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #e5e7eb;">
            <h3 style="margin:0;color:#1f2937;">
                <i class="fas fa-user-shield" style="color:#8b5cf6;"></i> {{ __('messages.assigned_roles') }}
                <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $admin->roles->count() }} {{ __('messages.roles') }})</span>
            </h3>
        </div>

        @if ($admin->roles->count() > 0)
            <div class="roles-grid-container">
                @foreach ($admin->roles as $role)
                    <div class="role-card selected" style="pointer-events:none;">
                        <div class="role-info">
                            <span class="role-title">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                            <span class="role-count">
                                <i class="fas fa-key"></i> {{ $role->permissions->count() }} {{ __('messages.permissions') }}
                            </span>
                        </div>
                        <div class="check-icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div style="text-align:center;padding:40px;color:#9ca3af;">
                <i class="fas fa-user-shield" style="font-size:48px;margin-bottom:15px;"></i>
                <p style="margin:0;">{{ __('messages.no_roles_assigned_yet') }}</p>
            </div>
        @endif
    </div>

    <!-- Permissions Card -->
    <div class="table-card">
        <div style="margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #e5e7eb;">
            <h3 style="margin:0;color:#1f2937;">
                <i class="fas fa-key" style="color:#f59e0b;"></i> {{ __('messages.effective_permissions') }}
                <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $admin->getAllPermissions()->count() }}
                    {{ __('messages.permissions') }})</span>
            </h3>
            <p style="margin:5px 0 0 0;font-size:13px;color:#6b7280;">
                {{ __('messages.permissions_from_roles') }}
            </p>
        </div>

        @if ($admin->getAllPermissions()->count() > 0)
        @php
            // Get all permissions from admin's roles
            $adminPermissionNames = $admin->getAllPermissions()->pluck('name')->toArray();
        @endphp

    <div class="permissions-wrapper">
        <div class="permissions-group-grid">
            @foreach ($permissions as $group => $groupPermissions)
                <div class="perm-group-title">
                    <i class="fas fa-shield-alt" style="color:#3b82f6;"></i>
                    {{ $group }} {{ __('messages.permissions') }}
                    <span style="color:#6b7280;font-size:12px;font-weight:normal;margin-left:5px;">
                        ({{ $groupPermissions->filter(function($perm) use ($adminPermissionNames) {
                            return in_array($perm->name, $adminPermissionNames);
                        })->count() }}/{{ $groupPermissions->count() }})
                    </span>
                </div>
                @foreach ($groupPermissions as $permission)
                    <div class="permission-tag {{ in_array($permission->name, $adminPermissionNames) ? 'active' : 'role-inherited' }}">
                        <i class="fas {{ in_array($permission->name, $adminPermissionNames) ? 'fa-check-circle' : 'fa-circle' }}"></i>
                        {{ $permission->name }}
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
        @else
            <div style="text-align:center;padding:60px;color:#9ca3af;">
                <i class="fas fa-key" style="font-size:64px;margin-bottom:20px;display:block;opacity:0.3;"></i>
                <p style="font-size:18px;font-weight:500;">{{ __('messages.no_permissions_assigned') }}</p>
                <p style="font-size:14px;margin-top:8px;">{{ __('messages.admin_no_permissions') }}</p>
                <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn btn-primary"
                    style="margin-top:20px;">
                    <i class="fas fa-plus"></i> {{ __('messages.assign_permissions') }}
                </a>
            </div>
        @endif
    </div>

@endsection
