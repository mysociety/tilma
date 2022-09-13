<?php

# Given a URL from the proxy config and an ID, look up the unit type

require_once dirname(__FILE__) . '/../../conf/alloy';
require_once dirname(__FILE__) . '/../fns.php';

$id = get('id', '[a-f0-9]{24}');
$url = get('url', '.+');
$token = API_KEYS[$url];

process($token, $id);

function process($token, $id) {
    $design = "designs_streetLights";
    $join_attributes = ["root.attributes_streetLightingUnitsUnitType.attributes_streetLightingUnitTypesDescription"];
    $private_types = [
        "Private light",
        "Private bollard",
        "Private sign",
        "Priv speed reac",
        "Private f p",
    ];
    $url = "https://api.uk.alloyapp.io/api/aqs/join";

    $params = [
        "aqs" => [
               "type" => "Join",
               "properties" => [
                   "collectionCode" => ["Live"],
                   "dodiCode" => $design,
                   "joinAttributes" => $join_attributes,
               ],
               "children"=> [[
                   "type" => "Equals",
                   "children" => [
                       ["type" => "ItemProperty", "properties" => ["itemPropertyName" => "itemId"]],
                       ["type" => "AlloyId", "properties" => ["value" => [$id]]]
                   ]
               ]]
           ]
       ];
    $data = make_request($url, $token, $params);

    if ($data->joinResults) {
        $type = $data->joinResults[0]->joinQueries[0]->item->attributes[0]->value;
        $private = in_array($type, $private_types);
        print json_encode(["unit_type" => $type, "private" => $private]);
    }
}
