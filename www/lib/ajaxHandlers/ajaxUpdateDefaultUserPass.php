<?php

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");



$defaultNodeUsername = $_REQUEST['defaultNodeUsername'];
$defaultNodePassword = $_REQUEST['defaultNodePassword'];
$defaultNodeEnable = $_REQUEST['defaultNodeEnable'];

$q = "UPDATE settings SET
		defaultNodeUsername = '" . $defaultNodeUsername . "', 
		defaultNodePassword = '" . $defaultNodePassword . "',
		defaultNodeEnable =  '" . $defaultNodeEnable . "'
		WHERE id = 1";


$db = new db();
if($db->q($q)){
	$response = 'Success - Username & Password details saved';
} else {
	$response = 'Failed:'.mysql_error();

}

echo json_encode($response);


?> 
