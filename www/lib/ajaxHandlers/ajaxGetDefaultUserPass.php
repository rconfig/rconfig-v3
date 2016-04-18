<?php
// Gets default device username and password from DB
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$db2->query("SELECT defaultNodeUsername, defaultNodePassword, defaultNodeEnable FROM settings WHERE id = 1");
//$db2->debugDumpParams();
$rows = $db2->resultset();
echo json_encode($rows);