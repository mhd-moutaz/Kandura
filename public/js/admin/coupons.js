// Coupons Page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const discountType = document.getElementById('discount_type');
    const discountValue = document.getElementById('discount_value');
    const discountHint = document.getElementById('discount_hint');

    if (discountType && discountValue) {
        // Update discount constraints based on type
        function updateDiscountConstraints() {
            const valueInput = document.querySelector('input[name="discount_value"]');

            if (discountType.value === 'percentage') {
                discountValue.max = '100';
                valueInput.placeholder = 'e.g., 10 (for 10% off)';
                if (discountHint) {
                    discountHint.innerHTML = '<i class="fas fa-info-circle"></i> Maximum value is 100%';
                }
            } else {
                discountValue.removeAttribute('max');
                valueInput.placeholder = 'e.g., 20.00 (for $20 off)';
                if (discountHint) {
                    discountHint.innerHTML = '<i class="fas fa-info-circle"></i> Enter discount amount in dollars';
                }
            }
        }

        discountType.addEventListener('change', updateDiscountConstraints);
        updateDiscountConstraints(); // Initialize on page load
    }
});
