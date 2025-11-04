self.addEventListener('push', function(event) {
    if (!event.data) {
        console.log('Push event but no data');
        return;
    }

    const data = event.data.json();
    const title = data.title || 'NestPlaner';
    const options = {
        body: data.body || 'Neue Benachrichtigung',
        icon: data.icon || '/icon-192x192.png',
        data: data.data || {},
        actions: data.actions || []
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const url = event.notification.data.url || '/';
    event.waitUntil(clients.openWindow(url));
});
