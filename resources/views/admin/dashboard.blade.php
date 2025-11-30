{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')



<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Users</h3>
            <div class="number">3,456</div>
        </div>
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Address</h3>
            <div class="number">4,598</div>
        </div>
        <div class="stat-icon purple">
            <i class="fas fa-map-marker-alt"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Today's Orders</h3>
            <div class="number">128</div>
        </div>
        <div class="stat-icon green">
            <i class="fas fa-shopping-bag"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h3>Total Products</h3>
            <div class="number">1,245</div>
        </div>
        <div class="stat-icon orange">
            <i class="fas fa-box-open"></i>
        </div>
    </div>

</div>

{{-- <!-- Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3>Monthly Sales</h3>
        <canvas id="salesChart" style="max-height: 300px;"></canvas>
    </div>

    <div class="chart-card">
        <h3>Top Products</h3>
        <canvas id="productsChart" style="max-height: 300px;"></canvas>
    </div>
</div> --}}

<!-- Recent Orders Table -->
<div class="table-card">
    <div class="table-header">
        <h3>Recent Orders</h3>
        <button class="btn btn-primary">
            <i class="fas fa-plus"></i> New Order
        </button>
    </div>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Product</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>#ORD-1234</td>
                <td>Mohammed Ahmed</td>
                <td>Smartphone</td>
                <td>$2,500</td>
                <td><span class="badge success">Completed</span></td>
                <td>2024-01-15</td>
                <td>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn delete">Delete</button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#ORD-1235</td>
                <td>Fatima Ali</td>
                <td>Handbag</td>
                <td>$450</td>
                <td><span class="badge warning">Processing</span></td>
                <td>2024-01-15</td>
                <td>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn delete">Delete</button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#ORD-1236</td>
                <td>Khaled Mahmoud</td>
                <td>Smart Watch</td>
                <td>$1,200</td>
                <td><span class="badge success">Completed</span></td>
                <td>2024-01-14</td>
                <td>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn delete">Delete</button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#ORD-1237</td>
                <td>Nora Saeed</td>
                <td>Programming Book</td>
                <td>$150</td>
                <td><span class="badge danger">Cancelled</span></td>
                <td>2024-01-14</td>
                <td>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn delete">Delete</button>
                    </div>
                </td>
            </tr>
            <tr>
                <td>#ORD-1238</td>
                <td>Omar Hassan</td>
                <td>Laptop</td>
                <td>$4,500</td>
                <td><span class="badge warning">Shipping</span></td>
                <td>2024-01-13</td>
                <td>
                    <div class="actions">
                        <button class="action-btn edit">Edit</button>
                        <button class="action-btn delete">Delete</button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
</div>
@endsection


