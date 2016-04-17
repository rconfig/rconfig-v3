<?php
// gethostbyname — Get the IPv4 address corresponding to a given Internet host name
// validate returned IP address and send the IP back to calling JS Script
$hostname = $_GET['hostname'];
$ip = gethostbyname($hostname);
if(filter_var($ip, FILTER_VALIDATE_IP)) {
	$response = $ip;
} else {
	$response = '';
}
echo json_encode($response);