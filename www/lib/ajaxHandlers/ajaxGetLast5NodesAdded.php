<?php
/* Gets last 5 added devices from devices tbl for dashboard.php */
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2  = new db2();
$log = ADLog::getInstance();
$q   = $db2->q("SELECT id, deviceName, deviceDateAdded, nodeAddedBy
        FROM nodes 
        WHERE status = 1
        ORDER BY deviceDateAdded DESC LIMIT 5");

echo json_encode($q);