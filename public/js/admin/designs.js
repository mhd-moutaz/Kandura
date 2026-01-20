// Designs Page JavaScript

// Slider functionality
const slideIndexes = {};

function changeSlide(designId, direction) {
    const images = document.querySelectorAll('.slider-image-' + designId);
    const dots = document.querySelectorAll('.slider-dot-' + designId);

    if (!slideIndexes[designId]) {
        slideIndexes[designId] = 0;
    }

    // Hide current image
    images[slideIndexes[designId]].style.opacity = '0';
    dots[slideIndexes[designId]].style.background = 'rgba(255,255,255,0.5)';

    // Calculate new index
    slideIndexes[designId] += direction;

    if (slideIndexes[designId] >= images.length) {
        slideIndexes[designId] = 0;
    }
    if (slideIndexes[designId] < 0) {
        slideIndexes[designId] = images.length - 1;
    }

    // Show new image
    images[slideIndexes[designId]].style.opacity = '1';
    dots[slideIndexes[designId]].style.background = 'white';
}

function goToSlide(designId, index) {
    const images = document.querySelectorAll('.slider-image-' + designId);
    const dots = document.querySelectorAll('.slider-dot-' + designId);

    if (!slideIndexes[designId]) {
        slideIndexes[designId] = 0;
    }

    // Hide current
    images[slideIndexes[designId]].style.opacity = '0';
    dots[slideIndexes[designId]].style.background = 'rgba(255,255,255,0.5)';

    // Show selected
    slideIndexes[designId] = index;
    images[slideIndexes[designId]].style.opacity = '1';
    dots[slideIndexes[designId]].style.background = 'white';
}

// JavaScript للتعامل مع تغيير Type وعرض القيم المناسبة
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('designOptionType');
    const valuesContainer = document.getElementById('designOptionValuesContainer');
    const valueSelect = document.getElementById('designOptionValue');

    if (typeSelect && valuesContainer && valueSelect) {
        // البيانات من Laravel
        const designOptionsElement = document.querySelector('[data-design-options]');
        const designOptions = designOptionsElement ? JSON.parse(designOptionsElement.dataset.designOptions) : [];

        typeSelect.addEventListener('change', function() {
            const selectedType = this.value;

            if (selectedType) {
                // إظهار حقل القيم
                valuesContainer.style.display = 'block';

                // مسح الخيارات القديمة
                valueSelect.innerHTML = '<option value="">All Values</option>';

                // فلترة الخيارات حسب النوع المختار
                const filteredOptions = designOptions.filter(option => option.type === selectedType);

                // إضافة الخيارات الجديدة
                filteredOptions.forEach(function(option) {
                    const optionElement = document.createElement('option');
                    optionElement.value = option.id;
                    optionElement.textContent = option.name.en || option.name.ar || 'N/A';
                    valueSelect.appendChild(optionElement);
                });
            } else {
                // إخفاء حقل القيم
                valuesContainer.style.display = 'none';
                valueSelect.innerHTML = '<option value="">All Values</option>';
            }
        });
    }

    // Hover effect for cards
    document.querySelectorAll('[style*="box-shadow:0 2px 8px"]').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 8px 20px rgba(0,0,0,0.12)';
            this.style.transform = 'translateY(-4px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
            this.style.transform = 'translateY(0)';
        });
    });
});
