<?php

require_once __DIR__ . '/../conf/config';
require_once __DIR__ . '/../web/fns.php';
require __DIR__ . '/../vendor/autoload.php';

$path = '../../data/srwr_data/';
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

// Do any data changes and checks
manipulateData();

// Remove data marked to be dropped
$printrow = _filterData();

// Insert into the streetmanager database
insertRows();

function _filterData() {
	global $printrow;

	$filtered = array_filter($printrow, function($value, $key){
		return !array_key_exists('drop_data', $value);
	}, ARRAY_FILTER_USE_BOTH);

	return $filtered;
}

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

	 $record_type = $row[1];
	 $activityId = $row[2];

	 if ($record_type == '001') { # Activity
		$printrow[$activityId]['latest_phase'] = $row[11];
		$printrow[$activityId]['promoter_id'] = $row[5];
	 } else if ($record_type == '007') { # Phase
		if ($row[7] == $printrow[$activityId]['latest_phase'] ) {
			$printrow[$activityId]['works_location_coordinates'] = $row[12];
			$printrow[$activityId]['location_text'] = $row[6];
			$printrow[$activityId]['work_category'] = _workCategory($row[9]);
			$printrow[$activityId]['work_status'] = _activityStatuses($row[10]);
		}
	 } else if ($record_type == '010') { # Site
	    $printrow[$activityId]['works_location_type'] = _siteLocations($row[9]);
	 } else if ($record_type == '008') { # Undertaker phase
	   if ($row[3] == $printrow[$activityId]['latest_phase'] && $row[6] ) {
	      $printrow[$activityId]['proposed_start_date'] = _stripTime($row[6]);
	   } else if ($row[3] == $printrow[$activityId]['latest_phase'] && $row[4]) {
	      $printrow[$activityId]['proposed_start_date'] = _stripTime($row[4]);
	   }
	   if ($row[3] == $printrow[$activityId]['latest_phase'] && $row[9]) {
	      $printrow[$activityId]['proposed_end_date'] = _stripTime($row[9]);
	   } else if ($row[3] == $printrow[$activityId]['latest_phase'] && $row[8]) {
	      $printrow[$activityId]['proposed_end_date'] = _stripTime($row[8]);
	   }
	   if ($row[3] == $printrow[$activityId]['latest_phase'] && $row[21]) {
		  $printrow[$activityId]['traffic_management_type'] = _trafficManagementType($row[20]);
		  $printrow[$activityId]['close_footway'] = $row[21] ? 'No' : 'Yes';
	   }
	 } else if ($record_type == '099') { # District
	   $companies[ $row[3] . $row[4] ] = $row[5];
	 }
}

function manipulateData() {
	global $printrow;

	foreach ($printrow as $key => $code) {
		checkDates($key, $code);
		addCompanies($key, $code);
		addMissingFields($key);
	}

}

// Put in defaults for any fields that may not have been in the data, but the database
// will be expecting

function addMissingFields($key) {
	global $printrow;

	if (!array_key_exists('work_category', $printrow[$key])) {
		$printrow[$key]['work_category'] = _workCategory('00');
	}

	if (!array_key_exists('works_location_type', $printrow[$key])) {
		$printrow[$key]['works_location_type'] = _siteLocations('0');
	}

	if (!array_key_exists('promoter_organisation', $printrow[ $key ]) ) {
		$printrow[ $key ]['promoter_organisation'] = '';
	}

	if (!array_key_exists('traffic_management_type', $printrow[ $key ]) ) {
		$printrow[ $key ]['traffic_management_type'] = _trafficManagementType('01');
	}

	if (!array_key_exists('close_footway', $printrow[ $key ]) ) {
		$printrow[ $key ]['close_footway'] = 'Not known';
	}

	if (!array_key_exists('work_status', $printrow[ $key ]) ) {
		$printrow[ $key ]['work_status'] = _activityStatuses('');
	}

	if (!array_key_exists('permit_status', $printrow[ $key ]) ) {
		$printrow[ $key ]['permit_status'] = '';
	}

}

// If there's a start date and no end date or vice versa, assume they are the same.
// Mark for deletion if there are neither.
// If the end date is older than today also mark for deletion

function checkDates($key, $code) {
	global $printrow;

	if (array_key_exists('proposed_start_date', $code) && !array_key_exists('proposed_end_date', $code)) {
		$printrow[$key]['proposed_end_date'] = $printrow[$key]['proposed_start_date'];
	} else if (array_key_exists('proposed_end_date', $code) && !array_key_exists('proposed_start_date', $code)) {
		$printrow[$key]['proposed_start_date'] = $printrow[$key]['proposed_end_date'];
	}

	if (!array_key_exists('proposed_start_date', $printrow[$key])) {
		$printrow[$key]['drop_data'] = 1;
	} else if (date_create($printrow[$key]['proposed_end_date'])->format("Y-m-d") < date_create('now')->format("Y-m-d")) {
		$printrow[$key]['drop_data'] = 1;
	}
}

// Look up the companies by code, all in the supplied csv data

function addCompanies($key, $code) {
	global $printrow;
	global $companies;

	if (array_key_exists('promoter_id', $code) ) {
		$company_key = $code['promoter_id'];
		if ( array_key_exists($company_key, $companies) ) {
			$printrow[ $key ]['promoter_organisation'] = $companies[$company_key];
		}
	}
}

function insertRows() {
	global $printrow;

	$query = createStreetManagerQuery();
	foreach ($printrow as $key => $object) {
		$object['permit_reference_number'] = 'srwr_' . $key;
		insertIntoStreetManager($query, $object);
	};
}

function _stripTime($date) {
	$date = preg_replace( '/ .*?$/', '', $date);
	return $date;
}

function _workCategory($cat_code) {

	$categories = [
		'01' => 'Minor With Excavation',
		'02' => 'Minor Without Excavation',
		'03' => 'Minor Mobile and Short Duration',
		'04' => 'Major',
		'05' => 'Standard',
		'06' => 'Urgent',
		'07' => 'Emergency',
		'09' => 'Remedial Other',
		'10' => 'Remedial Dangerous',
		'12' => 'Bar Hole',
		'13' => 'Dial Before You Dig',
		'14' => 'Unattributable Works',
		'15' => 'Defective Apparatus',
		'16' => 'Road Restriction',
		'17' => 'Diversionary Works',
		'18' => 'Works Licence',
		'19' => 'Traffic Regulation Order',
		'20' => 'Permission',
		'21' => 'Removal',
		'22' => 'Event/Disruption',
		'23' => 'Damage Report',
		'24' => 'Accepted Works',
		'26' => 'Unexpected Buried Object'
	];

	return $categories[$cat_code] ? $categories[$cat_code] : 'Unknown category';
}

function _siteLocations($site_code) {
	// Documentation says should be double digit
	// but currently single digit

	$sites = [
		'1' => 'Carriageway',
		'2' => 'Footway',
		'3' => 'Verge',
		'4' => 'Cycleway',
		'01' => 'Carriageway',
		'02' => 'Footway',
		'03' => 'Verge',
		'04' => 'Cycleway'
	];

	return $site_code && $sites[$site_code] ? $sites[$site_code] : '';
}

function _trafficManagementType($tmtype) {
	// Documentation says should be double digit
	// but currently single digit

	$tmTypes = [
		'0' => 'No Obstruction On C/W Or F/W',
		'1' => 'Traffic Management Not Yet Known',
		'6' => 'Road Closure',
		'00' => 'No Obstruction On C/W Or F/W',
		'01' => 'Traffic Management Not Yet Known',
		'06' => 'Road Closure',
		'31' => 'Road Narrowing (Two Way Working)',
		'32' => 'Portable Traffic Lights (TTLS)',
		'33' => 'Convoy Working',
		'34' => 'Stop/Go Boards Traffic Control',
		'35' => 'Priority System Traffic Control',
		'36' => 'Give and Take Traffic Control',
		'37' => 'Lane Closure',
		'38' => 'Hard Shoulder Closure',
		'39' => 'Slip Closure',
		'40' => 'Contraflow',
		'41' => 'Works Entirely On The Footway',
	];

	return $tmTypes[$tmtype] ? $tmTypes[$tmtype] : $tmTypes['01'];
}

function _activityStatuses($status) {

	$activityStatuses = [
		'01' => 'Potential',
		'03' => 'Advance Planning',
		'04' => 'Proposed',
		'05' => 'In Progress',
		'06' => 'Cleared',
		'07' => 'Closed',
		'08' => 'Closed No Excavation',
		'09' => 'Abandoned',
		'10' => 'Active',
		'11' => 'Lapsed',
		'12' => 'Awaiting Response',
		'13' => 'Accepted',
		'14' => 'Denied',
		'15' => 'In Force',
		'16' => 'Commenced',
		'17' => 'Overrun',
		'18' => 'Completed',
		'19' => 'Report Open',
		'20' => 'Report Closed',
		'21' => 'Recorded',
		'26' => 'Accepted - in Vault Submission',
	];

	return $activityStatuses[$status] ? $activityStatuses[$status] : 'Unknown status';
}
