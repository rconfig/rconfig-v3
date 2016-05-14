<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

//archive logs files
$mainPath        = $_GET['path'];
$archiveMainPath = $mainPath . "archive/";
$ext             = "*." . $_GET['ext'];
$fullpath        = $mainPath . $ext;
// create and archive dir if not already created
if (!is_dir($archiveMainPath)) {
    mkdir("$archiveMainPath");
}
$today = date("Ymd");
$commandString = "sudo -u apache zip -r -j " . $archiveMainPath . "filename" . $today . ".zip " . $mainPath . $ext;
exec($commandString);
foreach (glob($fullpath) as $v) {
    unlink($v);
}

$fileCount = count(glob($mainPath . $ext));

if ($fileCount > 0) {
    $response = json_encode(array(
        'failure' => true
    ));
} else {
    $response = json_encode(array(
        'success' => true
    ));
}

echo $response;
