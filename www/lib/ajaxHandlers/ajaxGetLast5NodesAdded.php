<?php
/* this will retrieve previously saved queries on a per user basis - based on the userid */
error_reporting(E_ALL);
ini_set('display_errors', '1');
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
?> 
