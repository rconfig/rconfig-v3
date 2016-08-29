<?php
require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Someone tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
    
    $fileContent = $_POST['data'];
    $savePath = $_POST['filepath'];
    file_put_contents($savePath,$fileContent);
//    confirm and send data back
    $readAfterWrite = file_get_contents($savePath, true);
    
    if($readAfterWrite == $fileContent) {
        $response = json_encode(array('success' => true));
//        header('Location: ' . $_SERVER['HTTP_REFERER']);
    } else {
        $response = json_encode(array('failure' => true));
            $log->Warn("Connection profile write Issue");
        echo $response;
    }
    echo $response;
}  // end session check