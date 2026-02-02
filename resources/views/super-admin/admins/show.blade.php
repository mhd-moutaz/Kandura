@extends('layouts.admin')

@section('title', 'Admin Details')

@push('styles')
    <link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <h2>Admin Details</h2>
        </div>
        <div class="header-right">
            <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
                <i class="fas fa-arrow-left"></i> Back to List
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
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    @else
                        <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                            <i class="fas fa-pause-circle"></i> Inactive
                        </span>
                    @endif
                    <span class="badge" style="background:#dbeafe;color:#1e40af;">
                        <i class="fas fa-user-shield"></i> Admin
                    </span>
                </div>
            </div>
            <div style="display:flex;gap:10px;">
                <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Edit Admin
                </a>
            </div>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:20px;">
            <div>
                <h3 style="margin:0 0 15px 0;color:#1f2937;font-size:16px;">
                    <i class="fas fa-info-circle" style="color:#3b82f6;"></i> Basic Information
                </h3>
                <div style="background:#f8fafc;padding:15px;border-radius:8px;">
                    <div class="admin-detail">
                        <i class="fas fa-envelope"></i>
                        <span class="label">Email:</span>
                        <span class="value">{{ $admin->email }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-phone"></i>
                        <span class="label">Phone:</span>
                        <span class="value">{{ $admin->phone }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-calendar-plus"></i>
                        <span class="label">Created:</span>
                        <span class="value">{{ $admin->created_at->format('Y-m-d H:i') }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-calendar-check"></i>
                        <span class="label">Last Updated:</span>
                        <span class="value">{{ $admin->updated_at->format('Y-m-d H:i') }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 style="margin:0 0 15px 0;color:#1f2937;font-size:16px;">
                    <i class="fas fa-chart-line" style="color:#10b981;"></i> Statistics
                </h3>
                <div style="background:#f8fafc;padding:15px;border-radius:8px;">
                    <div class="admin-detail">
                        <i class="fas fa-user-tag"></i>
                        <span class="label">Roles:</span>
                        <span class="value">
                            <strong style="color:#3b82f6;">{{ $admin->roles->count() }}</strong> assigned
                        </span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-check-circle"></i>
                        <span class="label">Email Verified:</span>
                        <span class="value">
                            @if ($admin->email_verified_at)
                                <span style="color:#10b981;">
                                    <i class="fas fa-check"></i> Yes
                                </span>
                            @else
                                <span style="color:#ef4444;">
                                    <i class="fas fa-times"></i> No
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
                <i class="fas fa-user-shield" style="color:#8b5cf6;"></i> Assigned Roles
                <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $admin->roles->count() }} roles)</span>
            </h3>
        </div>

        @if ($admin->roles->count() > 0)
            <div class="roles-grid-container">
                @foreach ($admin->roles as $role)
                    <div class="role-card selected" style="pointer-events:none;">
                        <div class="role-info">
                            <span class="role-title">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                            <span class="role-count">
                                <i class="fas fa-key"></i> {{ $role->permissions->count() }} Permissions
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
                <p style="margin:0;">No roles assigned yet</p>
            </div>
        @endif
    </div>

    <!-- Permissions Card -->
    <div class="table-card">
        <div style="margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #e5e7eb;">
            <h3 style="margin:0;color:#1f2937;">
                <i class="fas fa-key" style="color:#f59e0b;"></i> Effective Permissions
                <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $admin->getAllPermissions()->count() }}
                    permissions)</span>
            </h3>
            <p style="margin:5px 0 0 0;font-size:13px;color:#6b7280;">
                Permissions inherited from assigned roles
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
                    {{ $group }} Permissions
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
                <p style="font-size:18px;font-weight:500;">No Permissions Assigned</p>
                <p style="font-size:14px;margin-top:8px;">This admin has no permissions yet</p>
                <a href="{{ route('super-admin.admins.edit', $admin) }}" class="btn btn-primary"
                    style="margin-top:20px;">
                    <i class="fas fa-plus"></i> Assign Permissions
                </a>
            </div>
        @endif
    </div>

@endsection
