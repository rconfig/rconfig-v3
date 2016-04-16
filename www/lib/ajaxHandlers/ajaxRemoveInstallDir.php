<?php

include_once('/home/rconfig/config/functions.inc.php');

$dir = '/home/rconfig/www/install/';

if(is_dir($dir)){
	// system('/bin/rm -rf ' . $dir); //delete dir
	rrmdir($dir);
	sleep(1);
	
	if(!file_exists($dir)){ // check if install  does not dir exist after delete and return success
		$response = 'success';
	
	} else if (file_exists($dir)) { // else return failure as dir still exists
		$response = 'failure';

	}
} else if (!is_dir($dir)) { // first if - return success as dir does not exist
	$response = 'success';

}
echo json_encode($response); 
?>