<?php

require_once dirname(__FILE__) . '/../conf/mayrise';
require_once dirname(__FILE__) . '/fns.php';

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
