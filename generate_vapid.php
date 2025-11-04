<?php
require __DIR__ . '/vendor/autoload.php';

use Minishlink\WebPush\VAPID;

$keys = VAPID::createVapidKeys(['VAPID' => ['subject' => 'mailto:example@example.com']]);

echo "Public Key:  {$keys['publicKey']}\n";
echo "Private Key: {$keys['privateKey']}\n";
