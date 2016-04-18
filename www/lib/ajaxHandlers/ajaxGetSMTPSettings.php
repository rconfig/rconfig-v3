<?php
// Get SMTP settings from DB for settings.php
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$term = $_GET['term'];
$db2->query("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass, smtpLastTest, smtpLastTestTime  FROM settings  WHERE id = 1");
$rows = $db2->resultset();
echo json_encode($rows);