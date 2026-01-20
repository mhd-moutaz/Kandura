// Design Show Page JavaScript

let currentImageIndex = 0;
const totalImages = document.querySelectorAll('.main-gallery-image').length;

function changeMainImage(direction) {
    const images = document.querySelectorAll('.main-gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');

    images[currentImageIndex].style.display = 'none';
    if (thumbnails[currentImageIndex]) {
        thumbnails[currentImageIndex].style.borderColor = '#e2e8f0';
    }

    currentImageIndex += direction;

    if (currentImageIndex >= totalImages) {
        currentImageIndex = 0;
    }
    if (currentImageIndex < 0) {
        currentImageIndex = totalImages - 1;
    }

    images[currentImageIndex].style.display = 'block';
    if (thumbnails[currentImageIndex]) {
        thumbnails[currentImageIndex].style.borderColor = '#3b82f6';
    }
}

function showMainImage(index) {
    const images = document.querySelectorAll('.main-gallery-image');
    const thumbnails = document.querySelectorAll('.thumbnail-image');

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

// Hover effects for thumbnails
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
