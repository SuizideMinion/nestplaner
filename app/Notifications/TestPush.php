<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TestPush extends Notification
{
    use Queueable;

    public function via($notifiable)
    {
        // explizit WebPushChannel angeben, sonst kennt Laravel den Driver nicht
        return [WebPushChannel::class];
    }

    public function toWebPush($notifiable, $notification)
    {
        return (new WebPushMessage)
            ->title('NestPlaner')
            ->body('Dein Push-System funktioniert 🎉')
            ->icon('/icon-192x192.png')
            ->action('Jetzt öffnen', '/')
            ->data(['url' => '/']);
    }
}
