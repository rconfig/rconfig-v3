<?php

require('../../../classes/compareClass.php');
require('../../../config/functions.inc.php');

// simple script runtime check 
$Start = getTime();

session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2 = new db2();
$log = ADLog::getInstance();

// Validate and set full path vars
$verified = TRUE;
foreach ($_GET as $v) {
    if (!isset($v) || empty($v)) {
        $verified = FALSE;
    }
}

if (!$verified) {
    echo "<font color=\"red\">*</font> You must select all items with an asterisk"; // put in session to break and return error to script here
    exit();
} else {
    // initialise Vars
    $path_a = $_GET['path_a'];
    $path_b = $_GET['path_b'];
    $linepadding = $_GET['linepadding'];
}

$diff = new diff;
if ($linepadding >= 1 && $linepadding <= 99) { //if linepadding var is number and is between 1 and 99
    $text = $diff->inline($path_a, $path_b, $linepadding);
} else {
    $text = $diff->inline($path_a, $path_b);
}

echo count($diff->changes) . ' changes';
echo $text; // echo output
// Print time taken script
$End = getTime();
echo "<div class=\"spacer\"></div>";
echo "<Strong>Time taken = " . number_format(($End - $Start), 2) . " secs </strong>";