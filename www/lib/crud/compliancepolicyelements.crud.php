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
$elementName = mysql_real_escape_string($_POST['elementName']);
$elementDesc = mysql_real_escape_string($_POST['elementDesc']);
$singleParam1 = mysql_real_escape_string($_POST['singleParam1']);
$singleLine1 = mysql_real_escape_string($_POST['singleLine1']);

/* validations */

// validate elementName field
if (empty($elementName)) {
	$errors['elementName'] = "Element Name field cannot be empty";
}

// validate elementDesc field
if (empty($elementDesc)) {
	$errors['elementDesc'] = "Element Description field cannot be empty";
}
	
// validate single element is not empty
if(empty($singleLine1)) {
	$errors['singleLine1'] = "Input cannot be empty";
}

// var_dump($_POST);die();
	
// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
if (!empty($errors)) {
	$errors['elementNameVal'] = $elementName;
	$errors['elementDescVal'] = $elementDesc;
	$errors['singleLine1val'] = $_POST['singleLine1'];
    $_SESSION['errors'] = $errors;
    session_write_close();
    header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=".$elementValue);
    exit();
}

    if (empty($errors)) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
            
            // add element to compliancePolElem table
            $q = "INSERT INTO compliancePolElem
							(elementName, elementDesc, singleParam1, singleLine1) 
							VALUES 
							(	'" . $elementName . "', 
								'" . $elementDesc . "', 							
								'" . $singleParam1 . "', 	
								'" . $singleLine1 . "'
							)";

            if ($result = $db->q($q)) {
                $errors['Success'] = "Added Policy Element to DB";
                $log->Info("Success: Added Policy Element to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicyelements.php?success");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=".$elementValue);
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
				header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=".$elementValue);
                exit();
            }
            
            $q = "UPDATE compliancePolElem SET 
					elementName = '" . $elementName . "', 
					elementDesc = '" . $elementDesc . "', 
					singleParam1 = '" . $singleParam1 . "', 
					singleLine1 = '" . $singleLine1 . "'
					WHERE id = " . $id;
            
            if ($result = $db->q($q)) {

                // return success
                $errors['Success'] = "Edited Policy Element '" . $elementName . "' in Database";
                $log->Info("Success: Edited Policy Element " . $elementName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicyelements.php?errors");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "compliancepolicyelements.php?errors&elem=".$elementValue);
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
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "compliancepolicyelements.php?error");
        exit();
    }
    /* the query*/
    $q = "UPDATE compliancePolElem SET status = 2 WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted Policy Element in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete Policy Element in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
    echo $response;
    
} /* end 'delete' if*/ /* Below is used for an ajax call from vendors update 

jquery function to get row information to present back to vendor edit form*/ 
elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
    if (ctype_digit($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
		header("Location: " . $config_basedir . "compliancepolicyelements.php?errors");
        exit();
    }
    
    $q = $db->q("SELECT elementName, elementDesc, singleParam1, singleLine1
		FROM compliancePolElem
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