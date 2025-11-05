self.addEventListener('push', function (event) {
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
    } catch (e) {
        data = { title: 'Nachricht', body: event.data.text() };
    }

    const title = data.title || 'Benachrichtigung';
    const body = data.body || 'Keine Details verfügbar';
    const url = data.url || '/';

    event.waitUntil(
        self.registration.showNotification(title, {
            body: body,
            icon: '/icon.png',
            data: { url: url }
        })
    );
});

// Klick auf Notification → öffnet Seite
self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(clients.openWindow(event.notification.data.url));
});
