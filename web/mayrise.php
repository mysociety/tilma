<?php

require_once dirname(__FILE__) . '/../conf/config';
require_once dirname(__FILE__) . '/fns.php';

$bbox = get_bbox();
$type = get('type', '[MX]', 'M');
$result = fetch_data($bbox);

foreach ($result as $feature) {
    if ($type == 'M' && ($feature->UnitTypeCode == 'X' || !in_array($feature->OwnershipCode, ['LBM', 'LBMH', 'LBML', 'LBMP', 'LBMW']))) {
        continue;
    }
    if ($type == 'X' && $feature->UnitTypeCode != 'X') {
        continue;
    }
    if (!$feature->FaultMaintenanceSupport) {
        continue;
    }
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
    $client = new SoapClient(MAYRISE_API_URL, array('exceptions' => 0));
    $result = $client->GetUnitsByRect($bbox[0], $bbox[2], $bbox[1], $bbox[3]);
    if (is_soap_fault($result)) {
        print EMPTY_RESULT;
        exit;
    }
    return $result;
}
