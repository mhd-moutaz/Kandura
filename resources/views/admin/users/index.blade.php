@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
    <!-- Users Table -->
    <div class="table-card">

        @if (session('success'))
            <div class="alert-auto-hide">{{ session('success') }}</div>
            <style>
                .alert-auto-hide {
                    background: #d1fae5;
                    color: #065f46;
                    padding: 12px;
                    border-radius: 6px;
                    margin-bottom: 20px;
                    border: 1px solid #a7f3d0;
                    animation: fade 3s forwards;
                }

                @keyframes fade {

                    0%,
                    60% {
                        opacity: 1;
                    }

                    100% {
                        opacity: 0;
                        display: none;
                    }
                }
            </style>
        @endif

        <!-- Search and Filter Section -->
        <div class="search-filter-card" style="background: #f8fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <form method="GET" action="{{ route('users.index') }}" id="searchFilterForm">
                <div class="search-filter-grid"
                    style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 15px;">

                    <!-- Search Input -->
                    <div class="search-group">
                        <label for="search"
                            style="display: block; margin-bottom: 5px; font-weight: 500; color: #374151;">Search</label>
                        <input type="text" name="search" id="search" value="{{ request('search') }}"
                            placeholder="Search by name or email..."
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-group">
                        <label for="is_active"
                            style="display: block; margin-bottom: 5px; font-weight: 500; color: #374151;">Status</label>
                        <select name="is_active" id="status"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                            <option value="">All Status</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inactive
                            </option>
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div class="sort-group">
                        <label for="sort_dir"
                            style="display: block; margin-bottom: 5px; font-weight: 500; color: #374151;">Sort Dir</label>
                        <select name="sort_dir" id="sort_by"
                            style="width: 100%; padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                            <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>ASC
                            </option>
                            <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>DESC
                            </option>
                        </select>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="margin-top:15px;display:flex;gap:10px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('designs.index') }}"
                        style="background:#6b7280;color:white;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Is Active</th>
                    <th>Phone</th>
                    <th>Registration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            @if ($user->profile_image)
                                <img src="{{ asset('storage/' . $user->profile_image) }}"
                                    style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div
                                    style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%); display: flex; align-items: center; justify-content: center; color: #6b7280; font-weight: 600; font-size: 28px; border: 3px solid #d1d5db; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                        </td>
                        <td>#USR-{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>
                            <span class="status-badge {{ $user->is_active ? 'active' : 'inactive' }}"
                                style=" padding: 4px 8px; border-radius: 12px; font-size: 12px; font-weight: 500;{{ $user->is_active ? 'background: #d1fae5; color: #065f46;' : 'background: #fee2e2; color: #991b1b;' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('users.edit', $user) }}" class="action-btn edit">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn delete"
                                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px; color: #6b7280;">
                            No users found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="pagination">
                {{ $users->links() }}
            </div>
        @endif
    </div>
@endsection
