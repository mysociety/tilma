<?php

require_once __DIR__ . '/../../conf/config';
require_once __DIR__ . '/../fns.php';
require __DIR__ . '/../../vendor/autoload.php';

$path = './srwr_data/';
$printrow = [];
$companies = [];

// Delete any previously downloaded files
deleteFiles();

// Fetch the zip file from the opendata
fetchCSVFile();

// Unzip the file to use the csv file
$csv_file = unzipCSVFile();

// Parse the data from the csv file
while ($data = fgetcsv($csv_file)) {
      collateRows($data);
}

// Attach the company names to the correct projects
addCompanies();

// Insert into the streetmanager database
insertRows();

function deleteFiles() {
	global $path;

	if (!file_exists($path)) {
		mkdir($path, 0777, true);
		return;
	}

	$files = glob($path . '*');
	foreach($files as $file) {
  		if(is_file($file)) {
    		unlink($file);
  		}
	}
}

function fetchCSVFile() {
	global $path;
	$url = 'https://downloads.srwr.scot/export/api/v1/daily';

	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($httpcode != 200) {
		die("Error: Fetching url for zip file failed");
    } else {
		$data = json_decode($response, true);
		$url = $data['url'];
		curl_setopt($ch, CURLOPT_URL, $url);
		$response = curl_exec($ch);
		if ($httpcode != 200) {
			die("Error: Fetching zip file failed");
    	} else {
			file_put_contents($path . 'daily.zip', $response);
		}
    }
    curl_close($ch);
    $ch = null;
}

function unzipCSVFile() {
	global $path;

	$zip = new ZipArchive();
	$zipfiles = glob($path . '*.zip');
	if (sizeof($zipfiles) != 1) {
		die("Error: Expecting one zip file");
	} else {
		$zip->open($zipfiles[0]);
		$zip->extractTo($path);
		$csvfiles = glob($path . '*.csv');
		if (sizeof($csvfiles) != 1) {
			die("Error: Expecting one csv file");
		} else {
			$csv_file = fopen($csvfiles[0], 'r');
			return $csv_file;
		}
	}
}

function collateRows($row) {
	 global $printrow;
	 global $companies;

	 $activityId = $row[2];
	 if ($row[1] == '006') {
		$printrow[$activityId]['project_ref'] = $row[3];
	 } else if ($row[1] == '007') {
	    $printrow[$activityId]['works_location_coordinates'] = $row[12];
	    $printrow[$activityId]['location_text'] = $row[6];
	 } else if ($row[1] == '010') {
	    $printrow[$activityId]['works_location_type'] = $row[9];
	 } else if ($row[1] == '008') {
	   if ($row[6]) {
	      $printrow[$activityId]['proposed_start_date'] = _stripTime($row[6]);
	   } else if ($row[13]) {
	      $printrow[$activityId]['proposed_start_date'] = _stripTime($row[13]);
	   }
	   if ($row[9]) {
	      $printrow[$activityId]['proposed_end_date'] = _stripTime($row[9]);
	   } else if ($row[10]) {
	      $printrow[$activityId]['proposed_end_date'] = _stripTime($row[10]);
	   }
	 } else if ($row[1] == '099') {
	   $companies[ $row[3] . $row[4] ] = $row[5];
	 }
}

function addCompanies() {
	global $printrow;
	global $companies;

	foreach ($printrow as $key => $code) {
			$company_key = $code['project_ref'];
	    	if ($companies[$company_key]) {
     			$printrow[ $key ]['project_name'] = $companies[$company_key];
			}
    }
}

function insertRows() {
	global $printrow;

	$query = createStreetManagerQuery();
	foreach ($printrow as $key => $object) {
		$object['permit_reference_number'] = $key;
		insertIntoStreetManager($query, $object);
	};
}

function _stripTime($date) {
	$date = preg_replace( '/ .*?$/', '', $date);
	return $date;
}
