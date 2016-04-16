<?php
include("../../../config/config.inc.php");

if(defined('WEB_DIR')){
	if(is_dir(WEB_DIR.'/install')){
            $response = json_encode(array(
                'result' => 'present'
            ));
	} else {
			$response = json_encode(array(
                'result' => 'notpresent'
            ));
	}
} else {
 echo 'WEB_DIR not defined';
}

echo $response;

?>