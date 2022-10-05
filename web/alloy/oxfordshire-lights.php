<?php

require_once dirname(__FILE__) . '/../../conf/config';
require_once dirname(__FILE__) . '/../fns.php';

$bbox = get_bbox();
$layer = "designs_streetLights";
$token = get_alloy_token();

$page = 1;
while (true) {
    $results = alloy_process_page($token, $layer, $bbox, $page,
        ["root.attributes_streetLightingUnitsUnitType.attributes_streetLightingUnitTypesDescription"],
        function($result) {
            $type = $result->joinQueries[0]->item->attributes[0]->value;
            $private = is_private($type);
            return ["unit_type" => $type, "private" => $private];
        }
    );
    $features = array_merge($features, $results);
    if (count($results) == 100) {
        $page++;
    } else {
        break;
    }
}

print json_encode($geojson);

# ---

function is_private($type) {
    $private_types = [
        "Private light",
        "Private bollard",
        "Private sign",
        "Priv speed reac",
        "Private f p",
    ];
    return in_array($type, $private_types);
}

function data_as_geojson($feature) {
    return [
        "type" => "Feature",
        "geometry" => $feature['geometry'],
        "properties" => [
            "title" => $feature['title'],
            "unit_type" => $feature['unit_type'],
            "itemId" => $feature['id'],
            "private" => $feature['private'],
        ],
    ];
}
