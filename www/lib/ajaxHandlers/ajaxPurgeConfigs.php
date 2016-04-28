<?php
// loaded from www\js\configoverview.js. 
// php errors supressed on this page because they should not interrupt the JSON response. 
//i.e. if errors were made due to SQL errors etc.. JSON would not be processed by JS on the SettingsDB.php page
error_reporting(0);
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$log = ADLog::getInstance();
// set vars
$purgeDays = $_GET['purgeDays'];

// tasks: delete all files entries in DB by ID and then delete all Dirs
// 1. get all ids from DB older than X days
$db2  = new db2();
$db2->query("SELECT id FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL :purgeDays DAY) >= configDate");
$db2->bind(':purgeDays', $purgeDays); //bind here and create wildcard search term here also
$getIDs = $db2->resultsetCols();
// logging below
if($getIDs){
    $log->Info("Info: Start manual ".$purgeDays." day Config File Purge - GET DB IDs(File: " . $_SERVER['PHP_SELF'] . ")");
} else {
    $response['response'] = "ERROR: executing query $getIDs";
    $log->Fatal("Fatal: Could not Get GET DB IDs (File: " . $_SERVER['PHP_SELF'] . ")");
}
$iDlist = implode (", ", $getIDs);

// 2. get all dirs using a group by from DB older than X days
//$getDirs = 'SELECT configLocation FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL '.$purgeDays.' DAY) >= configDate GROUP BY configLocation;';
$db2->query("SELECT configLocation FROM configs WHERE DATE_SUB(CURDATE(),INTERVAL :purgeDays DAY) >= configDate GROUP BY configLocation;");
$db2->bind(':purgeDays', $purgeDays); //bind here and create wildcard search term here also
$getDirRes = $db2->resultsetCols();
if($executeRes){
    $log->Info("Info: Start manual ".$purgeDays." day Config File Purge - GET DIRs (File: " . $_SERVER['PHP_SELF'] . ")");
} else {
    $response['response'] = "ERROR: executing query ". $getDirs;
    $log->Fatal("Fatal: Could not Config File Purge - GET DIRs (File: " . $_SERVER['PHP_SELF'] . ")");
}
// physically remove directories
$dirsToPurgeArr = array();
while($row = $getDirRes) {
    foreach ($row as $k=>$v){
        exec('rm -fr '.$v);
    }
}
// delete all empty Dirs under /home/rconfig/data/ for completeness
exec('find /home/rconfig/data/. -type d -empty -delete');

// 3. Delete all ids from DB older than X days - if these are delete first - step 2 cannot 
//    work as will not be able to get unique dirs older than X days
if (!empty($iDlist)){          					
    $db2->query("DELETE FROM configs WHERE id IN ($iDlist)");
    $executeRes = $db2->execute();
if($executeRes){
        $log->Info("Info: Start manual ".$purgeDays." day Config File Purge - Delete DB Rows (File: " . $_SERVER['PHP_SELF'] . ")");
        $response['response'] = "SUCCESS: ".$purgeDays." day Config File Purge Completed";
    } else {
        $response['response'] = "ERROR: executing query Delete ID";
        $log->Fatal("Fatal: Could not Config File Purge - Delete DB Rows (File: " . $_SERVER['PHP_SELF'] . ")");
    }
}

echo json_encode($response);