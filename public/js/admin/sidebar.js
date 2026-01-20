// Sidebar JavaScript

document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const pinButton = document.getElementById('pinButton');
    const overlay = document.getElementById('sidebarOverlay');

    // Check for saved pinned state
    const isPinned = localStorage.getItem('sidebarPinned') === 'true';
    if (isPinned) {
        sidebar.classList.remove('collapsed');
        sidebar.classList.add('pinned');
        if (window.innerWidth <= 768) {
            overlay.classList.add('active');
        }
    }

    // Pin/Unpin functionality
    pinButton.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.toggle('pinned');
        sidebar.classList.toggle('collapsed');

        const pinned = sidebar.classList.contains('pinned');
        localStorage.setItem('sidebarPinned', pinned);

        // Handle overlay on mobile
        if (window.innerWidth <= 768) {
            if (pinned) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
    });

    // Close sidebar on mobile when clicking overlay
    overlay.addEventListener('click', function() {
        if (window.innerWidth <= 768) {
            sidebar.classList.remove('pinned');
            sidebar.classList.add('collapsed');
            overlay.classList.remove('active');
            localStorage.setItem('sidebarPinned', 'false');
        }
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            overlay.classList.remove('active');
        } else if (sidebar.classList.contains('pinned')) {
            overlay.classList.add('active');
        }
    });
});
