<?php
include("../../../config/config.inc.php");
include("../../../config/functions.inc.php");
include("../../../classes/db.class.php");

$db  = new db();
// check and set timeZone to avoid PHP errors
$q      = $db->q("SELECT timeZone FROM settings");
$result = mysql_fetch_assoc($q);
$timeZone = $result['timeZone'];
date_default_timezone_set($timeZone);

$today = date("Ymd");

/**
 * Create Logs backup and ZIP it to tmp dir
 */
$backupFile = $config_syslogBackup_basedir . 'syslogbackup-' . $today . '.zip';
touch($backupFile );
folderBackup($config_log_basedir, $backupFile);

if (file_exists($backupFile)) {
    $response = json_encode(array('success' => true));
} else {
    $response = json_encode(array('failure' => true));
}

echo $response;

?>