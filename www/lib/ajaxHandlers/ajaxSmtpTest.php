<?php

require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/phpmailer/class.phpmailer.php");
require_once("../../../config/config.inc.php");


$log = ADLog::getInstance();

$db = new db();
$q  = $db->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");

$result            = mysql_fetch_assoc($q);
$smtpServerAddr    = $result['smtpServerAddr'];
$smtpFromAddr      = $result['smtpFromAddr'];
$smtpRecipientAddr = $result['smtpRecipientAddr'];
if ($result['smtpAuth'] == 1) {
    $smtpAuth     = $result['smtpAuth'];
    $smtpAuthUser = $result['smtpAuthUser'];
    $smtpAuthPass = $result['smtpAuthPass'];
}


$mail = new PHPMailer();
// $body = file_get_contents('contents.html');
$body = 'Test mail from rConfig';
$body = eregi_replace("[\]", '', $body);

$mail->IsSMTP(); // telling the class to use SMTP
if ($result['smtpAuth'] == 1) {
    $mail->SMTPAuth = true; // enable SMTP authentication
    $mail->Username = $smtpAuthUser; // SMTP account username	
    $mail->Password = $smtpAuthPass; // SMTP account password
}

$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
$mail->Host          = $smtpServerAddr; // sets the SMTP server
$mail->Port          = 25; // set the SMTP port for the SMTP server

$mail->SetFrom($smtpFromAddr, 'Admin@rConfig');
// $mail->AddReplyTo('list@mydomain.com', 'List manager');

$mail->Subject = "PHPMailer Test Subject via smtp, basic with authentication";
$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);

$smtpRecipientAddresses = explode("; ", $smtpRecipientAddr);

foreach ($smtpRecipientAddresses as $emailAddr) {
    $mail->AddAddress($emailAddr);
}
// $mail->AddStringAttachment($row["photo"], "YourPhoto.jpg");

if (!$mail->Send()) {
    $q = $db->q("UPDATE settings SET smtpLastTest = 'Failed', smtpLastTestTime = NOW()");
    $log->Fatal('Fatal: Test Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
    $response = json_encode(array(
        'failure' => true
    ));
} else {
    $q = $db->q("UPDATE settings SET smtpLastTest = 'Passed', smtpLastTestTime = NOW()");
    $log->Info('Info: Test Message sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
    $response = json_encode(array(
        'success' => true
    ));
}
// Clear all addresses and attachments for next loop
$mail->ClearAddresses();
$mail->ClearAttachments();

echo $response;

?>