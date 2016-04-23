<?php
// requires - full path required
require("/home/rconfig/classes/db2.class.php");
require("/home/rconfig/classes/backendScripts.class.php");
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
$db2 = new db2();
//setup backend scripts Class
$backendScripts = new backendScripts($db2);
// get & set time for the script
$backendScripts->getTime();

// declare Logging Class
$log = ADLog::getInstance();
$log->logDir = $config_app_basedir . "logs/";

// script startTime and use extract to convert keys into variables for the script
extract($backendScripts->startTime());
// get ID from argv[1] input
/// if statement to check first argument in phpcli script - otherwise the script will not run under phpcli - similar to PHP getopt()
// script will exit with Error if not TID is sent
if (isset($argv[1])) {
    $_GET['id'] = $argv[1];
} else {
    echo $backendScripts->errorId($log, 'Task ID');
}

//set $argv to true of false based on 2nd parameter input
if (isset($argv[2]) && $argv[2] == 'true') {$argv = true;} else {$argv = false;}
extract($backendScripts->debugOnOff($db2, $argv));
$debug = new debug($debugPath);

// check how the script was run and log the info
$resetPerms = 0;
if (php_sapi_name() == 'cli') {
    if (isset($_SERVER['TERM'])) {
        $log->Info("The " . $_SERVER['PHP_SELF'] . " script was run from a manual invocation on a shell with Task ID:" . $tid . ""); // logg to file
        echo "The " . $_SERVER['PHP_SELF'] . " script was run from a manual invocation on a shell\r\n";
        // set this var so that later we can reset permissions on the /home/rconfig/data dir. Running the script from a shell causes perms to be set as root otherwise
        $resetPerms = 1;
    } else {
        $log->Info("The " . $_SERVER['PHP_SELF'] . " script was run from crontab ID:" . $tid . ""); // logg to file
        echo "The script was run from the crontab entry\r\n";
    }
} else {
    $log->Info("The " . $_SERVER['PHP_SELF'] . " script was run from a webserver, or something else"); // logg to file
    echo "The script was run from a webserver, or something else\r\n";
}


// get task details from DB
$db2->query("SELECT taskname, mailConnectionReport, snipId FROM tasks WHERE status = '1' AND id = :tid");
$db2->bind(':tid', $tid);
$taskRow = $db2->resultset();
$taskname = $taskRow[0]['taskname'];
$snipId = $taskRow[0]['snipId'];

// create connection report file
$reportFilename = 'conigSnippetReport' . $date . '.html';
$reportDirectory = 'configSnippetReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - " . $taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$connStatusFail = '<font color="red">Connection Fail</font>';
$connStatusPass = '<font color="green">Connection Success</font>';

// get timeout setting from DB
$db2->query("SELECT deviceConnectionTimout FROM settings");
$timeoutResult = $db2->resultset();
$timeout = $timeoutResult[0]['deviceConnectionTimout'];

// Get active nodes for a given task ID
// Query to retrieve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$db2->query("SELECT id, deviceName, deviceIpAddr, devicePrompt, deviceUsername,devicePassword, deviceEnableMode, deviceEnablePassword, nodeCatId, deviceAccessMethodId, connPort 
		FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1");
$resultNodesRes = $db2->resultset();
if (!empty($resultNodesRes)) {
    // push rows to $devices array
    $devices = array();
    foreach ($resultNodesRes as $row) {
        array_push($devices, $row);
    }

// get the config snippet data from the DB
    $db2->query("SELECT * FROM snippets WHERE id = :snipId");
    $db2->bind(':snipId', $snipId);
    $cmdsSql = $db2->resultset();
    $snippet = $cmdsSql[0]['snippet'];
    $cmdsSql = $db->q("SELECT * FROM snippets WHERE id = " . $snipId);
    $result = mysql_fetch_assoc($cmdsSql);
    $snippet = $result['snippet'];
    $snippetArr = explode("\n", $snippet); // explode text new lines to array
    $snippetArr = array_map('trim', $snippetArr); // trim whitespace from each array value
    $tableRow = "";

    foreach ($devices as $device) {
        // debugging check and action
        if ($debugOnOff === '1' || isset($cliDebugOutput)) {
            $debug->debug($device);
        }
        // ok, verification of host reachability based on fsockopen to host port i.e. 22 or 23. If fails, continue to next foreach iteration		
        $status = getHostStatus($device['deviceIpAddr'], $device['connPort']); // getHostStatus() from functions.php 

        if ($status === "<font color=red>Unavailable</font>") {
            $text = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
            $report->eachComplianceDataRowDeviceName($device['deviceName'], $connStatusFail, $text); // log to report
            echo $text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            continue;
        }

        // get the category for the device						
        $catNameQ = $db->q("SELECT categoryName FROM categories WHERE id = " . $device['nodeCatId']);
        $db2->query("SELECT categoryName FROM categories WHERE id = :nodeCatId");
        $db2->bind(':nodeCatId', $device['nodeCatId']);
        $catNameRow = $db2->resultset();
        $catName = $catNameRow[0]; // select only first value returned
        // declare file Class based on catName and DeviceName
        $file = new file($catName, $device['deviceName'], $config_data_basedir);

        // Connect for each row returned - might want to do error checking here based on if an IP is returned or not
        $conn = new Connection($device['deviceIpAddr'], $device['deviceUsername'], $device['devicePassword'], $device['deviceEnableMode'], $device['deviceEnablePassword'], $device['connPort'], $timeout);

        $failureText = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
        $connectedText = "Success: Connected to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for taskID " . $tid;

        // Set VARs
        $prompt = $device['devicePrompt'];

        if (!$prompt) {
            echo "Command or Prompt Empty - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn("Command or Prompt Empty - for function switch in  Success:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
        }

        // if connection is telnet, connect to device function
        if ($device['deviceAccessMethodId'] == '1') { // 1 = telnet
            if ($conn->connectTelnet() === false) {
                $log->Conn($connFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                $report->eachData($device['deviceName'], $connStatusFail, $failureText); // log to report
                echo $failureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                continue; // continue; probably not needed now per device connection check at start of foreach loop - failsafe?
            }

            echo $connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
            $report->eachData($device['deviceName'], $connStatusPass, $connectedText); // log to report

            foreach ($snippetArr as $k => $command) {
                $conn->writeSnippetTelnet($command, $result);
                $tableRow .= "
			<tr class=\"even indentRow\" style=\"float:left; width:800px;\">
				<td>" . nl2br($result) . "</td>							
			</tr>
			";
            }

            $conn->close('40'); // close telnet connection - ssh already closed at this point	
        } elseif ($device['deviceAccessMethodId'] == '3') { //SSHv2 - cause SSHv2 is likely to come before SSHv1
            // SSH conn failure 
            echo $connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
            $report->eachData($device['deviceName'], $connStatusPass, $connectedText); // log to report

            $result = $conn->writeSnippetSSH($snippetArr, $prompt);
            $tableRow .= "
		<tr class=\"even indentRow\" style=\"float:left; width:800px;\">
			<td>" . nl2br($result) . "</td>							
		</tr>
		";
        } else {
            continue;
        }

        // debugging check & write to file
        if ($debugOnOff === '1' || isset($cliDebugOutput)) {
            $debug->debug($result);
        }

        // send data output to the report
        $report->eachConfigSnippetData($tableRow);
        // unset tableRow data for next iteration
        $tableRow = "";
    }// devices foreach
// close table row tags	
// $report->endConfigSnippetData(); 
// script endTime
    $endTime = date('h:i:s A');
    $time_end = microtime(true);
    $time = round($time_end - $time_start) . " Seconds";

    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);

    $report->footer();


// Check if mailConnectionReport value is set to 1 and send email
    if ($taskRow['mailConnectionReport'] == '1') {
        require("/home/rconfig/classes/phpmailer/class.phpmailer.php");
        $db2->query("SELECT smtpServerAddr, smtpFromAddr, smtpRecipientAddr, smtpAuth, smtpAuthUser, smtpAuthPass FROM settings");
        $resultSelSmtp = $db2->resultset();
        $smtpServerAddr = $resultSelSmtp[0]['smtpServerAddr'];
        $smtpFromAddr = $resultSelSmtp[0]['smtpFromAddr'];
        $smtpRecipientAddr = $resultSelSmtp[0]['smtpRecipientAddr'];
        if ($result['smtpAuth'] == 1) {
            $smtpAuth = $resultSelSmtp[0]['smtpAuth'];
            $smtpAuthUser = $resultSelSmtp[0]['smtpAuthUser'];
            $smtpAuthPass = $resultSelSmtp[0]['smtpAuthPass'];
        }

        $mail = new PHPMailer();
        $report = $config_reports_basedir . $reportDirectory . "/" . $reportFilename;

        $body = file_get_contents($report);
        // $body = 'Test mail from rConfig';
        // $body = eregi_replace("[\]",'',$body);

        $mail->IsSMTP(); // telling the class to use SMTP
        if ($resultSelSmtp[0]['smtpAuth'] == 1) {
            $mail->SMTPAuth = true;      // enable SMTP authentication
            $mail->Username = $smtpAuthUser; // SMTP account username	
            $mail->Password = $smtpAuthPass;        // SMTP account password
        }

        $mail->SMTPKeepAlive = true;                  // SMTP connection will not close after each email sent
        $mail->Host = $smtpServerAddr;  // sets the SMTP server
        $mail->Port = 25;                    // set the SMTP port for the email server

        $mail->SetFrom($smtpFromAddr, $smtpFromAddr);
        $mail->Subject = "rConfig Report - " . $taskname;
        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
        $mail->MsgHTML($body);

        $smtpRecipientAddresses = explode("; ", $smtpRecipientAddr);

        foreach ($smtpRecipientAddresses as $emailAddr) {
            $mail->AddAddress($emailAddr);
        }
        // $mail->AddStringAttachment($row["photo"], "YourPhoto.jpg");

        if (!$mail->Send()) {
            $log->Fatal($title . ' Mailer Error (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ') ' . $mail->ErrorInfo);
        } else {
            $log->Info($title . ' Email Report sent to :' . $smtpRecipientAddr . ' (' . str_replace("@", "&#64;", $smtpRecipientAddr) . ')');
        }
        // Clear all addresses and attachments for next loop
        $mail->ClearAddresses();
        $mail->ClearAttachments();
    }

// reset folder permissions for data directory. This means script was run from the shell as possibly root 
// i.e. not apache user and this cause dir owner to be reset causing future downloads to be permission denied
    if ($resetPerms = 1) {
        $dataDir = '/home/rconfig/data/';
        shell_exec('chown -R apache ' . $dataDir);
        $log->Info("The owner permisions for directory " . $dataDir . " were reset to owner apache because script was run interactively"); // log to file
        echo "The owner permisions for directory " . $dataDir . " were reset to owner apache because script was run interactively\r\n";
    }
} else {
    echo "Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF'];
    $log->Fatal("Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF']);
    die();
}