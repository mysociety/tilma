<?php

require_once dirname(__FILE__) . '/../conf/config';
require_once dirname(__FILE__) . '/fns.php';

print EMPTY_RESULT;
exit;

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

    public function call_no_error($url, $method, $data) {
        $output = $this->call($url, $method, $data);
        if (property_exists($output, 'error')) {
            return json_decode(EMPTY_RESULT);
        }
        return $output;
    }
}

# ---

$api = new \Api(SM_API_URL, SM_USERNAME, SM_PASSWORD, SM_MEMCACHE_PREFIX);

$bbox = get_bbox();

$today = date('Y-m-d');
$start_date = get('start_date', '\d\d\d\d-\d\d-\d\d', $today);
if (get('end_today', '1')) {
    $default_end_date = $today;
} else {
    $default_end_date = date('Y-m-d', time()+86400*13*7);
}
$end_date = get('end_date', '\d\d\d\d-\d\d-\d\d', $default_end_date);
$forward_plans = get('forward_plans', '[01]', 1);
$points = get('points', '[01]', 0);

$params = [
    'minEasting' => $bbox[0],
    'minNorthing' => $bbox[1],
    'maxEasting' => $bbox[2],
    'maxNorthing' => $bbox[3],
    'start_date' => $start_date,
    'end_date' => $end_date,
];

$data = $api->call_no_error('geojson/works', 'GET', $params);
$features = [];
foreach ($data->features as $feature) {
    $props = $feature->properties;
    if (in_array($props->permit_status, ['closed', 'cancelled', 'revoked', 'refused'])) {
        continue;
    }
    # Would be nice to have description here for summary
    $tm = str_replace('_', ' ', $props->traffic_management_type);
    $category = ucfirst(str_replace('_', ' ', $props->work_category));
    $category = str_replace('Hs2', 'HS2', $category);
    if ($category == 'Paa') $category = 'Major';
    $feature->properties = [
        'work_ref' => $props->work_reference_number,
        'start_date' => $props->start_date,
        'end_date' => $props->end_date,
        'summary' => "$category works, with $tm",
        'promoter' => prettify_text($props->promoter_organisation),
    ];
    if ($points) {
        $feature->geometry = $props->work_centre_point;
    }
    $features[] = $feature;
}

$data2 = $api->call_no_error('geojson/activities', 'GET', $params);
foreach ($data2->features as $feature) {
    $props = $feature->properties;
    if ($props->activity_type == 'section58' || $props->cancelled) {
        continue;
    }
    $feature->properties = [
        'work_ref' => $props->activity_reference_number,
        'start_date' => $props->start_date,
        'end_date' => $props->end_date,
        'summary' => $props->activity_name,
        'description' => $props->activity_location_description,
    ];
    if ($points) {
        $feature->geometry = $props->activity_centre_point;
    }
    $features[] = $feature;
}

if ($forward_plans) {
    $data3 = $api->call_no_error('geojson/forward-plans', 'GET', $params);
    foreach ($data3->features as $feature) {
        $props = $feature->properties;
        if ($props->forward_plan_status == 'cancelled') {
            continue;
        }
        $feature->properties = [
            'work_ref' => $props->work_reference_number,
            'start_date' => $props->start_date,
            'end_date' => $props->end_date,
            'summary' => $props->location_description,
            'description' => $props->description_of_work,
            'promoter' => $props->promoter_organisation,
        ];
        if ($points) {
            $feature->geometry = $props->work_centre_point;
        }
        $features[] = $feature;
    }
}

$data->features = $features;
print json_encode($data);

function prettify_text($text) {
    $text = ucwords(strtolower($text));
    $text = str_replace('Of ', 'of ', $text);
    $text = preg_replace('#\bBt\b#', 'BT', $text);
    $text = preg_replace('#\bUk\b#', 'UK', $text);
    $text = preg_replace('#\btfl\b#i', 'TfL', $text);
    $text = preg_replace('#\bHs2\b#i', 'HS2', $text);
    return $text;
}
