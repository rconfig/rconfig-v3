<?php
/* this will retrieve previously saved queries on a per user basis - based on the userid */

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$q   = $db->q("SELECT snippet FROM snippets 
                WHERE id = " . $_GET['id'] );

$return_arr = array();
while ($row = mysql_fetch_assoc($q)) {
    $row_array['snippet']      = nl2br($row['snippet']);
    array_push($return_arr, $row_array);
}

echo json_encode($return_arr);
?> 