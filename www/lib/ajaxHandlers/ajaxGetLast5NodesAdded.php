<?php
/* Gets last 5 added devices from devices tbl for dashboard.php */
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$q   = $db2->query("SELECT id, deviceName, deviceDateAdded, nodeAddedBy
        FROM nodes 
        WHERE status = 1
        ORDER BY deviceDateAdded DESC LIMIT 5");
//$db2->debugDumpParams();
$rows = $db2->resultset();
echo json_encode($rows);