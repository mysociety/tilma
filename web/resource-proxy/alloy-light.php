<?php

# Given a URL from the proxy config and an ID, look up the unit type

class XmlParser {
    public $results = array();

    function __construct($f = "proxy.config") {
    $file = dirname(__FILE__) . "/../../conf/$f";
    $xml = file_get_contents($file);
        $parser = xml_parser_create();
        xml_set_object($parser, $this);
        xml_set_element_handler($parser, "tagStart", "tagEnd");
        xml_parse($parser, $xml);
        xml_parser_free($parser);
    }

    function tagStart($parser, $name, $attrs) {
        $attrs = array_change_key_case($attrs, CASE_LOWER);
        $tag = array(strtolower($name) => $attrs);
        array_push($this->results, $tag);
    }

    function tagEnd($parser, $name) {
        $this->results[count($this->results)-2]['childrens'][] = $this->results[count($this->results)-1];
        array_pop($this->results);
    }
}

header("Content-Type: application/json");

$id = isset($_GET['id']) ? $_GET['id'] : null;
if (!preg_match('#^[a-f0-9]{24}$#', $id)) {
    exit;
}
$url = isset($_GET['url']) ? $_GET['url'] : null;

$xmlParser = new XmlParser();
$data = $xmlParser->results[0]['childrens'][0]['childrens'];
foreach ($data as $serverurl) {
    $serverurl = $serverurl['serverurl'];
    if (stripos($serverurl['url'], $url) === 0) {
        process($serverurl, $id);
    }
}

function process($serverurl, $id) {
    $design = "designs_streetLights";
    $join_attributes = ["root.attributes_streetLightingUnitsUnitType.attributes_streetLightingUnitTypesDescription"];
    $private_types = [
        "Private light",
        "Private bollard",
        "Private sign",
        "Priv speed reac",
        "Private f p",
    ];
    $token = $serverurl['token'];
    $url = str_replace('layer', 'aqs/join', $serverurl['hostredirect']);
    $url .= "?token=$token";

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

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode != 200) {
        header("HTTP/1.1 $httpcode Error");
    } else {
        $data = json_decode($response);
        if ($data->joinResults) {
            $type = $data->joinResults[0]->joinQueries[0]->item->attributes[0]->value;
            $private = in_array($type, $private_types);
            print json_encode(["unit_type" => $type, "private" => $private]);
        }
    }
    curl_close($ch);
    $ch = null;
}
