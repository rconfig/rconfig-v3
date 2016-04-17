<?php
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2  = new db2();
$log = ADLog::getInstance();
$q   = $db2->q("SELECT id, deviceName
        FROM nodes 
        WHERE nodeCatId  ='" . $_GET['catId'] . "' 
		AND status = 1
        ORDER BY deviceName ASC");

echo json_encode($q);