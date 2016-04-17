<?php
/* this will retrieve previously saved queries on a per user basis - based on the userid */

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
$db = new db();
$q  = $db->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass, smtpLastTest FROM settings");
$return_arr = array();
while ($row = mysql_fetch_assoc($q)) {
    $row_array['configDate'] = $row['configDate'];
    array_push($return_arr, $row_array);
}

echo json_encode($return_arr);

?>