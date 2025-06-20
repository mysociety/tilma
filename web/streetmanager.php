<?php

require_once dirname(__FILE__) . '/../conf/config';
require_once dirname(__FILE__) . '/fns.php';

$bbox = get_bbox();
$today = date('Y-m-d');
$start_date = get('start_date', '\d\d\d\d-\d\d-\d\d', $today);
if (get('end_today', '1')) {
    $default_end_date = $today;
} else {
    $default_end_date = date('Y-m-d', time()+86400*13*7);
}
$end_date = get('end_date', '\d\d\d\d-\d\d-\d\d', $default_end_date);
$points = get('points', '[01]', 0);

$location_query = $points ? 'ST_Centroid(location)' : 'location';
$dbh = db_connect();
$query = $dbh->prepare("
SELECT ST_AsGeoJSON($location_query) as geojson, * FROM streetmanager
WHERE
    st_intersects(location, ST_SetSRID(ST_MakeBox2D(ST_Point(?,?), ST_Point(?, ?)), 27700))
    AND proposed_start_date >= ?
    AND proposed_end_date <= ?
");
$query->execute([$bbox[0], $bbox[1], $bbox[2], $bbox[3], $start_date, $end_date]);
foreach ($query as $row) {
    if (in_array($row['permit_status'], ['closed', 'cancelled', 'revoked', 'refused'])) {
        continue;
    }
    $features[] = data_as_geojson($row);
}

print json_encode($geojson);

function data_as_geojson($row) {
    $location_type = strtolower($row['works_location_type']);
    $tm = strtolower($row['traffic_management_type']);
    $category = $row['work_category'];
    if ($category == 'Major (PAA)') $category = 'Major';

    $summary = "$category works";
    if ($location_type) {
        $summary .= " in $location_type";
    }
    $summary .= ", with $tm";

    $properties = [
        'work_ref' => $row['permit_reference_number'],
        'start_date' => $row['proposed_start_date'],
        'end_date' => $row['proposed_end_date'],
        'summary' => $summary,
        'promoter' => prettify_text($row['promoter_organisation']),
    ];
    return [
        "type" => "Feature",
        "geometry" => json_decode($row['geojson'], 1),
        "properties" => $properties,
    ];
}

function prettify_text($text) {
    $text = ucwords(strtolower($text));
    $text = str_replace('Of ', 'of ', $text);
    $text = preg_replace('#\bBt\b#', 'BT', $text);
    $text = preg_replace('#\bUk\b#', 'UK', $text);
    $text = preg_replace('#\btfl\b#i', 'TfL', $text);
    $text = preg_replace('#\bHs2\b#i', 'HS2', $text);
    return $text;
}
