<?php 
// ini_set('display_errors', 1);
// ini_set('log_errors', 1);
// error_reporting(E_ALL);

$server = $_GET['server'];
$port = $_GET['port'];
$dbName = $_GET['dbName'];
$dbUsername = $_GET['dbUsername'];
$dbPassword = $_GET['dbPassword'];
$siteUrl = $_GET['siteUrl'];
$url = "http://".$_SERVER['SERVER_NAME'];

$installDir = $_GET['installDir'];
$sqlHost = $server.":".$port;
$array = array();

// chech server connectivity
$handle = fsockopen($server, $port); 

if ($handle) {
	$array['connTest'] = '<strong><font class="good">Pass</strong> </font>';
} else {
	$array['connTest'] = '<strong><font class="bad">Fail - Cannot connect to '.$server.':'.$port.'</strong></font>';
}
fclose($handle);


// check Username/Password 

$link = mysql_connect($sqlHost, $dbUsername, $dbPassword);

if ($link) {
	$array['credTest'] = '<strong><font class="good">Pass</strong></font>';
} else {
	$array['credTest'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font>';
}


// check if DB exists
$db_selected = mysql_select_db($dbName, $link);

if ($db_selected) {
	$array['dbTest'] = '<strong><font class="bad">Fail - Database already installed</strong></font>';
} else {
	if (mysql_errno() == '1049') {
		$array['dbTest'] = '<strong><font class="good">Pass - not in use</strong></font>';
	} else {
		$array['dbTest'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font>';
	}
}

mysql_close($link);

if($siteUrl == $url) {
	$array['siteUrl'] = '<strong><font class="good">Pass</strong></font>';
} else {
	$array['siteUrl'] = '<strong><font class="bad">Fail - Something went wrong with the SiteURL Test!</strong></font>';
}


if($installDir == $_SERVER['DOCUMENT_ROOT']) {
	$array['installDir'] = '<strong><font class="good">Pass</strong></font>';
} else {
	$array['installDir'] = '<strong><font class="bad">Fail - Something went wrong!</strong></font>';
}


echo json_encode($array);

?>