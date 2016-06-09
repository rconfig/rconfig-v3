<?php

require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
// used by settings.js to update all nodes in DB with defaultCreds set to 1, to new username in Password entered in Settings.php
    session_start();
    require_once("../../../classes/db2.class.php");
    require_once("../../../classes/ADLog.class.php");
    require_once("../../../config/config.inc.php");
    $defaultNodeUsername = $_REQUEST['defaultNodeUsername'];
    $defaultNodePassword = $_REQUEST['defaultNodePassword'];
    $defaultNodeEnable = $_REQUEST['defaultNodeEnable'];
    $db2 = new db2();
    $db2->query("UPDATE nodes SET
            defaultNodeUsername = :defaultNodeUsername, 
            defaultNodePassword = :defaultNodePassword,
            defaultNodeEnable =  :defaultNodeEnable
            WHERE defaultCreds = 1");
    $db2->bind(':defaultNodeUsername', $defaultNodeUsername);
    $db2->bind(':defaultNodePassword', $defaultNodePassword);
    $db2->bind(':defaultNodeEnable', $defaultNodeEnable);
    $queryResult = $db2->execute();

    if ($queryResult) {
        $response = 'Success - Username & Password details saved';
    } else {
        $response = 'Failed: on all nodes update from some reason, check ths logs - ajaxUpdateDefaultUserPassNode.php';
    }
    echo json_encode($response);
}