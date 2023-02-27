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

function alloy_process_page($token, $design, $bbox, $page) {
    $k = get('url', 'https://[a-z.]+');
    $cfg = ALLOY_LAYER_CONFIG[$k][$design] ?? [];

    $attributes = ["attributes_itemsTitle", "attributes_itemsGeometry"];
    $extra_attributes = $cfg['attributes'] ?? [];
    $attributes = array_merge($attributes, array_keys($extra_attributes));

    $join_attributes = $cfg['join'] ?? null;

    $query = $join_attributes ? 'join' : 'query';
    $url = "https://api.uk.alloyapp.io/api/aqs/$query?pageSize=100&page=$page";

    $params = alloy_query(ucfirst($query), $design, $attributes, $bbox, $join_attributes);
    $data = make_request($url, $token, $params);

    $extra = [];
    if (property_exists($data, 'joinResults')) {
        foreach ($data->joinResults as $result) {
            $id = $result->itemId;
            $type = $result->joinQueries[0]->item->attributes[0]->value;
            $extra[$id] = $cfg['join_extra']($type);
        }
    }

    $count = count($data->results);

    $features = [];
    foreach ($data->results as $result) {
        $id = $result->itemId;
        $feature = $extra[$id] ?? [];
        $feature['itemId'] = $id;
        foreach ($result->attributes as $attr) {
            if ($attr->attributeCode == 'attributes_itemsGeometry') {
                $feature['geometry'] = $attr->value;
            }
            if ($attr->attributeCode == 'attributes_itemsTitle') {
                $feature['title'] = $attr->value;
            }
            if (in_array($attr->attributeCode, array_keys($extra_attributes))) {
                $feature[$extra_attributes[$attr->attributeCode]] = $attr->value;
            }
        }
        if (array_key_exists('filter', $cfg) && !$cfg['filter']($feature)) {
            continue;
        }
        $features[] = data_as_geojson($feature);
    }

    return [$count, $features];
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
