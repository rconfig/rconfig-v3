<?php
// Gets OS level DNS Settings fro display on dashboard.php
$resolv = "/etc/resolv.conf";
$file_handle = fopen($resolv, "r");
while (!feof($file_handle)) {
    $line = fgets($file_handle);
    if (strstr($line, "nameserver") || strstr($line, "search")) {
        $line     = sscanf($line, "%s %s", $tag, $value);
        // echo $value;
        $dnsArr[] = $value;
    }
}
fclose($file_handle);
$result = array();
foreach ($dnsArr as $k => $v) {
    array_push($result, $v);
}
return implode(", ", $result); 