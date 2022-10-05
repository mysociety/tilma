<?php

require_once dirname(__FILE__) . '/../conf/config';
require_once dirname(__FILE__) . '/fns.php';

header('Content-Type: application/json');

$lat = get('lat', '[0-9.-]+');
$lon = get('lon', '[0-9.-]+');
if ($lat=='' || $lon=='') {
    print json_encode(["code" => 400, "error" => "Bad request"]);
    exit;
}

$context = stream_context_create(array(
    'http' => array('ignore_errors' => true),
));
$url = "https://mapit.mysociety.org/point/4326/$lon,$lat?type=OLF&api_key=" . MAPIT_API_KEY;
$mapit = file_get_contents($url, false, $context);
$mapit = json_decode($mapit, true);

if (array_key_exists('code', $mapit) && $mapit['code'] != 200) {
    print json_encode($mapit);
    exit;
}

$code = array_values($mapit)[0]["codes"]["ons"];

$fp = fopen(dirname(__FILE__) . '/../../layers/lsoa-to-decile/UK_IMD_E.csv', 'r');
if (!$fp) {
    print json_encode(["code" => 500, "error" => "Could not find CSV file"]);
    exit;
}

while ($data = fgetcsv($fp)) {
    $lsoa = $data[1];
    $decile = $data[9];
    if ($lsoa == $code) {
        print json_encode(["code" => 200, "UK_IMD_E_pop_decile" => $decile ]);
        fclose($fp);
        exit;
    }
}

print json_encode(["code" => 404, "error" => "No result found"]);
fclose($fp);
