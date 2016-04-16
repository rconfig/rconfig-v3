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
$policyName = mysql_real_escape_string($_POST['policyName']);
$policyDesc = mysql_real_escape_string($_POST['policyDesc']);


/* validations */

// validate policyName field
if (empty($policyName)) {
	$errors['policyName'] = "Policy Name field cannot be empty";
}

// validate policyDesc field
if (empty($policyDesc)) {
	$errors['policyDesc'] = "Policy Description field cannot be empty";
}

// validate policyDesc field
if (empty($_POST['selectedElems'])) {
	$errors['selectedElems'] = "You must select at least one Policy Element";
}

// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
if (!empty($errors)) {
	// set return vars if validation failure
	$errors['policyNameVal'] = $policyName;
	$errors['policyDescVal'] = $policyDesc;
    $_SESSION['errors'] = $errors;
    session_write_close();
    header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=".$elementValue);
    exit();
}

    if (empty($errors)) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
            
            // add policy to compliancePolicies table
            $q = "INSERT INTO compliancePolicies
							(policyName, policyDesc) 
							VALUES 
							(	'" . $policyName . "', 
								'" . $policyDesc . "'
							)";
			
            if ($result = $db->q($q)) {
				// insert compliancePolElemTbl values pairs
				// get policy insert ID from previous Insert stmt
				$lastInsertId = $db->lastID();
				foreach ($_POST['selectedElems'] as $selectedElems) {
						$q = "INSERT INTO compliancePolElemTbl 
							(polId, elemId) 
							VALUES 
							(	'" . $lastInsertId . "', 
								'" . $selectedElems . "'
							)";
							$db->q($q);
							// error_log($q); for debugging
					}

                $errors['Success'] = "Added Policy to DB";
                $log->Info("Success: Added Policy to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicies.php?success");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=".$elementValue);
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
				header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=".$elementValue);
                exit();
            }
            // update the main policy details
            $q = "UPDATE compliancePolicies SET 
					policyName = '" . $policyName . "', 
					policyDesc = '" . $policyDesc . "'
					WHERE id = " . $id;
            
            if ($result = $db->q($q)) {
			// if main policy details updated, then delete all policy/element pairings and insert new batch per selected box on form
				$delQ = "DELETE FROM compliancePolElemTbl WHERE polId = " . $id . ";";
				$db->q($delQ);
				foreach ($_POST['selectedElems'] as $selectedElems) {
						$updateQ = "INSERT INTO compliancePolElemTbl 
							(polId, elemId) 
							VALUES 
							(	'" . $id . "', 
								'" . $selectedElems . "'
							)";
							$db->q($updateQ);
							// error_log($q); for debugging
					}
			
                // return success
                $errors['Success'] = "Edited Policy '" . $policyName . "' in Database";
                $log->Info("Success: Edited Policy " . $policyName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicies.php?errors");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicies.php?errors&elem=".$elementValue);
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
        header("Location: " . $config_basedir . "compliancepolicies.php?error");
        exit();
    }
    /* the query*/
    $q = "UPDATE compliancePolicies SET status = 2 WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
		// hard delete policy/element pairings
		$delQ = "DELETE FROM compliancePolElemTbl WHERE polId = " . $id . ";";
		$db->q($delQ);
        $log->Info("Success: Deleted Policy in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete Policy in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
		header("Location: " . $config_basedir . "compliancepolicies.php?errors");
        exit();
    }
    
    $q = $db->q("SELECT policyName, policyDesc
		FROM compliancePolicies
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