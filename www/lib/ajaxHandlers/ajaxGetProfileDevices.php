<?php

require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
    require_once("../../../classes/db2.class.php");
    require_once("../../../classes/ADLog.class.php");
    require_once("../../../config/config.inc.php");
    $db2 = new db2();
    $id = $_GET['id'];
    $db2->query("SELECT profileLocation FROM profiles WHERE id = :id");
    $db2->bind(':id', $id); //bind here and create wildcard search term here also
    $profileLocation = $db2->resultset();
    
    // shorten path as this is what is in the nodes DB
    $array = explode("/", $profileLocation[0]['profileLocation']);
    unset($array[0], $array[1], $array[2], $array[3]);
    $path = implode("/", $array);
    
    $db2->query("SELECT deviceName 
                FROM nodes
                WHERE profile = '".$path."'
                ORDER BY deviceName Asc");
    $devices = $db2->resultset();
    echo json_encode($devices);
}