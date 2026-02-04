// Enable Push Notifications Button Component
// Add this button to your admin dashboard header or sidebar

export function initNotificationButton() {
    // Create notification button if it doesn't exist
    const existingButton = document.getElementById('enable-notifications-btn');
    if (existingButton) return;

    const notificationButton = document.createElement('button');
    notificationButton.id = 'enable-notifications-btn';
    notificationButton.innerHTML = '<i class="fas fa-bell"></i> Enable Notifications';
    notificationButton.className = 'btn-notification';
    notificationButton.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: #4CAF50;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 25px;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        font-size: 14px;
        font-weight: bold;
        z-index: 1000;
        transition: all 0.3s ease;
    `;

    // Check if notifications are already enabled
    if (Notification.permission === 'granted') {
        notificationButton.style.display = 'none';
    }

    // Handle button click
    notificationButton.addEventListener('click', async () => {
        if (typeof window.enableNotifications === 'function') {
            await window.enableNotifications();
            notificationButton.textContent = '✓ Notifications Enabled';
            notificationButton.style.background = '#2196F3';
            setTimeout(() => {
                notificationButton.style.display = 'none';
            }, 2000);
        }
    });

    // Add hover effect
    notificationButton.addEventListener('mouseenter', () => {
        notificationButton.style.transform = 'scale(1.05)';
        notificationButton.style.boxShadow = '0 6px 12px rgba(0,0,0,0.15)';
    });

    notificationButton.addEventListener('mouseleave', () => {
        notificationButton.style.transform = 'scale(1)';
        notificationButton.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
    });

    document.body.appendChild(notificationButton);
}

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initNotificationButton);
} else {
    initNotificationButton();
}
