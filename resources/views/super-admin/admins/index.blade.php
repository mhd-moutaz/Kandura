@extends('layouts.admin')

@section('title', 'Admins Management')

@push('styles')
<link href="{{ asset('css/admin/admin-management.css') }}" rel="stylesheet">
@endpush

@section('content')

<!-- Search & Filter Section -->

<div class="table-card">
    <div class="search-filter-card">
        <form method="GET" action="{{ route('super-admin.admins.index') }}">
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:15px;">

                <!-- Search -->
                <div>
                    <label>Search</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by name, email...">
                </div>

                <!-- Status Filter -->
                <div>
                    <label>Status</label>
                    <select name="is_active">
                        <option value="">All Status</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Sort Direction -->
                <div>
                    <label>Sort Direction</label>
                    <select name="sort_dir">
                        <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Newest First</option>
                        <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                </div>

            </div>

            <div style="margin-top:15px;display:flex;gap:10px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="{{ route('super-admin.admins.index') }}"
                    style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                    <i class="fas fa-redo"></i> Reset
                </a>
                <a href="{{ route('super-admin.admins.create') }}" class="btn btn-primary" style="margin-left:auto;">
                    <i class="fas fa-plus"></i> Create New Admin
                </a>
            </div>
        </form>
    </div>
    <div class="table-header" style="margin-bottom:20px;">
        <h3>Admins List ({{ $admins->total() }} admins)</h3>
    </div>

    @if (session('success'))
        <div class="alert-auto-hide">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    <div class="admins-grid">
        @forelse ($admins as $admin)
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-avatar">
                        {{ strtoupper(substr($admin->name, 0, 1)) }}
                    </div>
                    <div class="admin-info">
                        <div class="admin-name">{{ $admin->name }}</div>
                        <div class="admin-email">{{ $admin->email }}</div>
                    </div>
                </div>

                <div class="admin-card-body">
                    <div class="admin-detail">
                        <i class="fas fa-phone"></i>
                        <span class="label">Phone:</span>
                        <span class="value">{{ $admin->phone }}</span>
                    </div>
                    <div class="admin-detail">
                        <i class="fas fa-calendar"></i>
                        <span class="label">Created:</span>
                        <span class="value">{{ $admin->created_at->format('Y-m-d') }}</span>
                    </div>

                    <div class="admin-badges">
                        <span class="badge" style="background:#dbeafe;color:#1e40af;">
                            <i class="fas fa-key"></i> {{ $admin->permissions->count() }} permissions
                        </span>
                        @if($admin->is_active)
                            <span class="badge success" style="background:#d1fae5;color:#065f46;">
                                <i class="fas fa-check-circle"></i> Active
                            </span>
                        @else
                            <span class="badge" style="background:#f3f4f6;color:#6b7280;">
                                <i class="fas fa-pause-circle"></i> Inactive
                            </span>
                        @endif
                    </div>
                </div>

                <div class="admin-card-footer">
                    <a href="{{ route('super-admin.admins.show', $admin) }}" class="action-btn" style="background:#eff6ff;color:#1e40af;">
                        <i class="fas fa-eye"></i> View
                    </a>
                    <a href="{{ route('super-admin.admins.edit', $admin) }}" class="action-btn edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <form action="{{ route('super-admin.admins.destroy', $admin) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this admin?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:60px;color:#9ca3af;">
                <i class="fas fa-user-shield" style="font-size:64px;margin-bottom:20px;display:block;opacity:0.5;"></i>
                <p style="font-size:18px;font-weight:500;">No admins found</p>
                <p style="font-size:14px;margin-top:8px;">Create your first admin to get started</p>
            </div>
        @endforelse
    </div>

    @if ($admins->hasPages())
        <div class="pagination" style="margin-top:30px;">
            {{ $admins->links() }}
        </div>
    @endif
</div>

@endsection
