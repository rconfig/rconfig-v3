<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/crontab.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");
require_once("../../../classes/phpmailer/class.phpmailer.php");

$db  = new db();
$log = ADLog::getInstance();

/* Add tasks Here */
if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
    /* FORM FIELD VALIDATION BELOW */
    
    // validate taskType field
    if (!empty($_POST['taskType'])) {
        $taskType = $_POST['taskType'];
    } else {
        $errors['taskType'] = "Task Type cannot be empty";
    }
    
    // validate taskName & escape field
    if (!empty($_POST['taskName']) && is_string($_POST['taskName'])) {
        $taskType = mysql_real_escape_string($_POST['taskName']);
    } else {
        $errors['taskName'] = "Task Name cannot be empty";
    }
    
    // validate taskDesc & escape field
    if (!empty($_POST['taskDesc']) && is_string($_POST['taskDesc'])) {
        $taskType = mysql_real_escape_string($_POST['taskDesc']);
    } else {
        $errors['taskDesc'] = "Task Description cannot be empty";
    }
    
    // validate mailReport checkbox
    if (!empty($_POST['mailConnectionReport'])) {
        $mailConnectionReportChk = $_POST['mailConnectionReport'];
    } else {
        $mailConnectionReportChk = '0';
    }
	
    // validate snippetSlct select 
    if ($_POST['snippetSlct'] != 'select') {
        $snipId = deleteChar($_POST['snippetSlct'],10); // delete snippetId- from returned value
    } else {
		$errors['snippetSlct'] = "Select a Snippet";
    }
    
    // validate mailReport checkbox
    if (!empty($_POST['mailErrorsOnly'])) {
        $mailErrorsOnlyChk = $_POST['mailErrorsOnly'];
    } else {
        $mailErrorsOnlyChk = '0';
    }
    
    // validate selectRadio field
    if (isset($_POST['selectRadio'])) {
        if ($_POST['selectRadio'] == 'deviceSelectRadio' && empty($_POST['deviceSelect'])) {
            $errors['deviceSelectRadio'] = "You must choose some Devices";
        } elseif ($_POST['selectRadio'] == 'catSelectRadio' && empty($_POST['catSelect'])) {
            $errors['catSelectRadio'] = "You must choose a category(s)";
        }
    } else {
        $errors['selectRadio'] = "You must choose either devices or categories";
    }
    
    // validate catSelect select // used only if download type is selected on scheduler
	$categories = "";
    if (isset($_POST['catSelect'])) {
        $categories = serialize($_POST['catSelect']);
    }
	
	// validate catId select // used only if report type is selected on scheduler
    if (isset($_POST['catId'])) {
        $categories = serialize($_POST['catId']);
    }	
	
    // validate $deviceSelect select
    if (isset($_POST['deviceSelect'])) {
        $nodeIdArr = $_POST['deviceSelect'];
    } else {
        $errors['deviceSelectRadio'] = "Error: the deviceSelect array was empty" . mysql_error();
    }
	
    // validate $deviceSelect select
    if (isset($_POST['catCommand'])) {
        $catCommand = $_POST['catCommand'];
    } else {
        $errors['catCommand'] = "Error: the catCommand select was empty" . mysql_error();
    }

	// get the posts from the cron form and trim
	$minute = trim($_POST['minute']);
	$hour = trim($_POST['hour']);
	$day = trim($_POST['day']);
	$month = trim($_POST['month']);
	$weekday = trim($_POST['weekday']);
	
	// put them in an array
	$cronArray = array(
		"minute" => $minute,
		"hour" => $hour,
		"day" => $day,
		"month" => $month,
		"weekday" => $weekday
	);
	$regexPattern = '/^(?:[1-9]?\d|\*)(?:(?:[\/-][1-9]?\d)|(?:,[1-9]?\d)+)?$/';

	// test if any of the cron fields are empty
	foreach ($cronArray as $cronK=>$cronV){
		
		// echo $cronV;
		if($cronV === null || $cronV === ''){
			$errors['cron'] = 'A field was empty!';
			break;
		}
		if(!preg_match($regexPattern, $cronV)){
			$errors['cron'] = 'Field '.$cronK.' contains invalid characters';
			// break;	
		}
	}	

    /* END - VALIDATION */
    
    /* SQL and CRONTAB Additions below
     * Next code is based on the fact that the above validation has passed
     */
    if (!empty($_POST['taskName'])) {
        /* INSTALL CRON TABS */
        // Create Random Task ID to identify for scheduled script later
        $randNum = rand(100000, 999999);
        
        // pre-pend crontab task name with  # 
        $taskName    = "#" . $randNum . " - " . $_POST['taskName']; // add hash for comment in CRON script
        $taskDesc    = "#" . $randNum . " - " . $_POST['taskDesc'];
		$cronPattern = $minute .' '. $hour .' '. $day .' '. $month .' '. $weekday .' ';
        
        // check the task type selected and update the $cronScript VAR with the correct taskType
        if ($_POST['taskType'] == 1) { // taskType is download Configurations
            $script = "showCmdScript.php";
        } else if ($_POST['taskType'] == 2) {
			if(strstr($_POST['reportTypeSlct'][0], '-', true) == 'compliance'){ // check if the value form the select begins with str compliance
				$complianceId = substr($_POST['reportTypeSlct'][0], strrpos($_POST['reportTypeSlct'][0], '-' )+1 ); // get everything after the '-' and set the complianceId
				 $script = "complianceScript.php";
			} else {
				$script = "compareReportScript.php"; // default script name for $_POST['taskType'] == 2 (reports)
			}
        } else if ($_POST['taskType'] == 3) { // taskType is Schedule Config Snippet
			$script = "configCategoryScript.php";
		} else {
            $errors['taskTypeError'] = "There was a problem selecting the Task Type"; // throw an error
            $log->Warn("Failure: There was a problem selecting the Task Type (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        
        $cronScript = "php /home/rconfig/lib/" . $script . " " . $randNum;
        
        $crontab             = new crontab($cronScript, $taskName, $taskDesc, $cronPattern);
        $crontabUpdateResult = $crontab->addCron();
		
        if ($crontabUpdateResult == 0) {
            $errors['crontab'] = "Failure: Could not update crontab on the server "; // throw an error
            $log->Fatal("Failure: Could not update crontab on the server (File: " . $_SERVER['PHP_SELF'] . ")");
            
        }
        
        /* END - INSTALL CRON TABS */
        
        /* ADD CRONTAB RECORDS TO DB */
        if ($_POST['taskType'] == 1) { //taskType is download Configurations
            $query = "INSERT INTO tasks
				(id, 
				taskType,
				taskName,
				taskDescription,
				crontime,
				croncmd,
				addedby,
				dateadded,
				status,
				catId,
				mailConnectionReport,
				mailErrorsOnly) 
				VALUES 
					('" . $randNum . "',
					'" . $_POST['taskType'] . "', 								
					'" . $_POST['taskName'] . "', 								
					'" . $_POST['taskDesc'] . "', 								
					'" . $cronPattern . "', 								
					'" . $cronScript . "', 								
					'" . $_SESSION['username'] . "', 													
					NOW(), 								
					'1',
					'" . $categories . "',
					'" . $mailConnectionReportChk . "',		
					'" . $mailErrorsOnlyChk . "'		
					)";
        } else if ($_POST['taskType'] == 2) {
            $query = "INSERT INTO tasks
				(id, 
				taskType,
				taskName,
				taskDescription,
				crontime,
				croncmd,
				addedby,
				dateadded,
				status,
				mailConnectionReport,
				catId,
				catCommand,
				complianceId) 
				VALUES 
					('" . $randNum . "',
					'" . $_POST['taskType'] . "', 								
					'" . $_POST['taskName'] . "', 								
					'" . $_POST['taskDesc'] . "', 								
					'" . $cronPattern . "', 								
					'" . $cronScript . "', 								
					'" . $_SESSION['username'] . "', 													
					NOW(), 								
					'1',
					'" . $mailConnectionReportChk . "',		
					'" . $categories . "',
					'" . $catCommand . "',
					'" . $complianceId . "'
					)";
        } else if ($_POST['taskType'] == 3) {
            $query = "INSERT INTO tasks
				(id, 
				taskType,
				taskName,
				taskDescription,
				crontime,
				croncmd,
				addedby,
				dateadded,
				status,
				mailConnectionReport,
				catId,
				snipId) 
				VALUES 
					('" . $randNum . "',
					'" . $_POST['taskType'] . "', 								
					'" . $_POST['taskName'] . "', 								
					'" . $_POST['taskDesc'] . "', 								
					'" . $cronPattern . "', 								
					'" . $cronScript . "', 								
					'" . $_SESSION['username'] . "', 													
					NOW(), 								
					'1',
					'" . $mailConnectionReportChk . "',		
					'" . $categories . "',
					'" . $snipId . "'
					)";       
		}
        
        if ($result = $db->q($query)) {
		
        /* ADD NEW COLUMN TO NODES TABLE */
        /*  add to taskID column to nodes table in database to specify which nodes belong to this task 
        default of '2' means all nodes are not part of this task will update with 1 for node selection
        in next query
        */
        $addTaskColSql = "ALTER TABLE `nodes` ADD COLUMN taskId" . $randNum . " VARCHAR(20) NOT NULL DEFAULT '2' AFTER `id`";
        if ($result = $db->q($addTaskColSql)) {
            $log->Info("Success: Added task Column to nodes table to DB (File: " . $_SERVER['PHP_SELF'] . ")");
        } else {
            $errors['Fail'] = "ERROR: " . mysql_error();
            $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
        }
		/* END - ADD NEW COLUMN TO NODES TABLE */

		
        /* UPDATE NEW TASK COLUMN IN NODES TBL WITH '1' FOR SELECTED NODES/CATEGORIES */
        // Amend all selected Nodes new TaskID with a '1' to identify an active state for this task
		
        $categories = unserialize($categories);	// unserialize $categories before next tasks because was serialised for DB input

        if (!empty($categories)) {
            if (is_array($categories)) { // check if $categories is an array because when selecting compare report, cat option is single value
                
                $sanitized_post_ids = array();
                foreach ($categories as $id) {
                    $sanitized_post_ids[] = intval($id);
                }
                $in_str = implode(',', $sanitized_post_ids);
                $catRes = $db->q("SELECT id FROM nodes WHERE nodeCatId IN ( $in_str )");
            } else {
                $catRes = $db->q("SELECT id FROM nodes WHERE nodeCatId = $categories ");
                // $nodeIdArr = mysql_fetch_assoc($catRes);
            }
            
            $nodeIdArr = array();
            while ($catRow = mysql_fetch_assoc($catRes)) {
                $nodeIdArr[] = $catRow['id'];
            }
        }
        
        if (!empty($nodeIdArr)) {
            //Let's sanitize it
            $sanitized_ids = array();
            foreach ($nodeIdArr as $nodeId) {
                $sanitized_ids[] = intval($nodeId);
            }
            
            //Get the ids and add a trailing ",", but remove the last one
            $in_str = implode(',', $sanitized_ids);
            
            //Build the sql and execute it
            $addNodesToTask = "UPDATE nodes SET taskId" . $randNum . " = 1 WHERE id IN ( $in_str ) AND status = 1";
            
            if ($result = $db->q($addNodesToTask)) {
                $log->Info("Success: Added task Column to nodes table to DB (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
            }
        }
        
        /* END - UPDATE NEW COLUMN WITH '1' FOR SELECTED NODES/CATEGORIES */
	}
		
            $errors['Success'] = "Added Task Successfully";
            $log->Info("Success: Added Task to DB (File: " . $_SERVER['PHP_SELF'] . ")");
			$_SESSION['errors'] = $errors;
			session_write_close();
			header("Location: " . $config_basedir . "scheduler.php");
			exit();
        } else {
            $errors['Fail'] = "ERROR: " . mysql_error();
            $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        /* END - ADD CRONTAB RECORDS TO DB */
        
		
    // set the session id if any errors occur and redirect back to devices page with ?error set for JS on that page to keep form open 
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "scheduler.php?errors");
        exit();
    }
} // end add code

/* begin delete check */
elseif (isset($_POST['del'])) {
    $tid = $_POST['id'];
    
    // Query to retrieve row for given ID
    $delTaskSelectQ = "SELECT taskName, taskDescription, crontime, croncmd  FROM tasks WHERE id = " . $tid;
    $result = $db->q($delTaskSelectQ) or die ("ERROR: " . mysql_error());

    // Put row results into variables
    while ($row = mysql_fetch_assoc($result)) {
        $delTaskName = "#" . $tid . " - " . $row['taskName'];
        $delTaskDesc = "#" . $tid . " - " . $row['taskDescription'];
        $delCronJob  = $row['crontime'] . $row['croncmd'];
    }

    /* The cronfeed.txt/CronTab Delete */
	$crontab             = new crontab($delCronJob, $delTaskName, $delTaskDesc, $row['crontime']);
	$crontabUpdateResult = $crontab->removeCron($delTaskName, $delTaskDesc, $delCronJob);
	
	if ($crontabUpdateResult == 0) {
		$errors['crontab'] = "Failure:Could not update crontab on the server "; // throw an error
		$log->Fatal("Failure: Could not update crontab on the server (File: " . $_SERVER['PHP_SELF'] . ")");
		
	}

    /* the DB query*/
    $delTaskQ    = "UPDATE tasks SET status = 2 WHERE id = " . $tid;
    // Delete the tid column from the nodes table
    $delTaskColQ = "ALTER TABLE nodes DROP COLUMN taskId" . $tid . ";";
    $db->q($delTaskColQ);
    
    if ($result = $db->q($delTaskQ)) {
        $log->Info("Success: Deleted task " . $_POST['id'] . " in DB and CRONTAB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete task " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
    echo $response;
    
} /* end 'delete' if*/

elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $q = $db->q("SELECT * FROM tasks WHERE status = 1 AND id = $id");
    
    $items = array();
    while ($row = mysql_fetch_assoc($q)) {
        array_push($items, $row);
    }
    
    $devicesTaskQ = $db->q("SELECT deviceName FROM nodes WHERE taskId" . $id . " = 1");
    
    $devices = array();
    while ($rowDev = mysql_fetch_assoc($devicesTaskQ)) {
        array_push($devices, $rowDev);
    }
    
    $result["rows"]    = $items;
    $result["devices"] = $devices;
    echo json_encode($result);
    
}
/* end GetId */

?>