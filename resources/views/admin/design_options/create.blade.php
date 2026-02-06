@extends('layouts.admin')
@section('title', __('messages.create_design_option'))

@push('styles')
<link href="{{ asset('css/admin/design-options.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>{{ __('messages.create_new_design_option') }}</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('designOptions.index') }}" class="btn" style="background: #f3f4f6; color: #4b5563;">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
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
                    {{ __('messages.option_type') }} <span style="color: #ef4444;">*</span>
                </label>
                <select name="type" id="type" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" onchange="updateTypeIcon()">
                    <option value="">{{ __('messages.select_type') }}</option>
                    <option value="color" {{ old('type') == 'color' ? 'selected' : '' }}>{{ __('messages.color') }}</option>
                    <option value="dome_type" {{ old('type') == 'dome_type' ? 'selected' : '' }}>{{ __('messages.dome_type') }}</option>
                    <option value="fabric_type" {{ old('type') == 'fabric_type' ? 'selected' : '' }}>{{ __('messages.fabric_type') }}</option>
                    <option value="sleeve_type" {{ old('type') == 'sleeve_type' ? 'selected' : '' }}>{{ __('messages.sleeve_type') }}</option>
                </select>
                @error('type')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name (English) -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    {{ __('messages.name_english') }} <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="name[en]" value="{{ old('name.en') }}" required placeholder="{{ __('messages.enter_name_english') }}" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                @error('name.en')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name (Arabic) -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    {{ __('messages.name_arabic') }} <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="name[ar]" value="{{ old('name.ar') }}" required placeholder="{{ __('messages.enter_name_arabic') }}" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                @error('name.ar')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Color Picker Section (shown only for color type) -->
            <div id="colorPickerSection" style="display: none;">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    {{ __('messages.hex_color') }} <span style="color: #ef4444;">*</span>
                </label>
                <div style="display: flex; gap: 12px; align-items: start;">
                    <div style="flex: 0 0 60px;">
                        <input type="color" id="colorPicker" value="{{ old('hex_color', '#000000') }}"
                               style="width: 60px; height: 60px; border: 2px solid #d1d5db; border-radius: 8px; cursor: pointer;">
                    </div>
                    <div style="flex: 1;">
                        <input type="text" name="hex_color" id="hexColorInput"
                               value="{{ old('hex_color') }}"
                               placeholder="#000000"
                               pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: monospace;">
                        <div style="margin-top: 6px; font-size: 12px; color: #6b7280;">
                            <i class="fas fa-info-circle"></i> {{ __('messages.hex_color_format_hint') }}
                        </div>
                    </div>
                    <div style="flex: 0 0 80px;">
                        <div id="colorPreview" style="width: 80px; height: 60px; border: 2px solid #d1d5db; border-radius: 8px; background: {{ old('hex_color', '#000000') }};"></div>
                    </div>
                </div>
                @error('hex_color')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> {{ __('messages.create_option') }}
                </button>
                <a href="{{ route('designOptions.index') }}" class="btn" style="background: #f3f4f6; color: #4b5563; flex: 1; text-align: center;">
                    <i class="fas fa-times"></i> {{ __('messages.cancel') }}
                </a>
            </div>

        </div>
    </form>
</div>

@endsection

@push('scripts')
<script src="{{ asset('js/admin/design-options.js') }}"></script>
@endpush
