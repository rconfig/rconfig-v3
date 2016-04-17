<?php
// Get SMTP settings from DB for settings.php
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2 = new db2();
$q  = $db2->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass, smtpLastTest, smtpLastTestTime  FROM settings  WHERE id = 1");

echo json_encode($q);