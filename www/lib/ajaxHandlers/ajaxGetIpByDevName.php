<?php

$hostname = $_GET['hostname'];
$ip = gethostbyname($hostname);

if(filter_var($ip, FILTER_VALIDATE_IP)) {
	$response = $ip;
} else {
	$response = '';
}

echo json_encode($response);
?>