<?php

require_once dirname(__FILE__) . '/../../conf/config';
require_once dirname(__FILE__) . '/../fns.php';

$bbox = get_bbox();
$layer = get('layer', '.*');
$token = get_alloy_token();
$srs = get('srs', '[0-9]*');

if ($srs) {
    # Convert bbox to 4326
    $input = json_encode([
        "type" => "FeatureCollection",
        "features" => [
            ["type" => "Feature", "geometry" => ["type" => "Point","coordinates" => [floatval($bbox[0]), floatval($bbox[1])]]],
            ["type" => "Feature", "geometry" => ["type" => "Point","coordinates" => [floatval($bbox[2]), floatval($bbox[3])]]],
        ],
    ]);
    $output = ogr2ogr($input, $srs, 4326);
    $output = json_decode($output);
    $bbox = [
        $output->features[0]->geometry->coordinates[0],
        $output->features[0]->geometry->coordinates[1],
        $output->features[1]->geometry->coordinates[0],
        $output->features[1]->geometry->coordinates[1],
    ];
}

$page = 1;
while (true) {
    list($count, $results) = alloy_process_page($token, $layer, $bbox, $page);
    $features = array_merge($features, $results);
    if ($count == 100) {
        $page++;
    } else {
        break;
    }
}

$json = json_encode($geojson);

if ($srs) {
    $json = ogr2ogr($json, 4326, $srs);
}

print $json;

# ---

function data_as_geojson($feature) {
    $geometry = $feature['geometry'];
    unset($feature['geometry']);
    return [
        "type" => "Feature",
        "geometry" => $geometry,
        "properties" => $feature,
    ];
}

function ogr2ogr($input, $from, $to) {
    $command = "ogr2ogr -t_srs EPSG:$to -s_srs EPSG:$from -f GeoJSON /vsistdout/ /vsistdin/?buffer_limit=-1";
    $process = proc_open($command, [
        0 => ["pipe", "r"],
        1 => ["pipe", "w"],
    ], $pipes);
    $output = null;
    if (is_resource($process)) {
        fwrite($pipes[0], $input);
        fclose($pipes[0]);
        $output = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        proc_close($process);
    }
    return $output;
}
