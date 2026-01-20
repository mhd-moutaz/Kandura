// Design Options Page JavaScript

function updateTypeIcon() {
    const type = document.getElementById('type').value;
    const colorSection = document.getElementById('colorPickerSection');

    if (type === 'color') {
        colorSection.style.display = 'block';
    } else {
        colorSection.style.display = 'none';
    }
}

// Update hex value when color changes
document.getElementById('colorValue')?.addEventListener('input', function(e) {
    document.getElementById('colorHex').value = e.target.value.toUpperCase();
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTypeIcon();
});
