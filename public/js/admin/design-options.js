// Design Options Page JavaScript

/**
 * Show/hide color picker section based on selected type
 */
function updateTypeIcon() {
    const type = document.getElementById('type').value;
    const colorSection = document.getElementById('colorPickerSection');

    if (type === 'color') {
        colorSection.style.display = 'block';
    } else {
        colorSection.style.display = 'none';
    }
}

/**
 * Sync color picker with hex input
 */
function initColorPicker() {
    const colorPicker = document.getElementById('colorPicker');
    const hexInput = document.getElementById('hexColorInput');
    const colorPreview = document.getElementById('colorPreview');

    if (!colorPicker || !hexInput) return;

    // Update hex input when color picker changes
    colorPicker.addEventListener('input', function(e) {
        const color = e.target.value.toUpperCase();
        hexInput.value = color;
        if (colorPreview) {
            colorPreview.style.background = color;
        }
    });

    // Update color picker when hex input changes (with validation)
    hexInput.addEventListener('input', function(e) {
        let value = e.target.value.trim();

        // Auto-add # if missing
        if (value && !value.startsWith('#')) {
            value = '#' + value;
            e.target.value = value;
        }

        // Validate and update color picker if valid
        const hexRegex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
        if (hexRegex.test(value)) {
            // Expand 3-digit hex to 6-digit
            let fullHex = value;
            if (value.length === 4) {
                fullHex = '#' + value[1] + value[1] + value[2] + value[2] + value[3] + value[3];
            }

            colorPicker.value = fullHex;
            if (colorPreview) {
                colorPreview.style.background = fullHex;
            }

            // Remove error styling
            hexInput.style.borderColor = '#d1d5db';
        } else if (value.length >= 4) {
            // Show error styling for invalid input
            hexInput.style.borderColor = '#ef4444';
        }
    });

    // Format on blur
    hexInput.addEventListener('blur', function(e) {
        let value = e.target.value.trim().toUpperCase();

        if (value && !value.startsWith('#')) {
            value = '#' + value;
        }

        // Expand 3-digit hex to 6-digit
        const hexRegex3 = /^#[A-Fa-f0-9]{3}$/;
        if (hexRegex3.test(value)) {
            value = '#' + value[1] + value[1] + value[2] + value[2] + value[3] + value[3];
        }

        e.target.value = value;

        const hexRegex = /^#[A-Fa-f0-9]{6}$/;
        if (hexRegex.test(value)) {
            colorPicker.value = value;
            if (colorPreview) {
                colorPreview.style.background = value;
            }
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTypeIcon();
    initColorPicker();
});
