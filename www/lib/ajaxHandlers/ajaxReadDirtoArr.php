<?php

require_once('../../../config/functions.inc.php');

$path     = $_GET['path'];
$ext      = "*." . $_GET['ext'];
$fullpath = $path . $ext;
// echo $fullpath;

$output     = array();
$return_arr = array();
$array      = glob($fullpath);
usort($array, create_function('$b,$a', 'return filemtime($a) - filemtime($b);')); // sort by most recent modified

foreach ($array as $file) {
    $output['filename'] = basename($file);
    $output['filepath'] = $file;
    $output['filesize'] = _format_bytes(filesize($file)); // dir needs writeable to be set
    array_push($return_arr, $output);
}

echo json_encode($return_arr);

?>
