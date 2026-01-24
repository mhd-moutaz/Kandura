@extends('layouts.admin')

@section('title', 'Edit Admin')

@push('styles')
<link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>Edit Admin: {{ $admin->name }}</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="table-card">
    <form action="{{ route('super-admin.admins.update', $admin) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-grid">
            <div class="form-field">
                <label>Name *</label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Phone *</label>
                <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" {{ $admin->is_active ? 'checked' : '' }}>
                    <span>Is Active</span>
                </label>
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>New Password (leave empty to keep current)</label>
                <input type="password" name="password">
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label>Confirm New Password</label>
                <input type="password" name="password_confirmation">
            </div>
        </div>

        <hr class="section-divider">

        <h3 class="section-header">Manage Permissions</h3>
        <p class="section-description">Update the permissions for this admin</p>

        <div class="permissions-grid">
            @foreach($permissions as $group => $groupPermissions)
                <div class="permission-group">
                    <h4>
                        <i class="fas fa-shield-alt" style="color:#3b82f6;margin-right:5px;"></i>
                        {{ $group }} Permissions
                        <span style="color:#6b7280;font-size:12px;font-weight:normal;">({{ $groupPermissions->count() }})</span>
                    </h4>
                    @foreach($groupPermissions as $permission)
                        <div class="permission-item">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->name }}"
                                   id="perm_{{ $permission->id }}"
                                   {{ in_array($permission->name, old('permissions', $adminPermissions)) ? 'checked' : '' }}>
                            <label for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Admin
            </button>
            <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#6b7280;color:white;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection
