<?php
// will backup syslog files - called from settingsBackup.js 
include("../../../config/config.inc.php");
include("../../../config/functions.inc.php");
include("../../../classes/db2.class.php");

$db2  = new db2();
// check and set timeZone to avoid PHP errors
$q = $db2->query("SELECT timeZone FROM settings");
$row = $db2->single();
$timeZone = $row['timeZone'];
date_default_timezone_set($timeZone);

$today = date("Ymd");

/**
 * Create Logs backup and ZIP it to tmp dir
 */
$backupFile = $config_syslogBackup_basedir . 'syslogbackup-' . $today . '.zip';
touch($backupFile);
folderBackup($config_log_basedir, $backupFile);

if (file_exists($backupFile)) {
    $response = json_encode(array('success' => true));
} else {
    $response = json_encode(array('failure' => true));
}

echo $response;