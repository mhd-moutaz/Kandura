// public/js/admin/dashboard.js

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Charts
    initSalesChart();
    initProductsChart();

    // Add event listeners
    initEventListeners();
});

/**
 * Initialize Sales Chart
 */
function initSalesChart() {
    const ctx = document.getElementById('salesChart');
    if (!ctx) return;

    new Chart(ctx.getContext('2d'), {
        type: 'line',
        data: {
            labels: ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو'],
            datasets: [{
                label: 'المبيعات',
                data: [12000, 19000, 15000, 25000, 22000, 30000],
                borderColor: '#5a67d8',
                backgroundColor: 'rgba(90, 103, 216, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#5a67d8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#2d3748',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    borderRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f0f0f0',
                        drawBorder: false
                    },
                    ticks: {
                        color: '#718096',
                        callback: function(value) {
                            return value.toLocaleString() + ' ر.س';
                        }
                    }
                },
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: '#718096'
                    }
                }
            }
        }
    });
}

/**
 * Initialize Products Chart
 */
function initProductsChart() {
    const ctx = document.getElementById('productsChart');
    if (!ctx) return;

    new Chart(ctx.getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['إلكترونيات', 'ملابس', 'كتب', 'أخرى'],
            datasets: [{
                data: [40, 30, 20, 10],
                backgroundColor: [
                    '#5a67d8',
                    '#10b981',
                    '#f59e0b',
                    '#3b82f6'
                ],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        color: '#4a5568',
                        font: {
                            size: 13
                        },
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: '#2d3748',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    padding: 12,
                    borderRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + context.parsed + '%';
                        }
                    }
                }
            }
        }
    });
}

/**
 * Initialize Event Listeners
 */
function initEventListeners() {
    // Edit button click
    document.querySelectorAll('.action-btn.edit').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const row = this.closest('tr');
            const orderId = row.querySelector('td:first-child').textContent;
            editOrder(orderId);
        });
    });

    // Delete button click
    document.querySelectorAll('.action-btn.delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const row = this.closest('tr');
            const orderId = row.querySelector('td:first-child').textContent;
            deleteOrder(orderId, row);
        });
    });

    // New order button
    const newOrderBtn = document.querySelector('.btn-primary');
    if (newOrderBtn) {
        newOrderBtn.addEventListener('click', function(e) {
            e.preventDefault();
            createNewOrder();
        });
    }
}

/**
 * Edit Order
 */
function editOrder(orderId) {
    console.log('تعديل الطلب:', orderId);
    // يمكنك إضافة كود لفتح نافذة منبثقة أو الانتقال لصفحة التعديل
    alert('سيتم فتح صفحة تعديل الطلب: ' + orderId);
}

/**
 * Delete Order
 */
function deleteOrder(orderId, row) {
    if (confirm('هل أنت متأكد من حذف الطلب ' + orderId + '؟')) {
        console.log('حذف الطلب:', orderId);

        // إضافة تأثير الاختفاء
        row.style.transition = 'opacity 0.3s';
        row.style.opacity = '0';

        setTimeout(() => {
            row.remove();
            showNotification('تم حذف الطلب بنجاح', 'success');
        }, 300);

        // هنا يمكنك إضافة طلب AJAX لحذف الطلب من قاعدة البيانات
        // deleteOrderFromDatabase(orderId);
    }
}

/**
 * Create New Order
 */
function createNewOrder() {
    console.log('إنشاء طلب جديد');
    alert('سيتم فتح صفحة إنشاء طلب جديد');
    // يمكنك الانتقال لصفحة إنشاء طلب جديد
    // window.location.href = '/admin/orders/create';
}

/**
 * Show Notification
 */
function showNotification(message, type = 'info') {
    // يمكنك استخدام مكتبة للإشعارات مثل Toastr أو إنشاء إشعار مخصص
    console.log(`[${type}] ${message}`);
}

/**
 * Delete Order from Database (AJAX)
 */
function deleteOrderFromDatabase(orderId) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    fetch(`/admin/orders/${orderId}`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('تم حذف الطلب بنجاح', 'success');
        } else {
            showNotification('حدث خطأ أثناء الحذف', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء الحذف', 'error');
    });
}
