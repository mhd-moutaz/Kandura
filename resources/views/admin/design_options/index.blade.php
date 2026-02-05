@extends('layouts.admin')
@section('title', __('messages.design_options_management'))

@push('styles')
<link href="{{ asset('css/admin/design-options.css') }}" rel="stylesheet">
@endpush

@section('content')

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="fas fa-search"></i>
            <form action="{{ route('designOptions.index') }}" method="GET" style="display: flex; gap: 10px;">
                <input type="text" name="search" placeholder="{{ __('messages.search_options') }}" value="{{ request('search') }}">
                <select name="type" class="filter-select" onchange="this.form.submit()">
                    <option value="">{{ __('messages.all_types') }}</option>
                    <option value="color" {{ request('type') == 'color' ? 'selected' : '' }}>{{ __('messages.color') }}</option>
                    <option value="dome_type" {{ request('type') == 'dome_type' ? 'selected' : '' }}>{{ __('messages.dome_type') }}</option>
                    <option value="fabric_type" {{ request('type') == 'fabric_type' ? 'selected' : '' }}>{{ __('messages.fabric_type') }}
                    </option>
                    <option value="sleeve_type" {{ request('type') == 'sleeve_type' ? 'selected' : '' }}>{{ __('messages.sleeve_type') }}
                    </option>
                </select>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> {{ __('messages.filter') }}
                </button>
                <a href="{{ route('designs.index') }}"
                    style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                    <i class="fas fa-redo"></i>  {{ __('messages.reset') }}
                </a>

            </form>
        </div>
        <div class="filter-options">
            <a href="{{ route('designOptions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> {{ __('messages.add_new_option') }}
            </a>
        </div>
    </div>

    <!-- Success Message -->



    @if (session('success'))
        <div class="alert-auto-hide"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
    @endif

    <!-- Table Card -->
    <div class="table-card">
        <div class="table-header">
            <h3>{{ __('messages.design_options_list') }}</h3>
            <span style="color: #718096; font-size: 14px;">{{ __('messages.total') }}: {{ __('messages.options') }}</span>
        </div>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('messages.name') }}</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.created_at') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($designOptions as $option)
                    <tr>
                        <td>{{ $option->id }}</td>
                        <td>{{ $option->getTranslation('name', app()->getLocale()) ?? 'N/A' }}</td>
                        <td>
                            @php
                                $typeColors = [
                                    'color' => 'blue',
                                    'dome_type' => 'green',
                                    'fabric_type' => 'orange',
                                    'sleeve_type' => 'purple',
                                ];
                                $typeIcons = [
                                    'color' => 'fa-fill-drip',
                                    'dome_type' => 'fa-circle',
                                    'fabric_type' => 'fa-tshirt',
                                    'sleeve_type' => 'fa-ruler',
                                ];
                                $color = $typeColors[$option->type] ?? 'blue';
                                $icon = $typeIcons[$option->type] ?? 'fa-tag';
                            @endphp
                            <span class="badge"
                                style="background: #{{ $color == 'blue' ? 'dbeafe' : ($color == 'green' ? 'd1fae5' : ($color == 'orange' ? 'fed7aa' : 'e9d5ff')) }}; color: #{{ $color == 'blue' ? '1e40af' : ($color == 'green' ? '065f46' : ($color == 'orange' ? '92400e' : '6b21a8')) }};">
                                <i class="fas {{ $icon }}"></i>
                                {{ ucfirst(str_replace('_', ' ', $option->type)) }}
                            </span>
                        </td>
                        <td>{{ $option->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('designOptions.edit', $option->id) }}" class="action-btn edit">
                                    <i class="fas fa-edit"></i> {{ __('messages.edit') }}
                                </a>
                                <form action="{{ route('designOptions.destroy', $option->id) }}" method="POST"
                                    style="display: inline;"
                                    onsubmit="return confirm('{{ __('messages.delete_confirm_option') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete">
                                        <i class="fas fa-trash"></i> {{ __('messages.delete') }}
                                    </button>
                                </form>

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px; color: #9ca3af;">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 10px; display: block;"></i>
                            {{ __('messages.no_design_options_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        {{-- @if ($designOptions->hasPages())
            <div class="pagination">
                {{ $designOptions->links() }}
            </div>
        @endif --}}
    </div>

@endsection
