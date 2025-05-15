<?php

require_once __DIR__ . '/../../conf/config';
require_once __DIR__ . '/../fns.php';
require __DIR__ . '/../../vendor/autoload.php';

use Aws\Sns\Message;
use Aws\Sns\MessageValidator;

$message = Message::fromRawPostData();
$validator = new MessageValidator();
if (!$validator->isValid($message)) {
    http_response_code(404);
    exit;
}

if ($message['Type'] === 'SubscriptionConfirmation') {
    file_get_contents($message['SubscribeURL']);
} elseif ($message['Type'] === 'Notification') {
    handleNotification($message);
}

function handleNotification($message) {
    $data = json_decode($message['Message'], 1);
    $object = $data['object_data'];

    $query = createStreetManagerQuery();
    insertIntoStreetManager($query, $object);
}
