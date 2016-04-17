<?php
//php errors supressed on this pagre beacuse they should not interupt the JSON repsonse. 
// i.e. if errors were made due to SQL errors etc.. JSOn would not be processed by JS on the SettingsDB.php page
error_reporting(0); 
session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();

// query DB for all tables
if(defined('DB_NAME')){
	$tblsSql = $db->q("SHOW TABLES FROM " . DB_NAME);
} else {
	$response['response'] = "ERROR: executing query";
	$log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
}

// set and fill returnTbls Array
$returnTbls = array();
while ($row = mysql_fetch_assoc($tblsSql)) {
    array_push($returnTbls, $row);
}

// flatten array and get tbl names
$response = array(); // set $$response array
foreach ($returnTbls as $returnK) {
    foreach ($returnK as $key => $tbl) {
        // get list of columns for each table
        $columnSql = $db->q("SHOW COLUMNS FROM " . $tbl);
        
        // loop over the columns SQL 
        while ($row = mysql_fetch_assoc($columnSql)) {
            // if status column is present, do next query
            if ($row['Field'] == 'status') {
                // get list of rows for each table where status = '2' deleted
                if ($db->q("DELETE FROM " . $tbl . " WHERE status = 2")) {
                    $response['response'] = "SUCCESS: Deleted items purged";
                    $log->Info("Info: Deleted rows purged from Table:" . $tbl . "  (File: " . $_SERVER['PHP_SELF'] . ")");
                } else {
                    $response['response'] = "ERROR: Could not delete rows. Errors logged to log file";
                    $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                }
                
            } // if
        } // while
    } // 2nd foreach
} // 1st foreach

echo json_encode($response);
?>