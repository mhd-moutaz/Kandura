// Coupons Page JavaScript

// Update placeholder based on discount type
document.getElementById('discount_type')?.addEventListener('change', function(e) {
    const valueInput = document.querySelector('input[name="discount_value"]');
    if (e.target.value === 'percentage') {
        valueInput.placeholder = 'e.g., 10 (for 10% off)';
    } else {
        valueInput.placeholder = 'e.g., 20.00 (for $20 off)';
    }
});
