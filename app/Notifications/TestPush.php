<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

class TestPush extends Notification
{
    public function via($notifiable)
    {
        return ['webpush'];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('NestPlaner')
            ->body('Testbenachrichtigung funktioniert!')
            ->data(['url' => '/dashboard']);
    }
}
