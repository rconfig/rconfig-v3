<?php
//  used to retrive contents of file specified in JS in devicemgmt.php
$filepath = $_GET['path'];
if (file_exists($filepath)) {
    $fileArr = file($filepath);
} else {
	$fileArr = 'Failed';
}
echo json_encode($fileArr);