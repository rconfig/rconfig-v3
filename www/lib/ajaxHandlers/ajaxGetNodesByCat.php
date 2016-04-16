<?php
/* this will retrieve previously saved queries on a per user basis - based on the userid */

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

/* good complete implementation for json for autocomplete here
http://stackoverflow.com/questions/4234455/jquery-autocomplete-not-working-with-json-data 
or
http://www.jensbits.com/2010/03/29/jquery-ui-autocomplete-widget-with-php-and-mysql/
*/
$db  = new db();
$log = ADLog::getInstance();
$q   = $db->q("SELECT id, deviceName
        FROM nodes 
        WHERE nodeCatId  ='" . $_GET['catId'] . "' 
		AND status = 1
        ORDER BY deviceName ASC");

$return_arr = array();
while ($row = mysql_fetch_assoc($q)) {
    $row_array['id']         = $row['id'];
    $row_array['deviceName'] = $row['deviceName'];
    array_push($return_arr, $row_array);
}

echo json_encode($return_arr);

?> 