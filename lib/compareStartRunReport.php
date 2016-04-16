<?php
// requires - full path required
require("/home/rconfig/classes/db.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
require('/home/rconfig/classes/sshlib/Net/SSH2.php'); // this will be used in connection.class.php 
require("/home/rconfig/classes/connection.class.php");
require("/home/rconfig/classes/debugging.class.php");
require("/home/rconfig/classes/textFile.class.php");
require("/home/rconfig/classes/reportTemplate.class.php");
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
$log         = ADLog::getInstance();
$log->logDir = $config_app_basedir . "logs/";

// script startTime
$startTime  = date('h:i:s A');
$date       = date('Ymd');
$time_start = microtime(true);


// if statement to check first argument in phpcli script - otherwise the script will not run under phpcli - similar to PHP getopt()
// script will exit with Error if not TID is sent
if (isset($argv[1])) {
    $_GET['id'] = $argv[1];
} else {
    $text = "Task ID not Set - unable to run script";
    echo $text . "\n";
    $log->Fatal("Error: " . $text . " (File: " . $_SERVER['PHP_SELF'] . ")");
    die();
}


// Get/Set Task ID - as sent from cronjob when this script is called and is stored in DB.nodes table also
$tid = $_GET['id'];

// get task details from DB
// get mailConnectionReport Status form tasks table and send email
$taskResult = $db->q("SELECT * FROM tasks WHERE id = $tid AND status = '1'");
$taskRow    = mysql_fetch_assoc($taskResult);
$taskname = $taskRow['taskname'];


// create connection report file
$reportFilename  = 'compareStartRunReport' . $date . '.html';
$reportDirectory = 'compareReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report          = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - ".$taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$reportFail = '<font color="red">Fail</font>';
$reportPass = '<font color="green">Success</font>';



// Get active nodes for a given task ID
// Query to retireve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$getNodesSql = "SELECT id, deviceName, deviceIpAddr, deviceUsername, devicePassword, deviceEnableMode, deviceEnablePassword, nodeCatId, deviceAccessMethodId, connPort FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1";

if ($result = $db->q($getNodesSql)) {
    // push rows to $devices array
    $devices = array();
    
    while ($row = mysql_fetch_assoc($result)) {
        array_push($devices, $row);
    }
    
	$startupConfigFile = "showstartup-config.txt";
	$runningConfigFile = "showrun.txt";
    
    foreach ($devices as $device) {
        $deviceId = $device['id'];
        
        $getRunningConfigFilePath = $db->q("SELECT * FROM configs 
									WHERE deviceId = $deviceId
									AND configFilename LIKE '%$runningConfigFile%'
									ORDER BY configDate 
									DESC LIMIT 1");
        
        $getStartupConfigFilePath = $db->q("SELECT * FROM configs 
									WHERE deviceId = $deviceId
									AND configFilename LIKE '%$startupConfigFile%'
									ORDER BY configDate 
									DESC LIMIT 1");
        
        $pathResultRunning     = mysql_fetch_assoc($getRunningConfigFilePath);
        $pathResultStartup 	   = mysql_fetch_assoc($getStartupConfigFilePath);
        
        if (empty($pathResultRunning) || empty($pathResultStartup)) {
            // continue for the foreach if one of the files is not available as this compare will be invalid
            echo 'continue invoked for ' . $device['deviceName'];
            continue;
        }
        
        
        $pathResult_a = $pathResultRunning['configLocation'];
        $pathResult_b = $pathResultStartup['configLocation'];
        
        $path_a = $pathResult_a . '/' . $runningConfigFile;
        $path_b = $pathResult_b . '/' . $startupConfigFile;
        
        // run the comapre with no linepadding set
        $diff = new diff;
        $text = $diff->inline($path_a, $path_b);
        
        $count = count($diff->changes) . ' changes';
        
        // send output to the report
        $report->eachData($device['deviceName'], $count, $text); // log to report

    } // End Data insert loop
    
    // script endTime
    $endTime  = date('h:i:s A');
    $time_end = microtime(true);
    $time     = round($time_end - $time_start) . " Seconds";
    
    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);
    
    $report->footer();
    
    if ($taskRow['mailConnectionReport'] == '1') {
        require($_SERVER['DOCUMENT_ROOT'] . "/lib/classes/phpmailer/class.phpmailer.php");
        
        $q = $db->q("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");
        
        $result            = mysql_fetch_assoc($q);
        $smtpServerAddr    = $result['smtpServerAddr'];
        $smtpFromAddr      = $result['smtpFromAddr'];
        $smtpRecipientAddr = $result['smtpRecipientAddr'];
        if ($result['smtpAuth'] == 1) {
            $smtpAuth     = $result['smtpAuth'];
            $smtpAuthUser = $result['smtpAuthUser'];
            $smtpAuthPass = $result['smtpAuthPass'];
        }
        
        $mail   = new PHPMailer();
        $report = $config_reports_basedir . $reportDirectory . "/" . $reportFilename;
        
        $body = file_get_contents($report);
        // $body = 'Test mail from rConfig';
        // $body = eregi_replace("[\]",'',$body);
        
        $mail->IsSMTP(); // telling the class to use SMTP
        if ($result['smtpAuth'] == 1) {
            $mail->SMTPAuth = true; // enable SMTP authentication
            $mail->Username = $smtpAuthUser; // SMTP account username	
            $mail->Password = $smtpAuthPass; // SMTP account password
        }
        
        $mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
        $mail->Host          = $smtpServerAddr; // sets the SMTP server
        $mail->Port          = 25; // set the SMTP port for the GMAIL server
        
        $mail->SetFrom($smtpFromAddr, $smtpFromAddr);
        // $mail->AddReplyTo('list@mydomain.com', 'List manager');
        
        $mail->Subject = "rConfig Report - ".$taskname;
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->MsgHTML($body);
        
        
        $smtpRecipientAddresses = explode("; ", $smtpRecipientAddr);
        
        foreach ($smtpRecipientAddresses as $emailAddr) {
            $mail->AddAddress($emailAddr);
        }
        // $mail->AddStringAttachment($row["photo"], "YourPhoto.jpg");
        
        if (!$mail->Send()) {
            $log->Fatal('Fatal: ' . $title . ' Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
        } else {
            $log->Info('Info: ' . $title . ' Email Report sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
        }
        // Clear all addresses and attachments for next loop
        $mail->ClearAddresses();
        $mail->ClearAttachments();
    }
    
    
} else {
    $log->Fatal("Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR:" . mysql_error());
    die();
}


?>