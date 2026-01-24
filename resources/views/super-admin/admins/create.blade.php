@extends('layouts.admin')

@section('title', 'Create Admin')

@push('styles')
<link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>Create New Admin</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="table-card">
    <form action="{{ route('super-admin.admins.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-field">
                <label>Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" required>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
                @error('email')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Phone *</label>
                <input type="text" name="phone" value="{{ old('phone') }}" required>
                @error('phone')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="form-grid">
            <div class="form-field">
                <label>Password *</label>
                <input type="password" name="password" required>
                @error('password')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-field">
                <label>Confirm Password *</label>
                <input type="password" name="password_confirmation" required>
            </div>
        </div>

        <hr class="section-divider">

        <h3 class="section-header">Assign Permissions</h3>
        <p class="section-description">Select the permissions you want to give to this admin</p>

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
                                   {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                            <label for="perm_{{ $permission->id }}">{{ $permission->name }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Create Admin
            </button>
            <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#6b7280;color:white;">
                <i class="fas fa-times"></i> Cancel
            </a>
        </div>
    </form>
</div>

@endsection
