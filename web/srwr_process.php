<?php

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

	 if ($row[1] == '006') {
		$printrow[$row[2]]['project_ref'] = $row[3];
	 } else if ($row[1] == '007') {
	    $printrow[$row[2]]['works_location_coordinates'] = $row[12];
	    $printrow[$row[2]]['location_text'] = $row[6];
	 } else if ($row[1] == '010') {
	    $printrow[$row[2]]['works_location_type'] = $row[9];
	 } else if ($row[1] == '008') {
	   if ($row[6]) {
	      $printrow[$row[2]]['proposed_start_date'] = _stripTime($row[6]);
	   } else if ($row[13]) {
	      $printrow[$row[2]]['proposed_start_date'] = _stripTime($row[13]);
	   }
	   if ($row[9]) {
	      $printrow[$row[2]]['proposed_end_date'] = _stripTime($row[9]);
	   } else if ($row[10]) {
	      $printrow[$row[2]]['proposed_end_date'] = _stripTime($row[10]);
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

	$non_primary_columns = ['promoter_organisation', 'location',
        'works_location_type', 'proposed_start_date', 'proposed_end_date', 'work_category',
        'work_status', 'traffic_management_type', 'permit_status', 'close_footway'];
    $excluded_columns = $non_primary_columns;
    foreach ($excluded_columns as $k => $v) {
        $excluded_columns[$k] = "EXCLUDED." . $v;
    }

	$dbh = db_connect();
    $query = $dbh->prepare("INSERT INTO streetmanager
         (permit_reference_number, " . join(', ', $non_primary_columns) . ")
         VALUES (?" . str_repeat(",?", count($non_primary_columns)) . ")
         ON CONFLICT (permit_reference_number) DO UPDATE SET
         (" . join(', ', $non_primary_columns) . ") = (" . join(', ', $excluded_columns) . ")
    ");
	foreach ($printrow as $key => $object) {
		$query->execute([
			$key, $object['project_name'], 'SRID=27700;' . $object['works_location_coordinates'],
			$object['works_location_type'], $object['proposed_start_date'], $object['proposed_end_date'], 'work_cat',
			'status', 'traffic_management', 'Permit', 'Footway'
		]);
	};
}

function db_connect() {
    $dsn = 'pgsql:';
    if (OPTION_SM_DB_HOST) $dsn .= 'host=' . OPTION_SM_DB_HOST . ';';
    if (OPTION_SM_DB_PORT) $dsn .= 'port=' . OPTION_SM_DB_PORT . ';';
    if (OPTION_SM_DB_NAME) $dsn .= 'dbname=' . OPTION_SM_DB_NAME . ';';
    $dbh = new PDO($dsn, OPTION_SM_DB_USER, OPTION_SM_DB_PASS);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}
