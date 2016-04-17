<?php
// Gets default device username and password from DB
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2 = new db2();
$q  = $db2->q("SELECT defaultNodeUsername, defaultNodePassword, defaultNodeEnable FROM settings WHERE id = 1");

echo json_encode($q);