// Design Show Page JavaScript

let currentImageIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize hover effects for thumbnails
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        thumb.addEventListener('mouseenter', function() {
            if (this.getAttribute('data-index') != currentImageIndex) {
                this.style.borderColor = '#93c5fd';
            }
        });
        thumb.addEventListener('mouseleave', function() {
            if (this.getAttribute('data-index') != currentImageIndex) {
                this.style.borderColor = '#e2e8f0';
            }
        });
    });
});

function changeMainImage(direction) {
    const images = document.querySelectorAll('.main-gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');
    const total = images.length;

    if (total === 0) return;

    images[currentImageIndex].style.display = 'none';
    if (thumbnails[currentImageIndex]) {
        thumbnails[currentImageIndex].style.borderColor = '#e2e8f0';
    }

    currentImageIndex += direction;

    if (currentImageIndex >= total) {
        currentImageIndex = 0;
    }
    if (currentImageIndex < 0) {
        currentImageIndex = total - 1;
    }

    images[currentImageIndex].style.display = 'block';
    if (thumbnails[currentImageIndex]) {
        thumbnails[currentImageIndex].style.borderColor = '#3b82f6';
    }
}

function showMainImage(index) {
    const images = document.querySelectorAll('.main-gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');

    if (images.length === 0) return;

    images[currentImageIndex].style.display = 'none';
    if (thumbnails[currentImageIndex]) {
        thumbnails[currentImageIndex].style.borderColor = '#e2e8f0';
    }

    currentImageIndex = index;

    images[currentImageIndex].style.display = 'block';
    if (thumbnails[currentImageIndex]) {
        thumbnails[currentImageIndex].style.borderColor = '#3b82f6';
    }
}
