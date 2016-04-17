<?php
// Get Devices models from the devicemodelview based on $_GET['term'] for Ajax output on devices.php
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$log = ADLog::getInstance();
$q   = $db2->q("SELECT model AS value
        FROM devicemodelview 
        WHERE model LIKE '%" . $_GET['term'] . "%'");
echo json_encode($q);