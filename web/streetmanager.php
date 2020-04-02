<?php

require_once dirname(__FILE__) . '/../conf/streetmanager';

class Api {
    private $base;
    private $token;

    public function __construct($base, $username, $password, $prefix) {
        $this->base = $base;
        $this->token = $this->getToken($username, $password, $prefix);
    }

    private function getToken($username, $password, $prefix) {
        $memcache = new Memcache;
        $memcache->connect('localhost', 11211) or die ("Could not connect");
        $token = $memcache->get($prefix . 'token');
        if (!$token) {
            $refresh = $memcache->get($prefix . 'refresh');
            if ($refresh) {
                $result = $this->call('party/refresh', 'POST', [
                    'refresh_token' => $refresh
                ]);
                $token = $result->id_token;
                $memcache->set($prefix . 'token', $token, 0, 3600);
            } else {
                $result = $this->call('work/authenticate', 'POST', [
                    'emailAddress' => $username,
                    'password' => $password
                ]);
                $token = $result->idToken;
                $refresh = $result->refreshToken;
                $memcache->set($prefix . 'token', $token, 0, 3600);
                $memcache->set($prefix . 'refresh', $refresh, 0, 86400);
            }
        }
        return $token;
    }

    public function call($url, $method, $data) {
        $data = http_build_query($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($method === 'GET') {
            curl_setopt($ch, CURLOPT_URL, $this->base . $url . '?' . $data);
        } else {
            curl_setopt($ch, CURLOPT_URL, $this->base . $url);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if ($this->token) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["Token: " . $this->token]);
        }
        $output = curl_exec($ch);
        curl_close($ch);
        return json_decode($output);
    }
}

# ---

$api = new \Api(API_URL, USERNAME, PASSWORD, MEMCACHE_PREFIX);

header('Content-Type: application/json');

function get($id, $regex, $default = null) {
    $var = isset($_GET[$id]) ? $_GET[$id] : '';
    if ($var && preg_match('#^' . $regex . '$#', $var)) {
        return $var;
    }
    return $default;
}

$bbox = get('bbox', '\d+,\d+,\d+,\d+');
if (!$bbox) {
    print '{"type":"FeatureCollection","features":[]}';
    exit;
}
$bbox = explode(',', $bbox);

$start_date = get('start_date', '\d\d\d\d-\d\d-\d\d', date('Y-m-d'));
$default_end_date = date('Y-m-d', time()+86400*13*7);
$end_date = get('end_date', '\d\d\d\d-\d\d-\d\d', $default_end_date);
$forward_plans = get('forward_plans', '[01]', 1);

$params = [
    'minEasting' => $bbox[0],
    'minNorthing' => $bbox[1],
    'maxEasting' => $bbox[2],
    'maxNorthing' => $bbox[3],
    'start_date' => $start_date,
    'end_date' => $end_date,
];

$data = $api->call('geojson/works', 'GET', $params);
foreach ($data->features as $feature) {
    $props = $feature->properties;
    # Would be nice to have description here for summary
    $tm = str_replace('_', ' ', $props->traffic_management_type);
    $category = ucfirst(str_replace('_', ' ', $props->work_category));
    if ($category == 'Paa') $category = 'Major';
    $feature->properties = [
        'start_date' => $props->start_date,
        'end_date' => $props->end_date,
        'summary' => "$category works, with $tm",
        'promoter' => ucwords(strtolower($props->promoter_organisation)),
    ];
}

$data2 = $api->call('geojson/activities', 'GET', $params);
foreach ($data2->features as $feature) {
    $props = $feature->properties;
    $feature->properties = [
        'start_date' => $props->start_date,
        'end_date' => $props->end_date,
        'summary' => $props->activity_location_description,
    ];
}
$data->features = array_merge($data->features, $data2->features);

if ($forward_plans) {
    $data3 = $api->call('geojson/forward-plans', 'GET', $params);
    foreach ($data3->features as $feature) {
        $props = $feature->properties;
        $feature->properties = [
            'start_date' => $props->start_date,
            'end_date' => $props->end_date,
            'summary' => $props->location_description,
            'description' => $props->description_of_work,
            'promoter' => $props->promoter_organisation,
        ];
    }
    $data->features = array_merge($data->features, $data3->features);
}

print json_encode($data);
