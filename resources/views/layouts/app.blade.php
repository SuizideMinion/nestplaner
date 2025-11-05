<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <script>
            function urlBase64ToUint8Array(base64String) {
                const padding = '='.repeat((4 - base64String.length % 4) % 4);
                const base64 = (base64String + padding)
                    .replace(/-/g, '+')
                    .replace(/_/g, '/');
                const rawData = atob(base64);
                const outputArray = new Uint8Array(rawData.length);
                for (let i = 0; i < rawData.length; ++i) {
                    outputArray[i] = rawData.charCodeAt(i);
                }
                return outputArray;
            }

            if ('serviceWorker' in navigator && 'PushManager' in window) {
                navigator.serviceWorker.register('/service-worker.js').then(function (reg) {
                    console.log("Service Worker registriert:", reg.scope);

                    Notification.requestPermission().then(function (permission) {
                        if (permission !== 'granted') {
                            console.warn('Push-Benachrichtigungen nicht erlaubt');
                            return;
                        }

                        const vapidKey = "{{ config('webpush.vapid.public_key') }}";
                        console.log("VAPID Key:", vapidKey);

                        reg.pushManager.subscribe({
                            userVisibleOnly: true,
                            applicationServerKey: urlBase64ToUint8Array(vapidKey)
                        })
                            .then(function (subscription) {
                                console.log("Subscription erstellt:", subscription);
                                return fetch("{{ route('webpush.subscribe') }}", {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify(subscription)
                                });
                            })
                            .then(res => res.text().then(t => console.log("Subscription gespeichert:", res.status, t)))
                            .catch(err => console.error("Subscription Fehler:", err));
                    });
                });
            }
        </script>


        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>


</html>
