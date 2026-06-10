<?php

require_once dirname(__FILE__) . '/../conf/config';
require_once dirname(__FILE__) . '/fns.php';

$bbox = get_bbox();
$layer = get('layer', '.*');
$cfg = get_confirm_cfg();
$layer_cfg = get_confirm_layer_cfg($layer);

$w = $bbox[0];
$s = $bbox[1];
$e = $bbox[2];
$n = $bbox[3];

$poly = "POLYGON (($w $n, $e $n, $e $s, $w $s, $w $n))";
$query = str_replace("__BBOX__", $poly, $layer_cfg['query']);
$query = str_replace('__GEOMETRY__', "geometry: { intersects: \"$poly\" },", $query);
$query = str_replace('__REVISION__', '', $query);
$params = [ "query" => $query ];
$result = make_request($cfg['url'], $cfg['key'], $params, "Basic")->data->features;

foreach ($result as $feature) {
    $filter = $layer_cfg['filter'] ?? null;
    if (!$filter || $filter($feature)) {
        $features[] = data_as_geojson($feature, $layer_cfg);
    }
}

print json_encode($geojson);

# ---

function data_as_geojson($feature, $layer_cfg) {
    list($pre, $lon, $lat) = explode(" ", $feature->geometry);
    $lon = substr($lon, 1);
    $lat = substr($lat, 0, -1);
    unset($feature->geometry);
    if ($formatter = $layer_cfg['formatter'] ?? null) {
        $feature = $formatter($feature);
    }
    return [
        "type" => "Feature",
        "geometry" => [
            "type" => "Point",
            "coordinates" => [floatval($lon), floatval($lat)],
        ],
        "properties" => $feature,
    ];
}
