<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

/* good complete implementation fo json for autocomplete here
http://stackoverflow.com/questions/4234455/jquery-autocomplete-not-working-with-json-data 
or
http://www.jensbits.com/2010/03/29/jquery-ui-autocomplete-widget-with-php-and-mysql/
*/
$db  = new db();
$log = ADLog::getInstance();
$q   = $db->q("SELECT *
        FROM devicemodelview 
        WHERE model LIKE '%" . mysql_real_escape_string($_GET['term']) . "%'");

$return_arr = array();
while ($row = mysql_fetch_assoc($q)) {
    $row_array['value'] = $row['model'];
    array_push($return_arr, $row_array);
}

echo json_encode($return_arr);
?> 