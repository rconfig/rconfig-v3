<?php
error_reporting(0); // php errors supressed on this page because they should not interrupt the JSON response. i.e. if errors were made due to SQL errors etc.. JSON would not be processed by JS on the SettingsDB.php page
session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();

// set vars
$purgeDays = mysql_escape_string($_GET['purgeDays']);

// tasks: delete all files entries in DB by ID and then delete all Dirs

// 1. get all ids from DB older than X days
$getIDs = 'SELECT id FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL '.$purgeDays.' DAY) >= configDate';

if($getIDRes = $db->q($getIDs)){
	$log->Info("Info: Start manual ".$purgeDays." day Config File Purge - GET DB IDs(File: " . $_SERVER['PHP_SELF'] . ")");
} else {
	$response['response'] = "ERROR: executing query $getIDs";
	$log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
}

$iDsToPurgeArr = array();
while($iDsToPurgeArr[] = mysql_fetch_assoc($getIDRes));
array_pop($iDsToPurgeArr); // pop the last row off, which is an empty row

// create string list of IDs for later delete query
foreach ($iDsToPurgeArr as $k=>$vArr){
	foreach($vArr as $k1=>$v1){
		$iDlist .= $v1.', ';
	}
}
// remove trailing comma
$iDlist = substr($iDlist,0,-2);


// 2. get all dirs using a group by from DB older than X days
$getDirs = 'SELECT configLocation FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL '.$purgeDays.' DAY) >= configDate GROUP BY configLocation;';

if($getDirRes = $db->q($getDirs)){
	$log->Info("Info: Start manual ".$purgeDays." day Config File Purge - GET DIRs (File: " . $_SERVER['PHP_SELF'] . ")");
} else {
	$response['response'] = "ERROR: executing query ". $getDirs;
	$log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
}

$dirsToPurgeArr = array();
while($row = mysql_fetch_assoc($getDirRes)) {
	foreach ($row as $k=>$v){
		exec('rm -fr '.$v);
	}
}
// delete all empty Dirs under /home/rconfig/data/ for completeness
exec('find /home/rconfig/data/. -type d -empty -delete');

// 3. Delete all ids from DB older than X days - if these are delete first - step 2 cannot 
//    work as will not be able to get unique dirs older than X days
if (!empty($iDlist)){
	$delIdsQuery = 'DELETE FROM configs WHERE id IN ('.$iDlist.')';
					
	if($delIdsRes = $db->q($delIdsQuery)){
		$log->Info("Info: Start manual ".$purgeDays." day Config File Purge - Delete DB Rows (File: " . $_SERVER['PHP_SELF'] . ")");
		$response['response'] = "SUCCESS: ".$purgeDays." day Config File Purge Completed";
	} else {
		$response['response'] = "ERROR: executing query ". $delIdsQuery;
		$log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
	}
}

echo json_encode($response);
?>
