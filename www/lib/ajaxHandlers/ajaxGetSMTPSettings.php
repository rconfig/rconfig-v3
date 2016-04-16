<?php

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db = new db();
$q  = $db->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass, smtpLastTest, smtpLastTestTime  FROM settings  WHERE id = 1");

$return_arr = array();
while ($row = mysql_fetch_assoc($q)) {
    $row_array['smtpServerAddr']    = $row['smtpServerAddr'];
    $row_array['smtpFromAddr']      = $row['smtpFromAddr'];
    $row_array['smtpRecipientAddr'] = $row['smtpRecipientAddr'];
    if ($row['smtpAuth'] == 1) {
        $row_array['smtpAuth']     = $row['smtpAuth'];
        $row_array['smtpAuthUser'] = $row['smtpAuthUser'];
        $row_array['smtpAuthPass'] = $row['smtpAuthPass'];
    }
    $row_array['smtpLastTest']     = $row['smtpLastTest'];
    $row_array['smtpLastTestTime'] = $row['smtpLastTestTime'];
    
    
    array_push($return_arr, $row_array);
}

echo json_encode($return_arr);


?> 