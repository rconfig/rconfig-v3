<?php

// requires - full path required
require("/home/rconfig/classes/db.class.php");
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
// get ID from argv input
/// if statement to check first argument in phpcli script - otherwise the script will not run under phpcli - similar to PHP getopt()
// script will exit with Error if not TID is sent
if (isset($argv[1])) {
    $_GET['id'] = $argv[1];
} else {
    echo $backendScripts->errorId($log, 'Task ID');
}

//set $argv to true of false based on 2nd parameter input
if (isset($argv[2]) && $argv[2] == 'true') {
    $argv = true;
} else {
    $argv = false;
}
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


// get mailConnectionReport Status from tasks table and send email
$tasksResult = $db->q("SELECT taskname, mailConnectionReport, mailErrorsOnly FROM tasks WHERE status = '1' AND id = " . $tid);
$taskRow = mysql_fetch_assoc($tasksResult);
$taskname = $taskRow['taskname'];

// create connection report file
$reportFilename = 'deviceConnectionReport' . $date . '.html';
$reportDirectory = 'connectionReports';
$serverIp = getHostByName(getHostName()); // get server IP address for CLI scripts
$report = new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title = "rConfig Report - " . $taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$connStatusFail = '<font color="red">Connection Fail</font>';
$connStatusPass = '<font color="green">Connection Success</font>';

// get timeout setting from DB
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
										FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1";

if ($result = $db->q($getNodesSql)) {

    // push rows to $devices array
    $devices = array();

    while ($row = mysql_fetch_assoc($result)) {
        array_push($devices, $row);
    }


    foreach ($devices as $device) {

        // debugging check and action
        if ($debugOnOff === '1' || isset($cliDebugOutput)) {
            $debug->debug($device);
        }

        // ok, verification of host reachability based on fsockopen to host port i.e. 22 or 23. If fails, continue to next foreach iteration		
        $status = getHostStatus($device['deviceIpAddr'], $device['connPort']); // getHostStatus() from functions.php 

        if ($status === "<font color=red>Unavailable</font>") {
            $text = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
            $report->eachData($device['deviceName'], $connStatusFail, $text); // log to report
            echo $text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($text . " - getHostStatus() Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            continue;
        }

        // get command list for device. This is based on the catId. i.e. catId->cmdId->CmdName->Node
        $commands = $db->q("SELECT cmd.command 
							FROM cmdCatTbl AS cct
							LEFT JOIN configcommands AS cmd ON cmd.id = cct.configCmdId
							WHERE cct.nodeCatId = " . $device['nodeCatId']);
        $cmdNumRows = mysql_num_rows($commands);

        // get the category for the device						
        $catNameQ = $db->q("SELECT categoryName FROM categories WHERE id = " . $device['nodeCatId']);

        $catNameRow = mysql_fetch_row($catNameQ);
        $catName = $catNameRow[0]; // select only first value returned
        // check if there are any commands for this devices category, and if not, error and break the loop for this iteration
        if ($cmdNumRows == 0) {
            $text = "Failure: There are no commands configured for category " . $catName . " when running taskID " . $tid;
            $report->eachData($device['deviceName'], $connStatusFail, $text); // log to report
            echo $text . " - Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($text . " - Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
            continue;
        }

        // declare file Class based on catName and DeviceName
        $file = new file($catName, $device['deviceName'], $config_data_basedir);

        // Connect for each row returned - might want to do error checking here based on if an IP is returned or not
        $conn = new Connection($device['deviceIpAddr'], $device['deviceUsername'], $device['devicePassword'], $device['deviceEnableMode'], $device['deviceEnablePassword'], $device['connPort'], $timeout);

        // if connection is telnet, connect to device function
        if ($device['deviceAccessMethodId'] == '1') { // 1 = telnet
            $failureText = "Failure: Unable to connect to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " when running taskID " . $tid;
            $connectedText = "Success: Connected to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for taskID " . $tid;

            if ($conn->connectTelnet() === false) {
                $report->eachData($device['deviceName'], $connStatusFail, $failureText); // log to report
                echo $failureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                $log->Conn($failureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file

                continue; // continue; probably not needed now per device connection check at start of foreach loop - failsafe?
            }

            // telnet success logging
            // check if mailErrorsOnly flag is set and if it is, do not log successfuly connections
            if ($taskRow['mailErrorsOnly'] == '0') {
                $report->eachData($device['deviceName'], $connStatusPass, $connectedText); // log to report
            }
            echo $connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
            $log->Conn($connectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
        } // end if device access method

        $i = 0; // set i to prevent php notices	
        // loop over commands for given device
        while ($cmds = mysql_fetch_assoc($commands)) {
            $i++;

            // Set VARs
            $command = $cmds['command'];
            $prompt = $device['devicePrompt'];

            if (!$command || !$prompt) {
                echo "Command or Prompt Empty - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                $log->Conn("Command or Prompt Empty - for function switch in  Success:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                break;
            }

            // debugging check & write to file
            if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                $debug->debug($cmds);
            }

            //create new filepath and filename based on date and command -- see testFileClass for details - $fullpath return for use in insertFileContents method
            $fullpath = $file->createFile($command);

            // check for connection type i.e. telnet SSHv1 SSHv2 & run the command on the device
            if ($device['deviceAccessMethodId'] == '1') { // telnet
                $showCmd = $conn->showCmdTelnet($command, $prompt, $cliDebugOutput);
            } elseif ($device['deviceAccessMethodId'] == '3') { //SSHv2 - cause SSHv2 is likely to come before SSHv1
                $showCmd = $conn->connectSSH($command, $prompt);

                // if false returned, log failure
                if ($showCmd == false) {
                    $sshFailureText = "Failure: Unable to connect via SSH to " . $device['deviceName'] . " - " . $device['deviceIpAddr'] . " for command (" . $command . ")  when running taskID " . $tid;
                    echo $sshFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                    $log->Conn($sshFailureText . " - in  Error:(File: " . $_SERVER['PHP_SELF'] . ")"); // logg to file
                    $report->eachData($device['deviceName'], $connStatusFail, $sshFailureText); // log to report
                } else {

                    $sshConnectedText = "Success: Connected via SSH to " . $device['deviceName'] . " (" . $device['deviceIpAddr'] . ") for command (" . $command . ") for taskID " . $tid;
                    echo $sshConnectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")\n"; // log to console
                    $log->Conn($sshConnectedText . " - in (File: " . $_SERVER['PHP_SELF'] . ")"); // log to file
                    if ($taskRow['mailErrorsOnly'] == '0') {
                        $report->eachData($device['deviceName'], $connStatusPass, $sshConnectedText); // log to report
                    }
                }
            } else {
                continue;
            }

            // debugging check & write to file
            if ($debugOnOff === '1' || isset($cliDebugOutput)) {
                $debug->debug($showCmd);
            }

            // create new array with PHPs EOL parameter
            $filecontents = implode(PHP_EOL, $showCmd);

            // insert $filecontents to file
            $file->insertFileContents($filecontents, $fullpath);

            $filename = basename($fullpath); // get filename for DB entry
            $fullpath = dirname($fullpath); // get fullpath for DB entry
            // insert info to DB
            $configDbQ = "INSERT INTO configs (deviceId, configDate, configLocation, configFilename) 
					VALUES (
					" . $device['id'] . ", 
					NOW(), 
					'" . $fullpath . "',
					'" . $filename . "'
					)";

            if ($result = $db->q($configDbQ)) {
                $log->Conn("Success: Show Command '" . $command . "' for device '" . $device['deviceName'] . "' successful (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $log->Fatal("Failure: Unable to insert config information into DataBase Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR:" . mysql_error());
                die();
            }

            //check for last iteration... 
            if ($i == $cmdNumRows) {

                if ($device['deviceAccessMethodId'] == '1') { // 1 = telnet
                    $conn->close('40'); // close telnet connection - ssh already closed at this point
                }
            }
        }// end command while loop
    }// devices foreach
    // script endTime
    extract($backendScripts->endTime($time_start));
    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);
    $report->footer();
    // Check if mailConnectionReport value is set to 1 and send email
    if ($taskRow[0]['mailConnectionReport'] == '1') {
        $backendScripts->reportMailer($db2, $log, $title, $config_reports_basedir, $reportDirectory, $reportFilename, $taskname);
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
    echo "Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR: " . mysql_error();
    $log->Fatal("Failure: Unable to get Device information from Database Command (File: " . $_SERVER['PHP_SELF'] . ") SQL ERROR: " . mysql_error());
    die();
}
?>