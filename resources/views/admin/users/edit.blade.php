{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.admin')

@section('title', __('messages.edit_user') . ' - ' . $user->name)

@section('content')
    <div class="edit-user-container" style="max-width: 600px; margin: 0 auto;">

        <!-- Header -->
        <div class="page-header" style="margin-bottom: 5px;">
            <h1 style="font-size: 24px; font-weight: 600; color: #1f2937; margin-bottom: 0px;">
                {{ __('messages.edit_user') }}
            </h1>
        </div>

        @if (session('success'))
            <div class="alert alert-success"
                style="background: #d1fae5; color: #065f46; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #a7f3d0;">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger"
                style="background: #fee2e2; color: #991b1b; padding: 12px; border-radius: 6px; margin-bottom: 20px; border: 1px solid #fecaca;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- User Information Card -->
        <div class="user-info-card"
            style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px; margin-bottom: 10px;">

            <!-- Profile Image Section -->
            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #e5e7eb;">
                @if ($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}"
                        alt="{{ $user->name }}"
                        style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #3b82f6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                @else
                    <div
                        style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); display: flex; align-items: center; justify-content: center; color: #6b7280; font-weight: 600; font-size: 28px; border: 3px solid #d1d5db; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif

                <div>
                    <h2 style="font-size: 20px; font-weight: 600; color: #1f2937; margin: 0 0 4px 0;">
                        {{ $user->name }}
                    </h2>
                    <p style="font-size: 14px; color: #6b7280; margin: 0;">
                        {{ $user->email }}
                    </p>
                </div>
            </div>

            <h3
                style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">
                {{ __('messages.user_information') }}
            </h3>
            <div class="user-details-grid"
                style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px;">
                <!-- User ID -->
                <div class="detail-item">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                        {{ __('messages.user_id') }}
                    </label>
                    <p style="font-size: 16px; color: #1f2937; font-weight: 500; margin: 0;">
                        #USR-{{ $user->id }}
                    </p>
                </div>

                <!-- Name -->
                <div class="detail-item">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                        {{ __('messages.full_name') }}
                    </label>
                    <p style="font-size: 16px; color: #1f2937; font-weight: 500; margin: 0;">
                        {{ $user->name }}
                    </p>
                </div>

                <!-- Email -->
                <div class="detail-item">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                        {{ __('messages.email_address') }}
                    </label>
                    <p style="font-size: 16px; color: #1f2937; font-weight: 500; margin: 0;">
                        {{ $user->email }}
                    </p>
                </div>

                <!-- Phone -->
                <div class="detail-item">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                        {{ __('messages.phone_number') }}
                    </label>
                    <p style="font-size: 16px; color: #1f2937; font-weight: 500; margin: 0;">
                        {{ $user->phone ?? __('messages.n_a') }}
                    </p>
                </div>

                <!-- Registration Date -->
                <div class="detail-item">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                        {{ __('messages.registration_date') }}
                    </label>
                    <p style="font-size: 16px; color: #1f2937; font-weight: 500; margin: 0;">
                        {{ $user->created_at->format('M d, Y') }}
                    </p>
                </div>

                <!-- Last Updated -->
                <div class="detail-item">
                    <label style="display: block; font-size: 14px; font-weight: 500; color: #6b7280; margin-bottom: 4px;">
                        {{ __('messages.last_updated') }}
                    </label>
                    <p style="font-size: 16px; color: #1f2937; font-weight: 500; margin: 0;">
                        {{ $user->updated_at->format('M d, Y') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Status Update Form -->
        <div class="status-form-card"
            style="background: white; border: 1px solid #e2e8f0; border-radius: 8px; padding: 24px;">
            <h3
                style="font-size: 18px; font-weight: 600; color: #1f2937; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb;">
                {{ __('messages.account_status') }}
            </h3>

            <form action="{{ route('users.update', $user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group" style="margin-bottom: 24px;">
                    <label for="is_active"
                        style="display: block; font-size: 14px; font-weight: 500; color: #374151; margin-bottom: 8px;">
                        {{ __('messages.account_status') }}
                    </label>

                    <div class="status-options" style="display: flex; gap: 16px; flex-wrap: wrap;">
                        <label class="status-option"
                            style="display: flex; align-items: center; cursor: pointer; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.2s; background: white; min-width: 120px;">
                            <input type="radio" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                                style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500; color: #1f2937;">{{ __('messages.active') }}</div>
                                <div style="font-size: 12px; color: #6b7280;">{{ __('messages.user_can_login') }}</div>
                            </div>
                        </label>

                        <label class="status-option"
                            style="display: flex; align-items: center; cursor: pointer; padding: 12px 16px; border: 2px solid #e5e7eb; border-radius: 8px; transition: all 0.2s; background: white; min-width: 120px;">
                            <input type="radio" name="is_active" value="0" {{ !$user->is_active ? 'checked' : '' }}
                                style="margin-right: 8px;">
                            <div>
                                <div style="font-weight: 500; color: #1f2937;">{{ __('messages.inactive') }}</div>
                                <div style="font-size: 12px; color: #6b7280;">{{ __('messages.user_cannot_login') }}</div>
                            </div>
                        </label>
                    </div>

                    @error('is_active')
                        <p style="color: #dc2626; font-size: 14px; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Current Status Display -->
                <div class="current-status"
                    style="background: #f8fafc; padding: 12px 16px; border-radius: 6px; margin-bottom: 20px;">
                    <div style="display: flex; align-items: center; justify-content: between;">
                        <span style="font-size: 14px; color: #6b7280;">{{ __('messages.current_status') }}:</span>
                        <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}"
                            style="margin-left: 8px; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 500;
                                 {{ $user->is_active ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' }}">
                            {{ $user->is_active ? __('messages.active') : __('messages.inactive') }}
                        </span>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions"
                    style="display: flex; gap: 12px; align-items: center; justify-content: space-between; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <div>
                        <a href="{{ route('users.index') }}" class="back-btn"
                            style="display: inline-flex; align-items: center; padding: 8px 16px; background: #f3f4f6; color: #374151; text-decoration: none; border-radius: 6px; font-weight: 500; transition: background 0.2s;">
                            ← {{ __('messages.back_to_users') }}
                        </a>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="reset" class="reset-btn"
                            style="padding: 10px 20px; background: #f3f4f6; color: #374151; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                            {{ __('messages.reset') }}
                        </button>
                        <button type="submit" class="submit-btn"
                            style="padding: 10px 20px; background: #3b82f6; color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                            {{ __('messages.update_status') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>


    </div>
@endsection
