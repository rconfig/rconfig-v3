<?php 
// requires - full path required
require("/home/rconfig/classes/db.class.php");
require("/home/rconfig/classes/ADLog.class.php");
require("/home/rconfig/classes/compareClass.php");
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
    $text = "Report ID not Set - unable to run script";
    echo $text . "\n";
    $log->Fatal("Error: " . $text . " (File: " . $_SERVER['PHP_SELF'] . ")");
    die();
}
// Get/Set report ID - as sent from cronjob when this script is called and is stored in DB.nodes table also
$tid = $_GET['id'];

// get task details from DB
$taskResult = $db->q("SELECT * FROM tasks WHERE id = $tid AND status = '1'");
$taskRow    = mysql_fetch_assoc($taskResult);
$command = str_replace(' ', '', $taskRow['catCommand']);
$taskname = $taskRow['taskname'];
$rid = $taskRow['complianceId'];
$complianceReportId = $taskRow['complianceId'];

// create connection report file
$reportFilename  	= 'complianceReport' . $date . '.html';
$reportDirectory 	= 'complianceReports';
$serverIp 			= getHostByName(getHostName()); // get server IP address for CLI scripts
$report 			= new report($config_reports_basedir, $reportFilename, $reportDirectory, $serverIp);
$report->createFile();
$title 				= "rConfig Report - ".$taskname;
$report->header($title, $title, basename($_SERVER['PHP_SELF']), $tid, $startTime);
$reportFail 		= '<font color="red">Fail</font>';
$reportPass 		= '<font color="green">Success</font>';

// get base_encoded images for checkmarks later on
$greenCheck = file_get_contents('/home/rconfig/www/images/tick_32.png.base');
$redCross = file_get_contents('/home/rconfig/www/images/redCross.png.base');

// get polices for given $rid from DB
$policies = array();
$policyResult = $db->q("SELECT polId FROM complianceReportPolTbl WHERE reportId = $rid");
while ($row = mysql_fetch_assoc($policyResult)) {
	$policies[$row['polId']] = $row['polId'];
}

// Get active nodes for a given task ID
// Query to retireve row for given ID (tidxxxxxx is stored in nodes and is generated when task is created)
$getNodesSql = "SELECT id, deviceName, deviceIpAddr, deviceUsername, devicePassword, deviceEnableMode, deviceEnablePassword, nodeCatId, deviceAccessMethodId, connPort FROM nodes WHERE taskId" . $tid . " = 1 AND status = 1";

if ($result = $db->q($getNodesSql)) {
    // push rows to $devices array
    $devices = array();
    
    while ($row = mysql_fetch_assoc($result)) {
        array_push($devices, $row);
    }
		
	// loop over retrieved devices
	foreach ($devices as $device) {
		$deviceId = $device['id'];

		$getPathSqlLatest = $db->q("SELECT * FROM configs 
										WHERE deviceId = $deviceId
										AND configFilename LIKE '%$command%'
										ORDER BY configDate 
										DESC LIMIT 1");
		$pathResultLatest     = mysql_fetch_assoc($getPathSqlLatest);
			
		// append device name to report		
		$report->eachComplianceDataRowDeviceName($device['deviceName']); // log deviceName to report
			
		// continue for the foreach if one of the files is not available as this compliance will be invalid		
		if (empty($pathResultLatest)) {
			echo 'continue invoked for ' . $device['deviceName'];
			continue;
		}
			
		$pathResult_a = $pathResultLatest['configLocation'];
		$filenameResult_a = $pathResultLatest['configFilename'];
		$configFile = $pathResult_a . '/' . $filenameResult_a;
		
//		$configFile = $pathResult_a . '/' . $command . '.txt';
		$tableRow = "";	// set tableRow for later use and avoid  Undefined variable errors
		
		foreach ($policies as $k=>$v){
			$q = "SELECT e.elemId, cpe.elementName, cpe.singleParam1, cpe.singleLine1
							FROM compliancePolElemTbl as e
							LEFT JOIN compliancePolElem AS cpe ON e.elemId = cpe.id
							WHERE polId = $v
							ORDER BY elementName ASC";
			$elemsRes = $db->q($q);	
		
			// get policynames for report output
			$policyNameRes = $db->q('SELECT policyName FROM compliancePolicies WHERE id ='.$v);	
			while ($row = mysql_fetch_assoc($policyNameRes)) {
				$policyName = $row['policyName'];
			}
			// print policy name header to report
			$report->eachComplianceDataRowPolicyName($policyName, $configFile); 
			
			while ($row = mysql_fetch_assoc($elemsRes)) {
				$elems[] = array(  
					'elemId' => $row['elemId'],  
					'elementName' => $row['elementName'],
					'singleParam1' => $row['singleParam1'],
					'singleLine1' => $row['singleLine1']
				) ;
			}		
	
			// file to array
			$fileArr = file($configFile);
			foreach($elems as $kElem=>$vElem){
			
			$string = $vElem['singleLine1'];
			
			// set pattern Based on selected parameter where 
			// 1 = equals
			// 2 = contains

			if($vElem['singleParam1'] == 1){
				$pattern = "/^$string\$/m";
				$symbol = '='; // not used yet
			} else if($vElem['singleParam1'] == 2){
				$pattern = "/.*$string.*/i";
				$symbol = '~'; // not used yet
			}
			
			foreach ($fileArr as $k=>$line) {
			
					if (regexpMatch($pattern, $line) == 1) {
						// convert images to base here http://webcodertools.com/imagetobase64converter/Create
						// images from https://www.iconfinder.com/icons/34218/add_cross_delete_exit_remove_icon#size=32
						$result = 1;
						$tableRow .= "
						<tr class=\"even indentRow\" style=\"float:left; width:800px;\">
							<td><img src='$greenCheck' width=\"16\" height=\"14\" alt=\"pass\" title=\"pass\">
							</td>	
							<td> - ".$vElem['elementName']."</td>							
						</tr>
						";
						break; // Check if result = 1 (next to check this becuase of possible multiline match failing) break the foreach for first match
					} else {
						$result = 0;
					}
				}
				// if not matched the output in the negative
				if ($result !== 1){
						$tableRow .= "
						<tr class=\"even indentRow\" style=\"float:left; width:800px;\">
							<td><img src='$redCross' width=\"16\" height=\"14\" alt=\"fail: line No - $k\" title=\"Fail: line - $k\">
							</td>
							<td> - ".$vElem['elementName']."</td>							
						</tr>
						";
					}
				}
			// unset elems array for next iteration
			unset($elems);
			// send data output to the report
			$report->eachComplianceData($tableRow); 
			// unset tableRow data for next iteration
			$tableRow = "";
		}
	// close table row tags	
	$report->endComplianceData(); 
	
	} // END - // loop over retrieved devices

    // script endTime
    $endTime  = date('h:i:s A');
    $time_end = microtime(true);
    $time     = round($time_end - $time_start) . " Seconds";
    
    $report->findReplace('<taskEndTime>', $endTime);
    $report->findReplace('<taskRunTime>', $time);
    
    $report->footer();

	// if mail option is set - mail the report
  if ($taskRow['mailConnectionReport'] == '1') {
        require("/home/rconfig/classes/phpmailer/class.phpmailer.php");
        
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