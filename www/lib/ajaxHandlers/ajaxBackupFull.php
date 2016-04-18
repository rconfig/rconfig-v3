<?php
include("../../../config/config.inc.php");
include("../../../config/functions.inc.php");
include("../../../classes/db.class.php");

$db2  = new db2();
// check and set timeZone to avoid PHP errors
$q = $db2->query("SELECT timeZone FROM settings");
$row = $db2->single();
$timeZone = $row['timeZone'];
date_default_timezone_set($timeZone);

$today = date("Ymd");

// get each dir in /home/rconfig except, backups, tmp, . and ..
$dirsToBackup = array_diff(scandir($config_app_basedir), array('..', '.', 'backups', 'tmp'));

// then zip each folder to a zip file in the tmp dir
foreach ($dirsToBackup as $k=>$v) {
	/**
	 * Then create dir backup and ZIP it
	 */
	$backupFile = $config_temp_dir . 'backup-'.$v.'-' . $today . '.zip';
	folderBackup('/home/rconfig/'.$v, $backupFile);
}

/**
 *  create SQL backup 
 */
//  backup MySQL DB - vars from ../../../config/config.inc.php
$sqlBackupFile = sqlBackup(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, $config_temp_dir);


/**
 * Then Zip all  zip files into one
 */

$fullBackupFile = $config_backup_basedir . 'fullBackup-' . $today . '.zip';

/**
 * create array of filenames in /home/tmp dir
 */

$dirhandler = opendir($config_temp_dir);
$nofiles    = 0;
while ($file = readdir($dirhandler)) {
	// if $file isn't this directory or its parent 
	// add to the $file_names array
	if ($file != '.' && $file != '..') {
		$nofiles++;
		$file_names[$nofiles] = $config_temp_dir . $file;
	}
}

//close the handler
closedir($dirhandler);

/**
 * add all.zip files (array from above readdir) in /home/tmp to zip and create zip in /home/backups
 * below is a private function in functions file
 */
createZip($file_names, $fullBackupFile, true);

/**
 * delete all .zip files in /home/rconfig/tmp
 */

foreach ($file_names as $file) {
	unlink($file);
}


if (file_exists($fullBackupFile)) {
    $response = json_encode(array('success' => true));
} else {
    $response = json_encode(array('failure' => true));
}

echo $response;

?>