<?php
// check HTTP version and return to install pages
function find_HTTPD_Version()
{
    if (!function_exists('apache_get_version')) {
        if (!isset($_SERVER['SERVER_SOFTWARE']) || strlen($_SERVER['SERVER_SOFTWARE']) == 0) {
            return false;
        }
        return (float) filter_var($_SERVER["SERVER_SOFTWARE"], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    } else {
        $output = apache_get_version();
        preg_match('@[0-9]+\.[0-9]+\.[0-9]+@', $output, $version);
        return $version[0];
    }

}

if (find_HTTPD_Version() >= 2.2) {
    $response = '<strong><font class="good">Yes</font></strong>';
} else {
    $response = '<strong><font class="bad">No</font></strong>';
}
echo json_encode($response);
