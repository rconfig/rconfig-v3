<?php

$path     = $_GET['path'];
$ext      = "*." . $_GET['ext'];
$fullpath = $path . $ext;

foreach (glob($fullpath) as $v) {
    unlink($v);
}

$fileCount = count(glob($path . '*.' . $ext));

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

?> 