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
$installDir = $_GET['installDir'];
$sqlHost = $server.":".$port;
$dbFile = '../../rconfig.sql';
$configFilePathOriginal = '/home/rconfig/www/install/config.inc.php.template';
$configFilePathInstalled = '/home/rconfig/config/config.inc.php';
include
$array = array();

$link = mysql_connect($sqlHost, $dbUsername, $dbPassword);

if ($link) {
	if (mysql_query("CREATE DATABASE $dbName",$link)){
		if(mysql_select_db($dbName, $link)) {
			
			// rewrite the 'DATABASE_NAME' tage from the SQL file into memory
			$templateFile = file_get_contents($dbFile);

			// do tag replacements or whatever you want
			$templateFile = str_replace('DATABASE_NAME', $dbName, $templateFile);

			$sqlArray = explode(';',$templateFile);
			$sqlErrorCode = '';
			$sqlErrorText = '';
			foreach ($sqlArray as $stmt) {
			  if (strlen($stmt)>3 && substr(ltrim($stmt),0,2)!='/*') {
				$result = mysql_query($stmt);
				if (!$result) {
				  $sqlErrorCode = mysql_errno();
				  $sqlErrorText = mysql_error();
				  $sqlStmt = $stmt;
				  break;
				}
			  }
			}
			
			/* Add details to /includes/config.inc.php file */
			$configFile = file_get_contents($configFilePathOriginal);
			// re-write config file in memory
			$configFile = str_replace('_DATABASEHOST', $server, $configFile);
			$configFile = str_replace('_DATABASEPORT', $port, $configFile);
			$configFile = str_replace('_DATABASENAME', $dbName, $configFile);
			$configFile = str_replace('_DATABASEUSERNAME', $dbUsername, $configFile);
			$configFile = str_replace('_DATABASEPASSWORD', $dbPassword, $configFile);
			$configFile = str_replace('_SITEURL', $siteUrl, $configFile);
			$configFile = str_replace('_INSTALLDIR', $installDir, $configFile);
			
			chmod($configFilePath, 0777);
			file_put_contents($configFilePathInstalled, $configFile);
			chmod($configFilePath, 0644);

			if ($sqlErrorCode != 0) {
			  $array['error'] =  'An error occured during installation!<br/>';
			  $array['error'] =  'Error code: $sqlErrorCode<br/>';
			  $array['error'] =  'Error text: $sqlErrorText<br/>';
			  $array['error'] =  'Statement:<br/> $sqlStmt<br/>';
			} else {
			$array['success'] = '<strong><font class="Good">rConfig database installed successfully</strong></font><br/>';
			}
			
		} else {
			$array['error'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font><br/>';
			mysql_query("DROP DATABASE $dbName",$link);
		}
	} else {
		$array['error'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font><br/>';
	}
} else {
	$array['error'] = '<strong><font class="bad">Fail - ' . mysql_error().': '.mysql_errno().'</strong></font><br/>';
}

mysql_close($link);





echo json_encode($array);

?>