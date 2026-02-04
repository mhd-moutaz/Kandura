// Firebase Cloud Messaging Service Worker
// This file handles background push notifications

importScripts('https://www.gstatic.com/firebasejs/11.1.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/11.1.0/firebase-messaging-compat.js');

// Initialize Firebase in the service worker
// IMPORTANT: Replace these with your actual Firebase config values from Firebase Console
firebase.initializeApp({
    apiKey: "AIzaSyA8_hlhq2bz3IHsekGuRUIumjzYOQTznkE",
    authDomain: "kandura-c8aac.firebaseapp.com",
    projectId: "kandura-c8aac",
    storageBucket: "kandura-c8aac.firebasestorage.app",
    messagingSenderId: "873081966521",
    appId: "1:873081966521:web:257332641abce1d85f3068",
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('Received background message:', payload);

    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || 'You have a new notification',
        icon: '/favicon.ico',
        badge: '/favicon.ico',
        tag: payload.data?.design_id || 'notification',
        data: payload.data,
        requireInteraction: true,
        actions: [
            {
                action: 'view',
                title: 'View Design'
            },
            {
                action: 'close',
                title: 'Dismiss'
            }
        ]
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification clicks
self.addEventListener('notificationclick', (event) => {
    console.log('Notification clicked:', event);
    event.notification.close();

    if (event.action === 'view' && event.notification.data?.design_id) {
        // Open the design details page
        const designUrl = `/designs/${event.notification.data.design_id}`;
        event.waitUntil(
            clients.openWindow(designUrl)
        );
    } else if (event.action !== 'close') {
        // Default action: open dashboard
        event.waitUntil(
            clients.openWindow('/dashboard')
        );
    }
});
