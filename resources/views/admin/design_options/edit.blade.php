@extends('layouts.admin')
@section('title', __('messages.edit_design_option'))

@push('styles')
<link href="{{ asset('css/admin/design-options.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Header -->
<div class="header">
    <div class="header-left">
        <h2>{{ __('messages.edit_design_option') }}</h2>
    </div>
    <div class="header-right">
        <a href="{{ route('designOptions.index') }}" class="btn" style="background: #f3f4f6; color: #4b5563;">
            <i class="fas fa-arrow-left"></i> {{ __('messages.back_to_list') }}
        </a>
    </div>
</div>

<!-- Form Card -->
<div class="table-card">
    <form action="{{ route('designOptions.update', $designOption->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; gap: 20px;">

            <!-- Current Values Display -->
            <div style="background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px;">
                <h4 style="margin: 0 0 12px 0; color: #374151; font-size: 14px; font-weight: 600;">
                    <i class="fas fa-info-circle" style="color: #6b7280;"></i> {{ __('messages.current_values') }}
                </h4>
                <div style="display: grid; gap: 8px;">
                    <div style="display: flex; gap: 8px;">
                        <span style="color: #6b7280; min-width: 120px;">{{ __('messages.option_type') }}:</span>
                        <span style="color: #111827; font-weight: 500;">{{ ucfirst(str_replace('_', ' ', $designOption->type)) }}</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="color: #6b7280; min-width: 120px;">{{ __('messages.name_english') }}:</span>
                        <span style="color: #111827; font-weight: 500;">{{ $designOption->getTranslation('name', 'en') ?? 'N/A' }}</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <span style="color: #6b7280; min-width: 120px;">{{ __('messages.name_arabic') }}:</span>
                        <span style="color: #111827; font-weight: 500;">{{ $designOption->getTranslation('name', 'ar') ?? 'N/A' }}</span>
                    </div>
                    @if($designOption->type === 'color' && $designOption->hex_color)
                    <div style="display: flex; gap: 8px; align-items: center;">
                        <span style="color: #6b7280; min-width: 120px;">{{ __('messages.hex_color') }}:</span>
                        <div style="display: flex; gap: 8px; align-items: center;">
                            <div style="width: 24px; height: 24px; border-radius: 4px; border: 1px solid #d1d5db; background: {{ $designOption->hex_color }};"></div>
                            <span style="color: #111827; font-weight: 500; font-family: monospace;">{{ $designOption->hex_color }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Option Type -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    {{ __('messages.option_type') }} <span style="color: #ef4444;">*</span>
                </label>
                <select name="type" id="type" required style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;" onchange="updateTypeIcon()">
                    <option value="">{{ __('messages.select_type') }}</option>
                    <option value="color" {{ old('type', $designOption->type) == 'color' ? 'selected' : '' }}>{{ __('messages.color') }}</option>
                    <option value="dome_type" {{ old('type', $designOption->type) == 'dome_type' ? 'selected' : '' }}>{{ __('messages.dome_type') }}</option>
                    <option value="fabric_type" {{ old('type', $designOption->type) == 'fabric_type' ? 'selected' : '' }}>{{ __('messages.fabric_type') }}</option>
                    <option value="sleeve_type" {{ old('type', $designOption->type) == 'sleeve_type' ? 'selected' : '' }}>{{ __('messages.sleeve_type') }}</option>
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
                <input type="text" name="name[en]" value="{{ old('name.en', $designOption->name['en'] ?? '') }}" required placeholder="{{ __('messages.enter_name_english') }}" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                @error('name.en')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Name (Arabic) -->
            <div>
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    {{ __('messages.name_arabic') }} <span style="color: #ef4444;">*</span>
                </label>
                <input type="text" name="name[ar]" value="{{ old('name.ar', $designOption->name['ar'] ?? '') }}" required placeholder="{{ __('messages.enter_name_arabic') }}"  style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                @error('name.ar')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Color Picker Section (shown only for color type) -->
            <div id="colorPickerSection" style="display: {{ old('type', $designOption->type) == 'color' ? 'block' : 'none' }};">
                <label style="display: block; margin-bottom: 8px; font-weight: 500; color: #374151;">
                    {{ __('messages.hex_color') }} <span style="color: #ef4444;">*</span>
                </label>
                <div style="display: flex; gap: 12px; align-items: start;">
                    <div style="flex: 0 0 60px;">
                        <input type="color" id="colorPicker" value="{{ old('hex_color', $designOption->hex_color ?? '#000000') }}"
                               style="width: 60px; height: 60px; border: 2px solid #d1d5db; border-radius: 8px; cursor: pointer;">
                    </div>
                    <div style="flex: 1;">
                        <input type="text" name="hex_color" id="hexColorInput"
                               value="{{ old('hex_color', $designOption->hex_color) }}"
                               placeholder="#000000"
                               pattern="^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$"
                               style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: monospace;">
                        <div style="margin-top: 6px; font-size: 12px; color: #6b7280;">
                            <i class="fas fa-info-circle"></i> {{ __('messages.hex_color_format_hint') }}
                        </div>
                    </div>
                    <div style="flex: 0 0 80px;">
                        <div id="colorPreview" style="width: 80px; height: 60px; border: 2px solid #d1d5db; border-radius: 8px; background: {{ old('hex_color', $designOption->hex_color ?? '#000000') }};"></div>
                    </div>
                </div>
                @error('hex_color')
                    <span style="color: #ef4444; font-size: 12px; margin-top: 4px; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Information Box -->
            <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 14px; display: flex; gap: 12px; align-items: start;">
                <i class="fas fa-info-circle" style="color: #3b82f6; margin-top: 2px;"></i>
                <div style="color: #1e40af; font-size: 13px; line-height: 1.6;">
                    <strong>{{ __('messages.note') }}:</strong> {{ __('messages.changes_applied_immediately') }}
                </div>
            </div>

            <!-- Form Actions -->
            <div style="display: flex; gap: 12px; padding-top: 20px; border-top: 1px solid #e5e7eb;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="fas fa-save"></i> {{ __('messages.update_option') }}
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
