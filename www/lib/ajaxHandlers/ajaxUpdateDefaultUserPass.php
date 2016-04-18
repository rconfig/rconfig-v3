<?php
// used by settings.js to update the default username and password for NEW devices added to the database
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
$defaultNodeUsername = $_REQUEST['defaultNodeUsername'];
$defaultNodePassword = $_REQUEST['defaultNodePassword'];
$defaultNodeEnable = $_REQUEST['defaultNodeEnable'];

$db2  = new db2();
$db2->query("UPDATE settings SET
            defaultNodeUsername = :defaultNodeUsername, 
            defaultNodePassword = :defaultNodePassword,
            defaultNodeEnable =  :defaultNodeEnable
            WHERE id = 1");
$db2->bind(':defaultNodeUsername', $defaultNodeUsername); 
$db2->bind(':defaultNodePassword', $defaultNodePassword);   
$db2->bind(':defaultNodeEnable', $defaultNodeEnable); 
$queryResult = $db2->execute();

if($queryResult){
    $response = 'Success - Username & Password details saved';
} else {
    $response = 'Failed: on all nodes update from some reason, check ths logs - ajaxUpdateDefaultUserPass.php';
}
echo json_encode($response);