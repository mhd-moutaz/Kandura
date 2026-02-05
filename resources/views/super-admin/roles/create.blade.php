@extends('layouts.admin')

@section('title', __('messages.create_role'))

@push('styles')
<link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>{{ __('messages.create_new_role') }}</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('super-admin.roles.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
        </a>
    </div>
</div>

<div class="table-card">
    @if($errors->any())
    <div class="alert-auto-hide" style="background:#fee2e2;color:#991b1b;margin-bottom:20px;">
        <ul style="margin:0;padding-left:20px;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    @if(session('error'))
    <div class="alert-auto-hide" style="background:#fee2e2;color:#991b1b;margin-bottom:20px;">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
    </div>
    @endif

    <form action="{{ route('super-admin.roles.store') }}" method="POST">
        @csrf

        <div class="form-grid">
            <div class="form-field">
                <label>{{ __('messages.role_name') }} *</label>
                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       placeholder="{{ __('messages.role_name_placeholder') }}"
                       required>
                <small style="color:#6b7280;font-size:13px;margin-top:5px;display:block;">{{ __('messages.use_lowercase_underscores') }}</small>
                @error('name')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <hr class="section-divider">

        <h3 class="section-header">{{ __('messages.assign_permissions') }}</h3>
        <p class="section-description">{{ __('messages.select_permissions_description') }}</p>

        <div style="margin-bottom:15px;">
            <label style="display:flex;align-items:center;gap:8px;cursor:pointer;background:#f9fafb;padding:10px 15px;border-radius:6px;width:fit-content;">
                <input type="checkbox" id="select-all-permissions" style="cursor:pointer;">
                <strong>{{ __('messages.select_all_permissions') }}</strong>
            </label>
        </div>

        <div class="permissions-wrapper">
            <div class="permissions-group-grid">
                @foreach($permissions as $resource => $resourcePermissions)
                    <div class="perm-group-title">
                        <i class="fas fa-shield-alt" style="color:#3b82f6;"></i>
                        {{ ucwords($resource) }} {{ __('messages.permissions') }}
                        <span style="color:#6b7280;font-size:12px;font-weight:normal;margin-left:5px;">
                            ({{ count($resourcePermissions) }})
                        </span>
                    </div>
                    @foreach($resourcePermissions as $permission)
                        <label for="perm_{{ $permission->id }}" class="permission-tag {{ in_array($permission->name, old('permissions', [])) ? 'active' : '' }}" style="cursor:pointer;">
                            <input type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->name }}"
                                   class="permission-checkbox"
                                   id="perm_{{ $permission->id }}"
                                   style="display:none;"
                                   {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }}>
                            <i class="fas {{ in_array($permission->name, old('permissions', [])) ? 'fa-check-circle' : 'fa-circle' }}"></i>
                            {{ $permission->name }}
                        </label>
                    @endforeach
                @endforeach
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> {{ __('messages.create_role') }}
            </button>
            <a href="{{ route('super-admin.roles.index') }}" class="btn" style="background:#f3f4f6;color:#4b5563;">
                <i class="fas fa-times"></i> {{ __('messages.cancel') }}
            </a>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
// Toggle permission tag active state
function updatePermissionTagState(checkbox) {
    const label = checkbox.closest('.permission-tag');
    const icon = label.querySelector('i.fas');
    if (checkbox.checked) {
        label.classList.add('active');
        icon.classList.remove('fa-circle');
        icon.classList.add('fa-check-circle');
    } else {
        label.classList.remove('active');
        icon.classList.remove('fa-check-circle');
        icon.classList.add('fa-circle');
    }
}

// Select All Permissions
document.getElementById('select-all-permissions').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
        updatePermissionTagState(checkbox);
    });
});

// Update Select All if individual checkboxes change
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updatePermissionTagState(this);
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
        const selectAllCheckbox = document.getElementById('select-all-permissions');

        selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
    });
});
</script>
@endpush
