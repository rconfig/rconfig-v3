<?php
// Get Devices models from the devicemodelview based on $_GET['term'] for Ajax output on devices.php
require_once("../../../classes/db2.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$deviceId = $_GET['deviceId'];
$command = $_GET['command'];
$db2->query("SELECT configDate FROM configs 
                WHERE deviceid = :deviceId
                AND configFilename LIKE '$command%'");
$db2->bind(':deviceId', $deviceId); //bind here and create wildcard search term here also
$rows = $db2->resultsetCols();
// remove duplicates 
$uniqueResults = array_unique($rows);
$newDateFormatArray =  array();
foreach ($uniqueResults as $k=>$v){
    $newDate = date("n-d-Y", strtotime($v));
    $newDateFormatArray[] = $newDate;
}
echo json_encode($newDateFormatArray);