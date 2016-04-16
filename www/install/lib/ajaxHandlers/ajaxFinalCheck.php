<?php 
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// error_reporting(E_ALL);

$configFilePathInstalled = '/home/rconfig/config/config.inc.php';
include($configFilePathInstalled);
$sqlHost = DB_HOST.":".DB_PORT;
$array = array();

/* config.inc.php  file read check */
if(defined('DB_HOST') && defined('DB_USER')){
	$array['configFileMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';
} else {
	$array['configFileMsg'] = '<strong><font class="bad">Fail - Could not read config.inc.php</strong></font><br/>';
}

/* DB checks */
$link = mysql_connect($sqlHost, DB_USER, DB_PASSWORD);

if ($link) {
	$array['dbReadMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';

	mysql_select_db(DB_NAME, $link);
	if(! mysql_query("INSERT INTO categories (categoryName, status) VALUES ('testCat', 2)")) {
		$array['dbWriteMsg'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font><br/>';
	}
	$result = mysql_query("SELECT * FROM categories");
	while($row = mysql_fetch_array($result)) {
	  if($row['categoryName'] == 'testCat') {
		$dbWriteTest = 1;
	  }
	}
	if($dbWriteTest = 1){
		$array['dbWriteMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';
	} else {
		$array['dbWriteMsg'] = '<strong><font class="Bad">Could not write to Database</strong></font><br/>';
	}
	if(! mysql_query("DELETE FROM categories WHERE categoryName = 'testCat'")) {
		$array['dbWriteMsg'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font><br/>';
	}
	
} else {
	$array['dbReadMsg'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font><br/>';
}

mysql_close($link);

/* rConfig Application Directory file checks */

function dirRW ($directory, $msgTitle) {
	$fileName = $directory."testFile.txt";
	$text = 'someRandomText';
	$funcArr = array();
	if($fileHandle = fopen($fileName, 'w')){
		file_put_contents($fileName, $text);
		$funcArr[$msgTitle.'FileWriteMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';	
	} else {
		$funcArr[$msgTitle.'FileWriteMsg'] = '<strong><font class="bad">Fail - Could not write a file</strong></font><br/>';	
	}
	if(file_get_contents($fileName) == $text){
		$funcArr[$msgTitle.'FileReadMsg'] = '<strong><font class="Good">Pass</strong></font><br/>';	
	} else {
		$funcArr[$msgTitle.'FileReadMsg'] = '<strong><font class="bad">Fail - Could not read from file '.$fileName.'</strong></font><br/>';	
	}
	fclose($fileHandle);
	unlink($fileName);
	return $funcArr;
}

foreach (dirRW($config_data_basedir, 'app') as $k=>$v){
	$array[$k] = $v;
}
foreach (dirRW($config_backup_basedir, 'backup') as $k=>$v){
	$array[$k] = $v;
}
foreach (dirRW($config_temp_dir, 'tmp') as $k=>$v){
	$array[$k] = $v;
}

echo json_encode($array);

?>