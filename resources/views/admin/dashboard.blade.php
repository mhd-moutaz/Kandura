{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

<!-- Welcome Message -->
<div class="welcome-section">
    <div class="welcome-card">
        <div class="welcome-content">
            <h1>{{ __('messages.welcome') }}, {{ auth()->user()->name }}!</h1>
            <p>{{ __('messages.dashboard') }} - {{ __('messages.app_name') }}</p>
        </div>
        <div class="welcome-icon">
            <i class="fas fa-hand-wave"></i>
        </div>
    </div>
</div>

<!-- Navigation Cards (Same as Sidebar) -->
<div class="nav-cards-grid">
    <a href="{{ route('home') }}" class="nav-card {{ request()->routeIs('home') ? 'active' : '' }}">
        <div class="nav-card-icon blue">
            <i class="fas fa-home"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.dashboard') }}</h3>
        </div>
    </a>

    @can('view all admin')
    <a href="{{ route('super-admin.admins.index') }}" class="nav-card {{ request()->routeIs('super-admin.admins.*') ? 'active' : '' }}">
        <div class="nav-card-icon purple">
            <i class="fas fa-user-shield"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.admin_management') }}</h3>
        </div>
    </a>
    @endcan

    @can('view all role')
    <a href="{{ route('super-admin.roles.index') }}" class="nav-card {{ request()->routeIs('super-admin.roles.*') ? 'active' : '' }}">
        <div class="nav-card-icon indigo">
            <i class="fas fa-user-tag"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.roles_management') }}</h3>
        </div>
    </a>
    @endcan

    @can('view all user')
    <a href="{{ route('users.index') }}" class="nav-card {{ request()->routeIs('users.*') ? 'active' : '' }}">
        <div class="nav-card-icon teal">
            <i class="fas fa-users"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.users') }}</h3>
        </div>
    </a>
    @endcan

    @can('view all address')
    <a href="{{ route('addresses.index') }}" class="nav-card {{ request()->routeIs('addresses.*') ? 'active' : '' }}">
        <div class="nav-card-icon pink">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.addresses') }}</h3>
        </div>
    </a>
    @endcan

    @can('view design options')
    <a href="{{ route('designOptions.index') }}" class="nav-card {{ request()->routeIs('designOptions.*') ? 'active' : '' }}">
        <div class="nav-card-icon cyan">
            <i class="fas fa-palette"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.design_options') }}</h3>
        </div>
    </a>
    @endcan

    @can('view all designs')
    <a href="{{ route('designs.index') }}" class="nav-card {{ request()->routeIs('designs.*') ? 'active' : '' }}">
        <div class="nav-card-icon orange">
            <i class="fas fa-tshirt"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.designs') }}</h3>
        </div>
    </a>
    @endcan

    @can('view all order')
    <a href="{{ route('orders.index') }}" class="nav-card {{ request()->routeIs('orders.*') ? 'active' : '' }}">
        <div class="nav-card-icon green">
            <i class="fas fa-shopping-cart"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.orders') }}</h3>
        </div>
    </a>
    @endcan

    @can('view all coupon')
    <a href="{{ route('coupons.index') }}" class="nav-card {{ request()->routeIs('coupons.*') ? 'active' : '' }}">
        <div class="nav-card-icon red">
            <i class="fas fa-percent"></i>
        </div>
        <div class="nav-card-info">
            <h3>{{ __('messages.coupons') }}</h3>
        </div>
    </a>
    @endcan
</div>

<!-- Notifications Table -->
<div class="table-card">
    <div class="table-header">
        <h3><i class="fas fa-bell me-2"></i>{{ __('messages.notifications') }}</h3>
        <button type="button" class="btn btn-secondary" id="markAllReadBtn">
            <i class="fas fa-check-double"></i> {{ __('messages.mark_all_read') }}
        </button>
    </div>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>{{ __('messages.status') }}</th>
                    <th>{{ __('messages.type') }}</th>
                    <th>{{ __('messages.message') }}</th>
                    <th>{{ __('messages.date') }}</th>
                    <th>{{ __('messages.actions') }}</th>
                </tr>
            </thead>
            <tbody id="notificationsTableBody">
                <tr>
                    <td colspan="5" class="text-center">
                        <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="pagination-container" id="notificationsPagination"></div>
</div>

@endsection

@push('styles')
<style>
/* Welcome Section */
.welcome-section {
    margin-bottom: 30px;
}

.welcome-card {
    background: linear-gradient(135deg, #5a67d8 0%, #667eea 100%);
    padding: 30px;
    border-radius: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: white;
    box-shadow: 0 10px 40px rgba(90, 103, 216, 0.3);
}

.welcome-content h1 {
    font-size: 28px;
    font-weight: 600;
    margin-bottom: 8px;
}

.welcome-content p {
    font-size: 14px;
    opacity: 0.9;
}

.welcome-icon {
    font-size: 60px;
    opacity: 0.3;
}

/* Navigation Cards Grid */
.nav-cards-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.nav-card {
    background: white;
    padding: 24px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    text-decoration: none;
    color: inherit;
}

.nav-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    text-decoration: none;
    color: inherit;
}

.nav-card.active {
    border-color: #5a67d8;
    background: linear-gradient(135deg, #f7f8ff 0%, #ffffff 100%);
}

.nav-card-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: white;
    flex-shrink: 0;
}

.nav-card-icon.blue { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
.nav-card-icon.purple { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
.nav-card-icon.indigo { background: linear-gradient(135deg, #6366f1, #4338ca); }
.nav-card-icon.teal { background: linear-gradient(135deg, #14b8a6, #0d9488); }
.nav-card-icon.pink { background: linear-gradient(135deg, #ec4899, #db2777); }
.nav-card-icon.cyan { background: linear-gradient(135deg, #06b6d4, #0891b2); }
.nav-card-icon.orange { background: linear-gradient(135deg, #f97316, #ea580c); }
.nav-card-icon.green { background: linear-gradient(135deg, #22c55e, #16a34a); }
.nav-card-icon.red { background: linear-gradient(135deg, #ef4444, #dc2626); }

.nav-card-info h3 {
    font-size: 14px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
}

/* Table Card Improvements */
.table-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    border: 1px solid #f0f0f0;
    overflow: hidden;
}

.table-header {
    padding: 20px 24px;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.table-header h3 {
    font-size: 16px;
    font-weight: 600;
    color: #2d3748;
    margin: 0;
    display: flex;
    align-items: center;
}

.table-responsive {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

table th,
table td {
    padding: 14px 20px;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

table th {
    background: #fafbfc;
    font-weight: 600;
    font-size: 13px;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

table td {
    font-size: 14px;
    color: #4a5568;
}

table tbody tr:hover {
    background: #f7fafc;
}

/* Notification specific styles */
.notification-status {
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
}

.status-dot.unread {
    background: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
}

.status-dot.read {
    background: #d1d5db;
}

.notification-type {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
}

.notification-type.order {
    background: #dbeafe;
    color: #1d4ed8;
}

.notification-type.design {
    background: #fce7f3;
    color: #be185d;
}

.notification-type.default {
    background: #f3f4f6;
    color: #4b5563;
}

.notification-message {
    max-width: 300px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notification-date {
    color: #9ca3af;
    font-size: 13px;
}

.btn-sm {
    padding: 6px 12px;
    font-size: 12px;
    border-radius: 6px;
}

.btn-outline-primary {
    background: transparent;
    border: 1px solid #5a67d8;
    color: #5a67d8;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-outline-primary:hover {
    background: #5a67d8;
    color: white;
}

.pagination-container {
    padding: 16px 24px;
    border-top: 1px solid #f0f0f0;
    display: flex;
    justify-content: center;
    gap: 8px;
}

.pagination-btn {
    padding: 8px 14px;
    border: 1px solid #e2e8f0;
    background: white;
    border-radius: 6px;
    cursor: pointer;
    font-size: 13px;
    transition: all 0.2s;
}

.pagination-btn:hover:not(:disabled) {
    background: #f7fafc;
    border-color: #5a67d8;
    color: #5a67d8;
}

.pagination-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pagination-btn.active {
    background: #5a67d8;
    border-color: #5a67d8;
    color: white;
}

.empty-state {
    text-align: center;
    padding: 40px;
    color: #9ca3af;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 16px;
}

/* RTL Support */
[dir="rtl"] .nav-card {
    flex-direction: row-reverse;
}

[dir="rtl"] .welcome-card {
    flex-direction: row-reverse;
}

[dir="rtl"] table th,
[dir="rtl"] table td {
    text-align: right;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentPage = 1;

    // Load notifications on page load
    loadNotifications(currentPage);

    // Mark all as read button
    document.getElementById('markAllReadBtn').addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        markAllAsRead();
    });

    function loadNotifications(page) {
        const tbody = document.getElementById('notificationsTableBody');

        // Show loading state
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}
                </td>
            </tr>
        `;

        fetch(`/admin/notifications?page=${page}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                renderNotifications(data.data);
                renderPagination(data.data);
            } else {
                throw new Error('Failed to load notifications');
            }
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center empty-state">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>{{ __('messages.error_loading_notifications') }}</p>
                    </td>
                </tr>
            `;
        });
    }

    function renderNotifications(data) {
        const tbody = document.getElementById('notificationsTableBody');

        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center empty-state">
                        <i class="fas fa-bell-slash"></i>
                        <p>{{ __('messages.no_notifications') }}</p>
                    </td>
                </tr>
            `;
            return;
        }

        let html = '';
        data.data.forEach(notification => {
            const isRead = notification.read_at !== null;
            const notificationData = notification.data;
            const type = getNotificationType(notification.type);
            const message = notificationData.message || '{{ __('messages.no_message') }}';
            const date = new Date(notification.created_at).toLocaleString();

            html += `
                <tr class="${isRead ? '' : 'unread-row'}">
                    <td>
                        <span class="notification-status">
                            <span class="status-dot ${isRead ? 'read' : 'unread'}"></span>
                            ${isRead ? '{{ __('messages.read') }}' : '{{ __('messages.unread') }}'}
                        </span>
                    </td>
                    <td>
                        <span class="notification-type ${type.class}">${type.label}</span>
                    </td>
                    <td>
                        <span class="notification-message" title="${message}">${message}</span>
                    </td>
                    <td>
                        <span class="notification-date">${date}</span>
                    </td>
                    <td>
                        ${!isRead ? `
                            <button class="btn btn-sm btn-outline-primary" onclick="markAsRead('${notification.id}')">
                                <i class="fas fa-check"></i> {{ __('messages.mark_read') }}
                            </button>
                        ` : ''}
                    </td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
    }

    function getNotificationType(type) {
        if (type.includes('Order')) {
            return { class: 'order', label: '{{ __('messages.orders') }}' };
        } else if (type.includes('Design')) {
            return { class: 'design', label: '{{ __('messages.designs') }}' };
        }
        return { class: 'default', label: '{{ __('messages.notification') }}' };
    }

    function renderPagination(data) {
        const container = document.getElementById('notificationsPagination');

        if (data.last_page <= 1) {
            container.innerHTML = '';
            return;
        }

        let html = '';

        // Previous button
        html += `<button class="pagination-btn" ${data.current_page === 1 ? 'disabled' : ''} onclick="loadPage(${data.current_page - 1})">
            <i class="fas fa-chevron-left"></i>
        </button>`;

        // Page numbers
        for (let i = 1; i <= data.last_page; i++) {
            if (i === 1 || i === data.last_page || (i >= data.current_page - 2 && i <= data.current_page + 2)) {
                html += `<button class="pagination-btn ${i === data.current_page ? 'active' : ''}" onclick="loadPage(${i})">${i}</button>`;
            } else if (i === data.current_page - 3 || i === data.current_page + 3) {
                html += `<span class="pagination-btn" style="border: none;">...</span>`;
            }
        }

        // Next button
        html += `<button class="pagination-btn" ${data.current_page === data.last_page ? 'disabled' : ''} onclick="loadPage(${data.current_page + 1})">
            <i class="fas fa-chevron-right"></i>
        </button>`;

        container.innerHTML = html;
    }

    // Make functions globally accessible
    window.loadPage = function(page) {
        currentPage = page;
        loadNotifications(page);
    };

    window.markAsRead = function(id) {
        fetch(`/admin/notifications/${id}/read`, {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                loadNotifications(currentPage);
            } else {
                throw new Error('Failed to mark notification as read');
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    };

    function markAllAsRead() {
        const btn = document.getElementById('markAllReadBtn');
        const originalText = btn.innerHTML;

        // Show loading state on button
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('messages.loading') }}';

        fetch('/admin/notifications/read-all', {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                loadNotifications(currentPage);
            } else {
                throw new Error('Failed to mark notifications as read');
            }
        })
        .catch(error => {
            console.error('Error marking all notifications as read:', error);
            alert('{{ __('messages.error') }}');
        })
        .finally(() => {
            // Restore button state
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
});
</script>
@endpush


