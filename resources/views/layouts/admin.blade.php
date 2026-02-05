<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('messages.dashboard')) - {{ __('messages.app_name') }}</title>

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <!-- Bootstrap CSS (RTL/LTR) -->
    @if(app()->getLocale() === 'ar')
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    @else
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    @endif

    <!-- Custom CSS -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin/sidebar.css') }}" rel="stylesheet">

    <!-- RTL Adjustments -->
    @if(app()->getLocale() === 'ar')
    <style>
        body { font-family: 'Segoe UI', Tahoma, Arial, sans-serif; }
        .sidebar { right: auto; left: 0; }
        .sidebar.collapsed { transform: translateX(-100%); }
        .main-content { margin-left: 0; margin-right: auto; }
        .sidebar:not(.collapsed) ~ .main-content { margin-left: var(--sidebar-width, 250px); }
    </style>
    @endif

    @stack('styles')
</head>

<body>
    @include('layouts.sidebar')

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Sidebar JS -->
    <script src="{{ asset('js/admin/sidebar.js') }}"></script>

    <!-- Firebase Cloud Messaging -->
    <script type="module">
        import {
            initializeApp
        } from 'https://www.gstatic.com/firebasejs/11.1.0/firebase-app.js';
        import {
            getMessaging,
            getToken,
            onMessage
        } from 'https://www.gstatic.com/firebasejs/11.1.0/firebase-messaging.js';

        // Firebase configuration - IMPORTANT: Replace with your actual config from Firebase Console
        const firebaseConfig = {
            apiKey: "AIzaSyA8_hlhq2bz3IHsekGuRUIumjzYOQTznkE",
            authDomain: "kandura-c8aac.firebaseapp.com",
            projectId: "kandura-c8aac",
            storageBucket: "kandura-c8aac.firebasestorage.app",
            messagingSenderId: "873081966521",
            appId: "1:873081966521:web:257332641abce1d85f3068",
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = getMessaging(app);

        // Request notification permission and get FCM token
        async function requestNotificationPermission() {
            try {
                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    console.log('Notification permission granted.');

                    // Register service worker
                    const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                    console.log('Service Worker registered:', registration);

                    // Wait for service worker to be ready/active
                    if (registration.installing) {
                        console.log('Service Worker installing, waiting...');
                        await new Promise((resolve) => {
                            registration.installing.addEventListener('statechange', (e) => {
                                if (e.target.state === 'activated') {
                                    console.log('Service Worker activated!');
                                    resolve();
                                }
                            });
                        });
                    } else if (registration.waiting) {
                        console.log('Service Worker waiting...');
                        await new Promise((resolve) => {
                            registration.waiting.addEventListener('statechange', (e) => {
                                if (e.target.state === 'activated') {
                                    console.log('Service Worker activated!');
                                    resolve();
                                }
                            });
                        });
                    } else if (registration.active) {
                        console.log('Service Worker already active');
                    }

                    // Small delay to ensure SW is fully ready
                    await new Promise(resolve => setTimeout(resolve, 500));

                    // Get FCM token
                    const currentToken = await getToken(messaging, {
                        vapidKey: 'BFTULXgrpSE74rq6eR56liDH-nR0MaeSNlgVu5pXJlZ8diUqBzEHrF2LLMknFZrTB2rP1QnOyewAC-EFD9T_BII',
                        serviceWorkerRegistration: registration
                    });

                    if (currentToken) {
                        console.log('FCM Token:', currentToken);

                        // Save token to server
                        const tokenData = {};
                        tokenData.fcm_token = currentToken;

                        const response = await fetch('/admin/notifications/update-fcm-token', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(tokenData)
                        });

                        if (response.ok) {
                            console.log('FCM token saved to server successfully!');
                        } else {
                            console.error('Failed to save FCM token:', response.status, await response.text());
                        }
                    } else {
                        console.log('No registration token available.');
                    }
                } else {
                    console.log('Notification permission denied.');
                }
            } catch (err) {
                console.error('Error getting notification permission:', err);
            }
        }

        // Handle foreground messages
        onMessage(messaging, (payload) => {
            console.log('Foreground message received:', payload);

            const notificationTitle = payload.notification?.title || 'New Notification';
            const notificationOptions = {
                body: payload.notification?.body || 'You have a new notification',
                icon: '/favicon.ico',
                badge: '/favicon.ico',
                tag: payload.data?.design_id || 'notification',
                requireInteraction: true
            };

            // Show browser notification
            if (Notification.permission === 'granted') {
                const notification = new Notification(notificationTitle, notificationOptions);

                notification.onclick = function(event) {
                    event.preventDefault();
                    if (payload.data?.design_id) {
                        window.open('/designs/' + payload.data.design_id, '_blank');
                    } else {
                        window.focus();
                    }
                    notification.close();
                };
            }
        });

        // Auto-request permission on page load
        if ('Notification' in window && 'serviceWorker' in navigator) {
            if (Notification.permission === 'default') {
                // Show a subtle prompt or button instead of auto-requesting
                console.log('Notification permission not yet requested');
            } else if (Notification.permission === 'granted') {
                requestNotificationPermission();
            }
        }

        // Expose function to manually enable notifications
        window.enableNotifications = requestNotificationPermission;
    </script>

    @stack('scripts')
</body>

</html>
