<?php

$features = [];
$geojson = [ "type" => "FeatureCollection", "features" => &$features ];

define('EMPTY_RESULT', json_encode($geojson));

header('Content-Type: application/json');

function get_bbox() {
    $bbox = get('bbox', '[\d.-]+,[\d.-]+,[\d.-]+,[\d.-]+');
    if (!$bbox) {
        print EMPTY_RESULT;
        exit;
    }
    $bbox = explode(',', $bbox);
    return $bbox;
}

function get_alloy_token() {
    $url = get('url', 'https://[a-z.]+');
    $token = ALLOY_API_KEYS[$url];
    if (!$token) {
        print EMPTY_RESULT;
        exit;
    }
    return $token;
}

function get($id, $regex, $default = null) {
    $var = isset($_GET[$id]) ? $_GET[$id] : '';
    if ($var && preg_match('#^' . $regex . '$#', $var)) {
        return $var;
    }
    return $default;
}

function make_request($url, $token, $params) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        "Authorization: Bearer $token",
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode != 200) {
        header("HTTP/1.1 $httpcode Error");
        $data = null;
    } else {
        $data = json_decode($response);
    }
    curl_close($ch);
    $ch = null;
    return $data;
}

function alloy_process_page($token, $design, $bbox, $page, $join_attributes=null, $join_function=null) {
    $query = $join_attributes ? 'join' : 'query';
    $url = "https://api.uk.alloyapp.io/api/aqs/$query?pageSize=100&page=$page";
    $attributes = ["attributes_itemsTitle", "attributes_itemsGeometry"];
    $params = alloy_query(ucfirst($query), $design, $attributes, $bbox, $join_attributes);
    $data = make_request($url, $token, $params);

    $extra = [];
    if (property_exists($data, 'joinResults')) {
        foreach ($data->joinResults as $result) {
            $id = $result->itemId;
            $extra[$id] = $join_function($result);
        }
    }

    $features = [];
    foreach ($data->results as $result) {
        $id = $result->itemId;
        $feature = $extra[$id];
        $feature['id'] = $id;
        foreach ($result->attributes as $attr) {
            if ($attr->attributeCode == 'attributes_itemsGeometry') {
                $feature['geometry'] = $attr->value;
            }
            if ($attr->attributeCode == 'attributes_itemsTitle') {
                $feature['title'] = $attr->value;
            }
        }
        $features[] = data_as_geojson($feature);
    }
    return $features;
}

function alloy_query($type, $design, $attributes, $bbox, $join_attributes=null) {
    $query = [
        "aqs" => [
            "type" => $type,
            "properties" => [
                "collectionCode" => ["Live"],
                "dodiCode" => $design,
                "attributes" => $attributes,
            ],
            "children"=> [[
                "type" => "GeomIntersects",
                "children" => [
                    [
                        "type" => "Attribute",
                        "properties" => [ "attributeCode" => "attributes_itemsGeometry" ]
                    ],
                    [
                        "type" => "Geometry",
                        "properties" => [
                            "value" => [
                                "type" => "Polygon",
                                "coordinates" => [[
                                    [$bbox[0], $bbox[1]],
                                    [$bbox[0], $bbox[3]],
                                    [$bbox[2], $bbox[3]],
                                    [$bbox[2], $bbox[1]],
                                    [$bbox[0], $bbox[1]],
                                ]]
                            ]
                        ]
                    ]
                ]
            ]]
        ]
    ];
    if ($join_attributes) {
        $query["aqs"]["properties"]["joinAttributes"] = $join_attributes;
    }
    return $query;
}
