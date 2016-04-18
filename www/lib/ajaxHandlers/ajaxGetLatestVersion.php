<?php
// Check rconfig.com/downloads/version.txt for latest publish release
require('/home/rconfig/config/config.inc.php');
require('/home/rconfig/classes/ADLog.class.php');

if (!$sock = @fsockopen('www.rconfig.com', 80, $num, $error, 5)) {
    // declare Logging Class
    $log = ADLog::getInstance();
    $log->logDir = $config_app_basedir."logs/";
    $log->Fatal("Error: Unable to determine latest available update from rConfig.com as no internet access from server -  (File: ".$_SERVER['PHP_SELF'].")");

    $response = json_encode(array(
    'failure' => true
    ));
} else {
    //Setting the timeout properly without messing with ini values: 
    $ctx        = stream_context_create(array(
        'http' => array(
        'timeout' => 5
        )
    ));
	
    $currentVer = $config_version;
    $latestVer = file_get_contents("http://www.rconfig.com/downloads/version.txt", 0, $ctx);
    // default is failure i.e. version online is NTO higher than installed version
    $response = json_encode(array(
    'failure' => true
    ));
    if(!empty($currentVer) && !empty($latestVer)) {
            if ($latestVer > $currentVer){
                    // success means version online is higher than installed version
                    $response = json_encode(array(
                    'success' => true
                    ));		
            }	
    }
}
echo $response;