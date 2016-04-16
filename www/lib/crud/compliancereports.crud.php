<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");

$db  = new db();
$log = ADLog::getInstance();

/* Add Categories Here */

if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
// escaped variables
$reportsName = mysql_real_escape_string($_POST['reportsName']);
$reportsDesc = mysql_real_escape_string($_POST['reportsDesc']);


/* validations */

// validate reportsName field
if (empty($reportsName)) {
	$errors['reportsName'] = "Report Name field cannot be empty";
}

// validate reportsDesc field
if (empty($reportsDesc)) {
	$errors['reportsDesc'] = "Report Description field cannot be empty";
}

// validate selectedPolicies field
if (empty($_POST['selectedPolicies'])) {
	$errors['selectedPolicies'] = "You must select at least one Policy";
}

// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
if (!empty($errors)) {
	// set return vars if validation failure
	$errors['reportsNameVal'] = $reportsName;
	$errors['reportsDescVal'] = $reportsDesc;
    $_SESSION['errors'] = $errors;
    session_write_close();
    header("Location: " . $config_basedir . "compliancereports.php?errors&elem=".$elementValue);
    exit();
}

    if (empty($errors)) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
            
            // add reports to complianceReports table
            $q = "INSERT INTO complianceReports
							(reportsName, reportsDesc) 
							VALUES 
							(	'" . $reportsName . "', 
								'" . $reportsDesc . "'
							)";
			
            if ($result = $db->q($q)) {
				// insert complianceReportPolTbl values pairs
				// get reports insert ID from previous Insert stmt
				$lastInsertId = $db->lastID();
				foreach ($_POST['selectedPolicies'] as $selectedPolicies) {
						$q = "INSERT INTO complianceReportPolTbl 
							(reportId, polId) 
							VALUES 
							(	'" . $lastInsertId . "', 
								'" . $selectedPolicies . "'
							)";
							$db->q($q);
							error_log($q); 
					}

                $errors['Success'] = "Added Report to DB";
                $log->Info("Success: Added Report to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancereports.php?success");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancereports.php?errors&elem=".$elementValue);
                exit();
            }

        } else { // actual edit
            
            /* validate editid is numeric */
            if (ctype_digit($_POST['editid'])) {
                $id = $_POST['editid'];
            } else {
                $errors['Fail'] = "Fatal: editid not of type int for edit";
                $log->Fatal("Fatal: editid not of type int for edit - " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancereports.php?errors&elem=".$elementValue);
                exit();
            }
            // update the main reports details
            $q = "UPDATE complianceReports SET 
					reportsName = '" . $reportsName . "', 
					reportsDesc = '" . $reportsDesc . "'
					WHERE id = " . $id;
            
            if ($result = $db->q($q)) {
			// if main reports details updated, then delete all reports/element pairings and insert new batch per selected box on form
				$delQ = "DELETE FROM complianceReportPolTbl WHERE reportId = " . $id . ";";
				$db->q($delQ);
				foreach ($_POST['selectedPolicies'] as $selectedPolicies) {
						$updateQ = "INSERT INTO complianceReportPolTbl 
							(reportId, polId) 
							VALUES 
							(	'" . $id . "', 
								'" . $selectedPolicies . "'
							)";
							$db->q($updateQ);
							// error_log($q); for debugging
					}
			
                // return success
                $errors['Success'] = "Edited Report '" . $reportsName . "' in Database";
                $log->Info("Success: Edited Report " . $reportsName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                session_write_close();
				header("Location: " . $config_basedir . "compliancereports.php?errors");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancereports.php?errors&elem=".$elementValue);
                exit();
            }
        }
    }/* end 'id' post check*/
} /* end 'add' if*/

/* begin delete check */
elseif (isset($_POST['del'])) {
    if (ctype_digit($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int";
        $log->Fatal("Fatal: id not of type int - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "compliancereports.php?error");
        exit();
    }
    /* the query*/
    $q = "UPDATE complianceReports SET status = 2 WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
		// hard delete reports/element pairings
		$delQ = "DELETE FROM complianceReportPolTbl WHERE reportId = " . $id . ";";
		$db->q($delQ);
        $log->Info("Success: Deleted Report in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete Report in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
    echo $response;
} /* end 'delete' if*/ /* Below is used for an ajax call from vendors update 

jquery function to get row information to present back to compliance reports edit form*/ 
elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
    if (ctype_digit($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
		header("Location: " . $config_basedir . "compliancereports.php?errors");
        exit();
    }
    
    $q = $db->q("SELECT reportsName, reportsDesc
		FROM complianceReports
		WHERE id =" . $id);
    $items = array();
    while ($row = mysql_fetch_assoc($q)) {
        array_push($items, $row);
    }
    $result["rows"] = $items;
    echo json_encode($result);
}
/* end GetId */

?>