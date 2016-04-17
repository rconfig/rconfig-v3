<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Get actual Public/ Natted IP for rConfig instance
//Setting the timeout properly without messing with ini values: 
$ctx        = stream_context_create(array(
    'http' => array(
        'timeout' => 5
    )
));
$NoIp       = "<font color=\"red\">Public IP Address Not Available</font>";
$notConnect = "<font color=\"red\">Could Not Connect to Remote Server</font>";

if (!$sock = @fsockopen('www.rconfig.com', 80, $num, $error, 5)) {
    // test can we connect to website
    $response = json_encode($notConnect);
    file_put_contents("publicIp.txt", $notConnect);
} else {
    if ($ip = file_get_contents("http://www.rconfig.com/ip.php", 0, $ctx)) {
        if (filter_var($ip, FILTER_VALIDATE_IP)) {
            // test if what is returned is actually an IP address
            file_put_contents("publicIp.txt", $ip);
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $response = json_encode(array(
                'failure' => true
            ));
            file_put_contents("publicIp.txt", $NoIp);
        } 
    }
}

echo $response;