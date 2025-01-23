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

    $non_primary_columns = ['promoter_organisation', 'location',
        'works_location_type', 'proposed_start_date', 'proposed_end_date', 'work_category',
        'work_status', 'traffic_management_type', 'permit_status', 'close_footway'];
    $excluded_columns = $non_primary_columns;
    foreach ($excluded_columns as $k => $v) {
        $excluded_columns[$k] = "EXCLUDED." . $v;
    }
    $dbh = db_connect();
    $query = $dbh->prepare("INSERT INTO streetmanager
        (permit_reference_number, " . join(', ', $non_primary_columns) . ")
        VALUES (?" . str_repeat(",?", count($non_primary_columns)) . ")
        ON CONFLICT (permit_reference_number) DO UPDATE SET
        (" . join(', ', $non_primary_columns) . ") = (" . join(', ', $excluded_columns) . ")
    ");
    $query->execute([
        $object['permit_reference_number'], $object['promoter_organisation'], 'SRID=27700;' . $object['works_location_coordinates'],
        $object['works_location_type'], $object['proposed_start_date'], $object['proposed_end_date'], $object['work_category'],
        $object['work_status'], $object['traffic_management_type'], $object['permit_status'], $object['close_footway']
    ]);
}
