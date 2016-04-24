<?php 
include("../../../config/config.inc.php");
ob_start(); // begin collecting output
$passedRid = $_GET['rid'];
$passedSnipId = $_GET['snipId'];
$passedUsername = $_GET['username'];
$passedPassword = $_GET['password'];
include($config_app_basedir.'lib/configDeviceScript.php');
$result = ob_get_clean(); // retrieve output from myfile.php, stop buffering
echo $result;