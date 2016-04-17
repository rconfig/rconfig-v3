<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Send test mail from settings.php 'Test mail Server' button
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/phpmailer/class.phpmailer.php");
require_once("../../../config/config.inc.php");
$log = ADLog::getInstance();

$db2 = new db2();
$q = $db2->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");
$smtpServerAddr    = $q[0]['smtpServerAddr'];
$smtpFromAddr      = $q[0]['smtpFromAddr'];
$smtpRecipientAddr = $q[0]['smtpRecipientAddr'];
if ($q[0]['smtpAuth'] == 1) {
    $smtpAuth     = $q[0]['smtpAuth'];
    $smtpAuthUser = $q[0]['smtpAuthUser'];
    $smtpAuthPass = $q[0]['smtpAuthPass'];
}

$mail = new PHPMailer();
$body = 'Test mail from rConfig';
// next line per http://stackoverflow.com/questions/7706918/eregi-replace-data-what-does-this-line-do
$body = preg_replace("[\\\]",'',$body);

$mail->IsSMTP(); // telling the class to use SMTP
if ($q[0]['smtpAuth'] == 1) {
    $mail->SMTPAuth = true; // enable SMTP authentication
    $mail->Username = $smtpAuthUser; // SMTP account username	
    $mail->Password = $smtpAuthPass; // SMTP account password
}

$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
$mail->Host = $smtpServerAddr; // sets the SMTP server
$mail->Port = 25; // set the SMTP port for the SMTP server

$mail->SetFrom($smtpFromAddr, 'Admin@rConfig.com');
$mail->Subject = "PHPMailer Test Subject via smtp, basic with authentication";
$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);

$smtpRecipientAddresses = explode("; ", $smtpRecipientAddr);

foreach ($smtpRecipientAddresses as $emailAddr) {
    $mail->AddAddress($emailAddr);
}
if (!$mail->Send()) {
    $db2->update("UPDATE settings SET smtpLastTest = 'Failed', smtpLastTestTime = NOW()");
    $log->Fatal('Fatal: Test Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
    $response = json_encode(array(
        'failure' => true
    ));
} else {
    $q = $db2->update("UPDATE settings SET smtpLastTest = 'Passed', smtpLastTestTime = NOW()");
    $log->Info('Info: Test Message sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
    $response = json_encode(array(
        'success' => true
    ));
}
// Clear all addresses and attachments for next loop
$mail->ClearAddresses();
$mail->ClearAttachments();

echo $response;