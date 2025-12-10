@extends('layouts.admin')
@section('title', 'Create Design Option')
@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2> Create New Design Option</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('designOptions.index') }}" class="btn" style="background: #f3f4f6; color: #4b5563;">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="table-card">
    <form action="{{ route('designOptions.store') }}" method="POST">
        @csrf

        <div style="display: grid; gap: 20px;">

            <!-- Option Type -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    Option Type <span style="color: #ef4444;">*</span>
                </label>
                <select name="type" id="type" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" onchange="updateTypeIcon()">
                    <option value="">Select Type</option>
                    <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>Color</option>
                    <option value="dome_type" {{ old('type') == 'dome_type' ? 'selected' : '' }}>Dome Type</option>
                    <option value="fabric_type" {{ old('type') == 'fabric_type' ? 'selected' : '' }}>Fabric Type</option>
                    <option value="sleeve_type" {{ old('type') == 'sleeve_type' ? 'selected' : '' }}>Sleeve Type</option>
                </select>
                @error('type')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name (English) -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    Name (English) <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="name[en]" value="{{ old('name.en') }}" required placeholder="Enter name in English" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                @error('name.en')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name (Arabic) -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    Name (Arabic) <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="name[ar]" value="{{ old('name.ar') }}" required placeholder="أدخل الاسم بالعربية" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                @error('name.ar')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            

            <!-- Form Actions -->
            <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> Create Option
                </button>
                <a href="{{ route('designOptions.index') }}" class="btn" style="background: #f3f4f6; color: #4b5563; flex: 1; text-align: center;">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>

        </div>
    </form>
</div>

<script>
function updateTypeIcon() {
    const type = document.getElementById('type').value;
    const colorSection = document.getElementById('colorPickerSection');

    if (type === 'color') {
        colorSection.style.display = 'block';
    } else {
        colorSection.style.display = 'none';
    }
}

// Update hex value when color changes
document.getElementById('colorValue')?.addEventListener('input', function(e) {
    document.getElementById('colorHex').value = e.target.value.toUpperCase();
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTypeIcon();
});
</script>

@endsection
