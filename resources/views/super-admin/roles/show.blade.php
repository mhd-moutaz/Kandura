@extends('layouts.admin')

@section('title', 'Role Details')

@push('styles')
<link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>Role Details</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('super-admin.roles.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Role Info Card -->
<div class="table-card" style="margin-bottom:20px;">
    <div style="display:flex;align-items:center;gap:20px;margin-bottom:20px;padding-bottom:20px;border-bottom:2px solid #e5e7eb;">
        <div class="admin-avatar" style="width:80px;height:80px;font-size:32px;background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            {{ strtoupper(substr($role->name, 0, 1)) }}
        </div>
        <div style="flex:1;">
            <h2 style="margin:0 0 8px 0;color:#1f2937;">{{ ucwords(str_replace('_', ' ', $role->name)) }}</h2>
            <div style="display:flex;gap:15px;flex-wrap:wrap;">
                @if($role->guard_name == 'web')
                    <span class="badge" style="background:#dbeafe;color:#1e40af;">
                        <i class="fas fa-desktop"></i> Web Guard
                    </span>
                @else
                    <span class="badge" style="background:#ddd6fe;color:#5b21b6;">
                        <i class="fas fa-mobile-alt"></i> API Guard
                    </span>
                @endif
                @if(in_array($role->name, ['user', 'admin', 'super_admin']))
                    <span class="badge" style="background:#fef3c7;color:#92400e;">
                        <i class="fas fa-shield-alt"></i> System Role
                    </span>
                @else
                    <span class="badge" style="background:#e0e7ff;color:#3730a3;">
                        <i class="fas fa-star"></i> Custom Role
                    </span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> Edit Role
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
                    <i class="fas fa-calendar-plus"></i>
                    <span class="label">Created:</span>
                    <span class="value">{{ $role->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="admin-detail">
                    <i class="fas fa-calendar-check"></i>
                    <span class="label">Last Updated:</span>
                    <span class="value">{{ $role->updated_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>

        <div>
            <h3 style="margin:0 0 15px 0;color:#1f2937;font-size:16px;">
                <i class="fas fa-chart-line" style="color:#10b981;"></i> Statistics
            </h3>
            <div style="background:#f8fafc;padding:15px;border-radius:8px;">
                <div class="admin-detail">
                    <i class="fas fa-key"></i>
                    <span class="label">Permissions:</span>
                    <span class="value">
                        <strong style="color:#3b82f6;">{{ $role->permissions->count() }}</strong> total
                    </span>
                </div>
                <div class="admin-detail">
                    <i class="fas fa-users"></i>
                    <span class="label">Users:</span>
                    <span class="value">
                        <strong style="color:#3b82f6;">{{ $role->users->count() }}</strong> assigned
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Permissions Card -->
<div class="table-card" style="margin-bottom:20px;">
    <div style="margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #e5e7eb;">
        <h3 style="margin:0;color:#1f2937;">
            <i class="fas fa-key" style="color:#f59e0b;"></i> Assigned Permissions
            <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $role->permissions->count() }} permissions)</span>
        </h3>
    </div>

    @if($role->permissions->count() > 0)
        @php
            $rolePermissionNames = $role->permissions->pluck('name')->toArray();
        @endphp

        <div class="permissions-wrapper">
            <div class="permissions-group-grid">
                @foreach($permissions as $resource => $resourcePermissions)
                    <div class="perm-group-title">
                        <i class="fas fa-shield-alt" style="color:#3b82f6;"></i>
                        {{ ucwords($resource) }} Permissions
                        <span style="color:#6b7280;font-size:12px;font-weight:normal;margin-left:5px;">
                            ({{ collect($resourcePermissions)->filter(function($perm) use ($rolePermissionNames) {
                                return in_array($perm->name, $rolePermissionNames);
                            })->count() }}/{{ count($resourcePermissions) }})
                        </span>
                    </div>
                    @foreach($resourcePermissions as $permission)
                        <div class="permission-tag {{ in_array($permission->name, $rolePermissionNames) ? 'active' : '' }}">
                            <i class="fas {{ in_array($permission->name, $rolePermissionNames) ? 'fa-check-circle' : 'fa-circle' }}"></i>
                            {{ $permission->name }}
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    @else
        <div style="text-align:center;padding:60px;color:#9ca3af;">
            <i class="fas fa-key" style="font-size:64px;margin-bottom:20px;display:block;opacity:0.5;"></i>
            <p style="font-size:18px;font-weight:500;">No permissions assigned</p>
            <p style="font-size:14px;margin-top:8px;">Edit this role to add permissions</p>
        </div>
    @endif
</div>

<!-- Users with this Role -->
<div class="table-card">
    <div style="margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #e5e7eb;">
        <h3 style="margin:0;color:#1f2937;">
            <i class="fas fa-users" style="color:#8b5cf6;"></i> Users with this Role
            <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $role->users->count() }} users)</span>
        </h3>
    </div>

    @if($role->users->count() > 0)
        <div class="admins-grid">
            @foreach($role->users as $user)
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div class="admin-avatar">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <div class="admin-info">
                            <div class="admin-name">{{ $user->name }}</div>
                            <div class="admin-email">{{ $user->email }}</div>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        <div class="admin-detail">
                            <i class="fas fa-calendar"></i>
                            <span class="label">Joined:</span>
                            <span class="value">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="admin-badges">
                            @if($user->is_active)
                                <span class="badge success" style="background:#d1fae5;color:#065f46;">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            @else
                                <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                                    <i class="fas fa-pause-circle"></i> Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div style="text-align:center;padding:60px;color:#9ca3af;">
            <i class="fas fa-users" style="font-size:64px;margin-bottom:20px;display:block;opacity:0.5;"></i>
            <p style="font-size:18px;font-weight:500;">No users assigned</p>
            <p style="font-size:14px;margin-top:8px;">This role has not been assigned to any users yet</p>
        </div>
    @endif
</div>

<!-- Danger Zone -->
@if(!in_array($role->name, ['user', 'admin', 'super_admin']))
<div class="table-card" style="margin-top:20px;border:2px solid #fecaca;">
    <h3 style="margin:0 0 10px 0;color:#991b1b;">
        <i class="fas fa-exclamation-triangle"></i> Danger Zone
    </h3>
    <p style="color:#6b7280;margin-bottom:15px;">Deleting this role is permanent and cannot be undone.</p>

    @if($role->users->count() > 0)
    <div class="alert-auto-hide" style="background:#fef3c7;color:#92400e;">
        <i class="fas fa-exclamation-triangle"></i>
        Cannot delete this role because it is assigned to {{ $role->users->count() }} user(s).
        Please remove this role from all users first.
    </div>
    @else
    <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST"
          onsubmit="return confirm('Are you absolutely sure you want to delete this role? This action cannot be undone.');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn" style="background:#dc2626;color:white;">
            <i class="fas fa-trash"></i> Delete Role
        </button>
    </form>
    @endif
</div>
@endif

@endsection
