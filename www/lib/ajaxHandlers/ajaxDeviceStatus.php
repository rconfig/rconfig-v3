<?php 
// get the device status 
$deviceIpAddr = $_GET['deviceIpAddr'];
$connPort = $_GET['connPort'];

require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");
echo getHostStatus($deviceIpAddr, $connPort); //func in functions.inc.php;