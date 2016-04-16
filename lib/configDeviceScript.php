<?php
/*
 * Why I am creating a new downloadNowScript when a lot of the code is already in the showCmdScript already?
 * well, most of the code is similar for sure, but the functionality between the features is different, where
 * the showCmdScript is based of the Task ID (tid) and this script is based off the routers own DB ID. 
 * I will also remove the reporting elements as they appear in the showCmdScript script.
 *
*/

// requires - full path required
require("/home/rconfig/classes/db.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
require('/home/rconfig/classes/sshlib/Net/SSH2.php'); // this will be used in connection.class.php 
require("/home/rconfig/classes/connection.class.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
require("/home/rconfig/classes/reportTemplate.class.php");
require("/home/rconfig/classes/phpmailer/class.phpmailer.php");
require_once("/home/rconfig/config/config.inc.php");
require_once("/home/rconfig/config/functions.inc.php");

// declare DB Class
$db = new db();

// check and set timeZone
$q      = $db->q("SELECT timeZone FROM settings");
$result = mysql_fetch_assoc($q);
$timeZone = $result['timeZone'];
date_default_timezone_set($timeZone);

// declare Logging Class
$log = ADLog::getInstance();
$log->logDir = $config_app_basedir."logs/";

// create array for json output to return to snippet window
$jsonArray = array();

// check if this script was CLI Invoked and throw an error to the CLI if it was.
if (php_sapi_name() == 'cli') {  // if invoked from CLI
	$text = "You are not allowed to invoke this script from the CLI - unable to run script";
	echo $text."\n";
	$log->Fatal("Error: ".$text." (File: ".$_SERVER['PHP_SELF'].")");
	die();
} 

// set vars passed from ajaxConfigDevice.php on require()
$rid = $passedRid;
$snipId = $passedSnipId;
$providedUsername = $passedUsername;
$providedPassword = $passedPassword;

// Log the script start
$log->Info("The ".$_SERVER['PHP_SELF']." script was run manually invoked with Router ID: $rid & Snippet ID: $snipId "); // logg to file

// get time-out setting from DB
$timeoutSql = $db->q("SELECT deviceConnectionTimout FROM settings");
$result = mysql_fetch_assoc($timeoutSql);
$timeout = $result['deviceConnectionTimout'];

// Get active nodes for a given task ID
// Query to retrieve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$getNodesSql = "SELECT 
					id, 
					deviceName, 
					deviceIpAddr, 
					devicePrompt, 
					deviceUsername, 
					devicePassword, 
					deviceEnableMode, 
					deviceEnablePassword, 
					nodeCatId, 
					deviceAccessMethodId, 
					connPort
					FROM nodes WHERE id = " . $rid . " AND status = 1";

if($result = $db->q($getNodesSql)) {

	// push rows to $devices array
	$devices = array();
	while($row = mysql_fetch_assoc($result)){
		array_push($devices, $row);
	}

	// create cliOutputArray
	$cliOutputArray = array();
	
	foreach($devices as $device){ // iterate over each device - in this scripts case, there will only be a single device

	// set prompt for SSH conns
	$prompt = $device['devicePrompt'];
	
	// verification of host reachability based on fsockopen to host port i.e. 22 or 23. If fails, continue to next foreach iteration		
	$status = getHostStatus($device['deviceIpAddr'], $device['connPort']); // getHostStatus() from functions.php 
	
	if ($status === "<font color=red>Unavailable</font>"){
		$text = "Failure: Unable to connect to ".$device['deviceName']." - ".$device['deviceIpAddr']." when running Router ID ".$rid;
		$jsonArray['connFailMsg'] = $text;
		$log->Conn($text." - getHostStatus() Error:(File: ".$_SERVER['PHP_SELF'].")"); // logg to file
		echo json_encode($jsonArray);
		continue;
	}
	
	if (!empty($providedUsername) && !empty($providedPassword) && $providedUsername != "0" && $providedPassword != "0"){
		$conn = new Connection($device['deviceIpAddr'], $providedUsername, $providedPassword, $device['deviceEnableMode'], $providedPassword, $device['connPort'], $timeout);	
	}else{
		// create the connection by calling the connection class
		$conn = new Connection($device['deviceIpAddr'], $device['deviceUsername'], $device['devicePassword'], $device['deviceEnableMode'], $device['deviceEnablePassword'], $device['connPort'], $timeout);	
	}
	
	// get the config snippet data from the DB
	$cmdsSql = $db->q("SELECT * FROM snippets WHERE id = ".$snipId);
	$result = mysql_fetch_assoc($cmdsSql);
	$snippet = $result['snippet'];
	$snippetArr = explode("\n", $snippet); // explode text new lines to array
	$snippetArr=array_map('trim',$snippetArr); // trim whitespace from each array value
	
	$connFailureText = "Failure: Unable to connect to ".$device['deviceName']." - ".$device['deviceIpAddr']." for Router ID ".$rid;
	$connSuccessText = "Success: Connected to ".$device['deviceName']." (".$device['deviceIpAddr'].") for Router ID ".$rid;
			
	// if connection is telnet, connect to device function
	if($device['deviceAccessMethodId'] == '1'){ // 1 = telnet

		if($conn->connectTelnet() === false){
			$log->Conn($connFailureText." - in  Error:(File: ".$_SERVER['PHP_SELF'].")"); // logg to file
			$jsonArray['failTelnetConnMsg'] = $text;
			echo json_encode($jsonArray);
			continue; // continue; probably not needed now per device connection check at start of foreach loop - failsafe?
		}

		$jsonArray['telnetConnMsg'] = $connSuccessText.'<br /><br />';
		$log->Conn($connSuccessText." - in (File: ".$_SERVER['PHP_SELF'].")"); // log to file
		
		// iterate over snippet lines and get output from telnet session
		foreach($snippetArr as $k=>$command){
				$conn->writeSnippetTelnet($command, $result);
				$cliOutputArray[] = nl2br($result);
			}
			
		$conn->close('40'); // close telnet connection - ssh already closed at this point
	} elseif($device['deviceAccessMethodId'] == '3'){ //SSHv2 - cause SSHv2 is likely to come before SSHv1
		
			// SSH conn failure 
			$jsonArray['telnetConnMsg'] = $connSuccessText.'<br /><br />';
			$log->Conn($connSuccessText." - in (File: ".$_SERVER['PHP_SELF'].")"); // log to file
		
			$result = $conn->writeSnippetSSH($snippetArr, $prompt);
			$cliOutputArray[] = nl2br($result);
	
		} else {
			continue;	
		}
		 
	} //end foreach
$cliOutputArray = implode(", ", $cliOutputArray); // make flat string out of result array
$cliOutputArray = str_replace(',', '', $cliOutputArray); // remove commas from string
$jsonArray['result'] = $cliOutputArray; // add to jason array

// final msg
$jsonArray['finalMsg'] = "<br/><b>Manual device configuration completed</b> <br/><br/> <a href='javascript:window.close();'>close</a>";	

// echo json response for msgs back to page
// echo '<pre>';
// print_r($cliOutputArray);
echo json_encode($jsonArray);

// mail user results of snippet execution

$currentUser = $session->username;
$q = $db->q("SELECT email FROM users WHERE username = $currentUser");
$result = mysql_fetch_assoc($q);
$smtpCurrentUserAddr = $result['email'];

$q = $db->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");

$result	= mysql_fetch_assoc($q);
$smtpServerAddr	= $result['smtpServerAddr'];
$smtpFromAddr	  = $result['smtpFromAddr'];
$smtpRecipientAddr = $result['smtpRecipientAddr'];
if ($result['smtpAuth'] == 1) {
	$smtpAuth	 = $result['smtpAuth'];
	$smtpAuthUser = $result['smtpAuthUser'];
	$smtpAuthPass = $result['smtpAuthPass'];
}

$mail   = new PHPMailer();
// $report = $config_reports_basedir . $reportDirectory . "/" . $reportFilename;

// $body = file_get_contents($report);
$body = $cliOutputArray;

$mail->IsSMTP(); // telling the class to use SMTP
if ($result['smtpAuth'] == 1) {
	$mail->SMTPAuth = true; // enable SMTP authentication
	$mail->Username = $smtpAuthUser; // SMTP account username
	$mail->Password = $smtpAuthPass; // SMTP account password
}

$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
$mail->Host  = $smtpServerAddr; // sets the SMTP server
$mail->Port  = 25; // set the SMTP port for the GMAIL server

$mail->SetFrom($smtpFromAddr, $smtpFromAddr);
// $mail->AddReplyTo('list@mydomain.com', 'List manager');

// $mail->Subject = "rConfig Report - ".$taskname;
$mail->Subject = "rConfig Snippet - Sucess: Connected to ".$device['deviceName'];
$mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
$mail->MsgHTML($body);

$smtpRecipientAddresses = explode("; ", $smtpRecipientAddr);

foreach ($smtpRecipientAddresses as $emailAddr) {
	$mail->AddAddress($emailAddr);
}
//$mail->AddStringAttachment($row["photo"], "YourPhoto.jpg");

// // $mail->AddAddress($smtpCurrentUserAddr);

if (!$mail->Send()) {
	$log->Fatal('Fatal: ' . $title . ' Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
} else {
	$log->Info('Info: ' . $title . ' Email Report sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
}
// Clear all addresses and attachments for next loop
$mail->ClearAddresses();
$mail->ClearAttachments();

} else {
	$text = "Failure: Unable to get Device information from Database Command (File: ".$_SERVER['PHP_SELF'].") SQL ERROR: ". mysql_error();
	$log->Fatal($text);
	$jsonArray['finalMsg'] = $text;
	echo json_encode($jsonArray);
	die();
}

?>
