@extends('layouts.admin')

@section('title', __('messages.edit_admin'))

@push('styles')
    <link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <h2>{{ __('messages.edit_admin') }}: {{ $admin->name }}</h2>
        </div>
        <div class="header-right">
            <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
                <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
            </a>
        </div>
    </div>

    <div class="table-card">
        <form action="{{ route('super-admin.admins.update', $admin) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label>{{ __('messages.name') }} *</label>
                    <input type="text" name="name" value="{{ old('name', $admin->name) }}" autocomplete="off" required>
                    @error('name')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label>{{ __('messages.email') }} *</label>
                    <input type="email" name="email" value="{{ old('email', $admin->email) }}" autocomplete="off" required>
                    @error('email')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label>{{ __('messages.phone') }} *</label>
                    <input type="text" name="phone" value="{{ old('phone', $admin->phone) }}" autocomplete="off" required>
                    @error('phone')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Modified Section -->
                <div class="form-field">
                    <label>{{ __('messages.status') }}</label>

                    <!-- Wrapper div to control height and center the checkbox vertically -->
                    <div style="display: flex; align-items: center; height: 46px; padding: 0 2px;">
                        <label class="checkbox-label" style="margin: 0; cursor: pointer;">
                            <input type="checkbox" name="is_active" {{ $admin->is_active ? 'checked' : '' }}>
                            <span>{{ __('messages.is_active') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-grid">
                <div class="form-field">
                    <label>{{ __('messages.new_password') }} ({{ __('messages.leave_empty_password') }})</label>
                    <input type="password" name="password" autocomplete="new-password">
                    @error('password')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-field">
                    <label>{{ __('messages.confirm_new_password') }}</label>
                    <input type="password" name="password_confirmation" autocomplete="new-password">
                </div>
            </div>

            <hr class="section-divider">

            <h3 class="section-header">{{ __('messages.manage_roles') }}</h3>
            <p class="section-description">{{ __('messages.update_roles_description') }}</p>

            <div class="roles-grid-container" style="margin-bottom:30px;">
                @foreach ($roles as $role)
                    <label class="role-card" onclick="toggleRole(this)">
                        <input type="checkbox" name="roles[]" value="{{ $role->name }}"
                            data-role-name="{{ $role->name }}"
                            data-permissions='{{ json_encode($role->permissions->pluck('name')->toArray()) }}'
                           {{ in_array($role->name, old('roles', $admin->getRoleNames()->toArray())) ? 'checked' : '' }}>
                        <div class="role-info">
                            <span class="role-title">{{ ucwords(str_replace('_', ' ', $role->name)) }}</span>
                            <span class="role-count">
                                <i class="fas fa-key"></i> {{ $role->permissions->count() }} {{ __('messages.permissions') }}
                            </span>
                        </div>
                        <div class="check-icon">
                            <i class="fas fa-check"></i>
                        </div>
                    </label>
                @endforeach
            </div>
            @error('roles')
                <span class="error-message"
                    style="display:block;margin-top:-20px;margin-bottom:20px;">{{ $message }}</span>
            @enderror

            <h3 class="section-header">{{ __('messages.role_permissions_preview') }}</h3>
            <p class="section-description">{{ __('messages.permissions_inherited') }}</p>

            <div class="permissions-wrapper">
                <div class="permissions-group-grid">
                    @foreach ($permissions as $group => $groupPermissions)
                        <div class="perm-group-title">
                            <i class="fas fa-shield-alt" style="color:#3b82f6;"></i>
                            {{ $group }} {{ __('messages.permissions') }}
                            <span
                                style="color:#6b7280;font-size:12px;font-weight:normal;margin-left:5px;">({{ $groupPermissions->count() }})</span>
                        </div>
                        @foreach ($groupPermissions as $permission)
                            <div class="permission-tag role-inherited" data-perm-name="{{ $permission->name }}">
                                <i class="fas fa-circle"></i> {{ $permission->name }}
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> {{ __('messages.update_admin') }}
                </button>
                <a href="{{ route('super-admin.admins.index') }}" class="btn" style="background:#6b7280;color:white;">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
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

                    if (checkbox.checked && isAdminRole) {
                        // Uncheck all other roles
                        const allCheckboxes = document.querySelectorAll('.role-card input[type="checkbox"]');
                        allCheckboxes.forEach(cb => {
                            if (cb !== checkbox) {
                                cb.checked = false;
                                cb.closest('.role-card').classList.remove('selected');
                            }
                        });
                        cardElement.classList.add('selected');
                    } else if (checkbox.checked) {
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

                    updateRolePermissions();
                }, 10);
            }

            function updateRolePermissions() {
                // Find all checked roles
                const checkedRoles = document.querySelectorAll('.role-card input[type="checkbox"]:checked');

                // Collect all permissions from checked roles
                let activePermissions = new Set();

                checkedRoles.forEach(role => {
                    const perms = JSON.parse(role.dataset.permissions || '[]');
                    perms.forEach(p => activePermissions.add(p));
                });

                // Update UI for role-inherited permissions
                const rolePermissionTags = document.querySelectorAll('.permission-tag.role-inherited');

                rolePermissionTags.forEach(tag => {
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
                // Initialize role cards
                const roleCheckboxes = document.querySelectorAll('.role-card input[type="checkbox"]');
                roleCheckboxes.forEach(box => {
                    const card = box.closest('.role-card');
                    if (box.checked) card.classList.add('selected');
                });

                updateRolePermissions();
            });
        </script>
    @endpush

@endsection
