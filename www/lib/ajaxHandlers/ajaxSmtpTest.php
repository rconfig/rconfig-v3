<?php
// Send test mail from settings.php 'Test mail Server' button
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/phpmailer/class.phpmailer.php");
require_once("../../../config/config.inc.php");
$log = ADLog::getInstance();

$db2  = new db2();
$db2->query("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");
$row = $db2->single();
$smtpServerAddr    = $row['smtpServerAddr'];
$smtpFromAddr      = $row['smtpFromAddr'];
$smtpRecipientAddr = $row['smtpRecipientAddr'];
if ($row['smtpAuth'] == 1) {
    $smtpAuth     = $row['smtpAuth'];
    $smtpAuthUser = $row['smtpAuthUser'];
    $smtpAuthPass = $row['smtpAuthPass'];
}

$mail = new PHPMailer();
$body = 'Test mail from rConfig';
// next line per http://stackoverflow.com/questions/7706918/eregi-replace-data-what-does-this-line-do
$body = preg_replace("[\\\]",'',$body);

$mail->IsSMTP(); // telling the class to use SMTP
if ($row['smtpAuth'] == 1) {
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
    $db2->query("UPDATE settings SET smtpLastTest = 'Failed', smtpLastTestTime = NOW()");
    $db2->execute();
    $log->Fatal('Fatal: Test Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
    $response = json_encode(array(
        'failure' => true
    ));
} else {
    $q = $db2->query("UPDATE settings SET smtpLastTest = 'Passed', smtpLastTestTime = NOW()");
    $db2->execute();
    $log->Info('Info: Test Message sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
    $response = json_encode(array(
        'success' => true
    ));
}
// Clear all addresses and attachments for next loop
$mail->ClearAddresses();
$mail->ClearAttachments();

echo $response;