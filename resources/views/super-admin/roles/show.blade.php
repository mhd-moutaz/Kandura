@extends('layouts.admin')

@section('title', __('messages.role_details'))

@push('styles')
<link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>{{ __('messages.role_details') }}</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('super-admin.roles.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
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
                        <i class="fas fa-desktop"></i> {{ __('messages.web_guard') }}
                    </span>
                @else
                    <span class="badge" style="background:#ddd6fe;color:#5b21b6;">
                        <i class="fas fa-mobile-alt"></i> {{ __('messages.api_guard') }}
                    </span>
                @endif
                @if(in_array($role->name, ['user', 'admin', 'super_admin']))
                    <span class="badge" style="background:#fef3c7;color:#92400e;">
                        <i class="fas fa-shield-alt"></i> {{ __('messages.system_role') }}
                    </span>
                @else
                    <span class="badge" style="background:#e0e7ff;color:#3730a3;">
                        <i class="fas fa-star"></i> {{ __('messages.custom_role') }}
                    </span>
                @endif
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <a href="{{ route('super-admin.roles.edit', $role) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> {{ __('messages.edit_role') }}
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
                    <i class="fas fa-calendar-plus"></i>
                    <span class="label">{{ __('messages.created_at') }}:</span>
                    <span class="value">{{ $role->created_at->format('Y-m-d H:i') }}</span>
                </div>
                <div class="admin-detail">
                    <i class="fas fa-calendar-check"></i>
                    <span class="label">{{ __('messages.last_updated') }}:</span>
                    <span class="value">{{ $role->updated_at->format('Y-m-d H:i') }}</span>
                </div>
            </div>
        </div>

        <div>
            <h3 style="margin:0 0 15px 0;color:#1f2937;font-size:16px;">
                <i class="fas fa-chart-line" style="color:#10b981;"></i> {{ __('messages.statistics') }}
            </h3>
            <div style="background:#f8fafc;padding:15px;border-radius:8px;">
                <div class="admin-detail">
                    <i class="fas fa-key"></i>
                    <span class="label">{{ __('messages.permissions') }}:</span>
                    <span class="value">
                        <strong style="color:#3b82f6;">{{ $role->permissions->count() }}</strong> {{ __('messages.total') }}
                    </span>
                </div>
                <div class="admin-detail">
                    <i class="fas fa-users"></i>
                    <span class="label">{{ __('messages.users') }}:</span>
                    <span class="value">
                        <strong style="color:#3b82f6;">{{ $role->users->count() }}</strong> {{ __('messages.assigned') }}
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
            <i class="fas fa-key" style="color:#f59e0b;"></i> {{ __('messages.assigned_permissions') }}
            <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $role->permissions->count() }} {{ __('messages.permissions') }})</span>
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
                        {{ ucwords($resource) }} {{ __('messages.permissions') }}
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
            <p style="font-size:18px;font-weight:500;">{{ __('messages.no_permissions_assigned') }}</p>
            <p style="font-size:14px;margin-top:8px;">{{ __('messages.no_permissions_assigned_description') }}</p>
        </div>
    @endif
</div>

<!-- Users with this Role -->
<div class="table-card">
    <div style="margin-bottom:20px;padding-bottom:15px;border-bottom:2px solid #e5e7eb;">
        <h3 style="margin:0;color:#1f2937;">
            <i class="fas fa-users" style="color:#8b5cf6;"></i> {{ __('messages.users_with_role') }}
            <span style="color:#6b7280;font-size:14px;font-weight:normal;">({{ $role->users->count() }} {{ __('messages.users') }})</span>
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
                            <span class="label">{{ __('messages.joined') }}:</span>
                            <span class="value">{{ $user->created_at->format('Y-m-d') }}</span>
                        </div>
                        <div class="admin-badges">
                            @if($user->is_active)
                                <span class="badge success" style="background:#d1fae5;color:#065f46;">
                                    <i class="fas fa-check-circle"></i> {{ __('messages.active') }}
                                </span>
                            @else
                                <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                                    <i class="fas fa-pause-circle"></i> {{ __('messages.inactive') }}
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
            <p style="font-size:18px;font-weight:500;">{{ __('messages.no_users_assigned') }}</p>
            <p style="font-size:14px;margin-top:8px;">{{ __('messages.no_users_assigned_description') }}</p>
        </div>
    @endif
</div>

<!-- Danger Zone -->
@if(!in_array($role->name, ['user', 'admin', 'super_admin']))
<div class="table-card" style="margin-top:20px;border:2px solid #fecaca;">
    <h3 style="margin:0 0 10px 0;color:#991b1b;">
        <i class="fas fa-exclamation-triangle"></i> {{ __('messages.danger_zone') }}
    </h3>
    <p style="color:#6b7280;margin-bottom:15px;">{{ __('messages.delete_role_permanent') }}</p>

    @if($role->users->count() > 0)
    <div class="alert-auto-hide" style="background:#fef3c7;color:#92400e;">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('messages.cannot_delete_role_users', ['count' => $role->users->count()]) }}
    </div>
    @else
    <form action="{{ route('super-admin.roles.destroy', $role) }}" method="POST"
          onsubmit="return confirm('{{ __('messages.delete_role_absolute_confirm') }}');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn" style="background:#dc2626;color:white;">
            <i class="fas fa-trash"></i> {{ __('messages.delete_role') }}
        </button>
    </form>
    @endif
</div>
@endif

@endsection
