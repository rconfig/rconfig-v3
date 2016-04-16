<?php

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db = new db();
$q  = $db->q("SELECT defaultNodeUsername, defaultNodePassword, defaultNodeEnable FROM settings WHERE id = 1");

$return_arr = array();
while ($row = mysql_fetch_assoc($q)) {
    $row_array['defaultNodeUsername']    = $row['defaultNodeUsername'];
    $row_array['defaultNodePassword']      = $row['defaultNodePassword'];
    $row_array['defaultNodeEnable'] = $row['defaultNodeEnable'];    
    
    array_push($return_arr, $row_array);
}

echo json_encode($return_arr);


?> 