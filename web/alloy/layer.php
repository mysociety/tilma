<?php

require_once dirname(__FILE__) . '/../../conf/config';
require_once dirname(__FILE__) . '/../fns.php';

$bbox = get_bbox();
$layer = get('layer', '.*');
$token = get_alloy_token();

$page = 1;
while (true) {
    $results = alloy_process_page($token, $layer, $bbox, $page);
    $features = array_merge($features, $results);
    if (count($results) == 100) {
        $page++;
    } else {
        break;
    }
}

print json_encode($geojson);

# ---

function data_as_geojson($feature) {
    return [
        "type" => "Feature",
        "geometry" => $feature['geometry'],
        "properties" => [
            "title" => $feature['title'],
            "itemId" => $feature['id'],
        ],
    ];
}
