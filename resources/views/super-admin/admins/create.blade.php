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

        <h3 class="section-header">Assign Role</h3>
        <p class="section-description">Select one or more roles to assign to this admin</p>

        <div class="roles-grid-container" style="margin-bottom:30px;">
            @foreach($roles as $role)
                <label class="role-card" onclick="toggleRole(this)">
                    <input type="checkbox"
                           name="roles[]"
                           value="{{ $role->name }}"
                           data-role-name="{{ $role->name }}"
                           data-permissions='{{ json_encode($role->permissions->pluck('name')->toArray()) }}'
                           {{ in_array($role->name, old('roles', [])) ? 'checked' : '' }}>
                    <div class="role-info">
                        <span class="role-title">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                        <span class="role-count">
                            <i class="fas fa-key"></i> {{ $role->permissions->count() }} Permissions
                        </span>
                    </div>
                    <div class="check-icon">
                        <i class="fas fa-check"></i>
                    </div>
                </label>
            @endforeach
        </div>
        @error('roles')
            <span class="error-message" style="display:block;margin-top:-20px;margin-bottom:20px;">{{ $message }}</span>
        @enderror

        <h3 class="section-header">Role Permissions</h3>
        <p class="section-description">Permissions from selected roles (display only)</p>

        <div class="permissions-wrapper">
            <div class="permissions-group-grid">
                @foreach($permissions as $group => $groupPermissions)
                    <div class="perm-group-title">
                        <i class="fas fa-shield-alt" style="color:#3b82f6;"></i>
                        {{ $group }} Permissions
                        <span style="color:#6b7280;font-size:12px;font-weight:normal;margin-left:5px;">({{ $groupPermissions->count() }})</span>
                    </div>
                    @foreach($groupPermissions as $permission)
                        <div class="permission-tag" data-perm-name="{{ $permission->name }}">
                            <i class="fas fa-circle"></i> {{ $permission->name }}
                        </div>
                    @endforeach
                @endforeach

            </div>
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

@push('scripts')
<script>
function toggleRole(cardElement) {
    const checkbox = cardElement.querySelector('input[type="checkbox"]');

    setTimeout(() => {
        // Check if admin role is being selected
        const isAdminRole = checkbox.dataset.roleName === 'admin';

        if(checkbox.checked && isAdminRole) {
            // Uncheck all other roles
            const allCheckboxes = document.querySelectorAll('.role-card input[type="checkbox"]');
            allCheckboxes.forEach(cb => {
                if (cb !== checkbox) {
                    cb.checked = false;
                    cb.closest('.role-card').classList.remove('selected');
                }
            });
            cardElement.classList.add('selected');
        } else if(checkbox.checked) {
            // If another role is selected, uncheck admin
            const adminCheckbox = document.querySelector('.role-card input[data-role-name="admin"]');
            if (adminCheckbox && adminCheckbox.checked) {
                adminCheckbox.checked = false;
                adminCheckbox.closest('.role-card').classList.remove('selected');
            }
            cardElement.classList.add('selected');
        } else {
            cardElement.classList.remove('selected');
        }

        updatePermissions();
    }, 10);
}

function updatePermissions() {
    // 1. Find all checked roles
    const checkedRoles = document.querySelectorAll('.role-card input[type="checkbox"]:checked');

    // 2. Collect all permissions from checked roles
    let activePermissions = new Set();

    checkedRoles.forEach(role => {
        const perms = JSON.parse(role.dataset.permissions || '[]');
        perms.forEach(p => activePermissions.add(p));
    });

    // 3. Update UI for permissions list
    const permissionTags = document.querySelectorAll('.permission-tag');

    permissionTags.forEach(tag => {
        const permName = tag.getAttribute('data-perm-name');

        if (activePermissions.has(permName)) {
            tag.classList.add('active');
            tag.querySelector('i').className = 'fas fa-check-circle';
        } else {
            tag.classList.remove('active');
            tag.querySelector('i').className = 'fas fa-circle';
        }
    });
}

// Initialize state on page load
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('.role-card input[type="checkbox"]');

    // Sync UI with any pre-checked values (if editing)
    checkboxes.forEach(box => {
        const card = box.closest('.role-card');
        if(box.checked) card.classList.add('selected');
    });

    updatePermissions();
});
</script>
@endpush

@endsection

