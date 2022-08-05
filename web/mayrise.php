<?php

require_once dirname(__FILE__) . '/../conf/mayrise';

$features = [];
$geojson = [ "type" => "FeatureCollection", "features" => &$features ];

define('EMPTY_RESULT', json_encode($geojson));

header('Content-Type: application/json');

$bbox = get_bbox();
$result = fetch_data($bbox);

foreach ($result as $feature) {
    $features[] = data_as_geojson($feature);
}

print json_encode($geojson);

# ---

function data_as_geojson($feature) {
    return [
        "type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [$feature->Easting, $feature->Northing],
        ],
        "properties" => [
            "UnitID" => $feature->UnitID,
            "UnitNumber" => $feature->UnitNumber,
        ],
    ];
}

function fetch_data($bbox) {
    $client = new SoapClient(API_URL, array('exceptions' => 0));
    $result = $client->GetUnitsByRect($bbox[0], $bbox[2], $bbox[1], $bbox[3]);
    if (is_soap_fault($result)) {
        print EMPTY_RESULT;
        exit;
    }
    return $result;
}

function get_bbox() {
    $bbox = get('bbox', '[\d.]+,[\d.]+,[\d.]+,[\d.]+');
    if (!$bbox) {
        print EMPTY_RESULT;
        exit;
    }
    $bbox = explode(',', $bbox);
    return $bbox;
}

function get($id, $regex, $default = null) {
    $var = isset($_GET[$id]) ? $_GET[$id] : '';
    if ($var && preg_match('#^' . $regex . '$#', $var)) {
        return $var;
    }
    return $default;
}
