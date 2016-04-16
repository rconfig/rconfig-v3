<?php

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");


$ldapServer = $_REQUEST['ldapServer'];

$q = "UPDATE settings SET
		ldapServer = '" . $ldapServer . "'
		WHERE id = 1";


$db = new db();
if($db->q($q)){
	$response = 'Success - LDAP server saved';
} else {
	$response = 'Failed:'.mysql_error();

}

echo json_encode($response);


?> 
