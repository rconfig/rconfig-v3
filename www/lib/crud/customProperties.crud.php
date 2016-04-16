<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();


/* Add Custom Property Here */
if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
    if (!empty($_POST['customProperty'])) {
        /* Begin DB query. This will either be an Insert if $_POST ID is not set - or an edit/Update if ID is set in POST*/
        
        /* Validate Input from Form */
        if (!ctype_alnum($_POST['customProperty'])) {
            $errors['customProperty'] = "Input was not a valid string!";
            $log->Warn("Failure: customProperty Input was not a valid string! (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "customProperties.php?error");
            exit();
        } else {
            $customProperty = mysql_real_escape_string($_POST['customProperty']);
        }
        /* end validate */
        
        $property = str_replace(' ', '', $customProperty);
        $q        = "ALTER TABLE `nodes`  
					  ADD COLUMN `custom_" . $property . "` 
					  VARCHAR(255) 
					  NULL 
					  COMMENT 'Custom Property - " . $property . "' 
					  AFTER `status`;";
        if ($result = $db->q($q)) {
            $errors['Success'] = "Added new Custom Property " . $customProperty . " to Database";
            $log->Info("Success: Added new Custom Property, " . $customProperty . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "customProperties.php");
        } else {
            $errors['Fail'] = "ERROR: " . mysql_error();
            $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "customProperties.php?error");
            exit();
        }
        
    } else {
        $errors['customProperty'] = "Field cannot be empty";
        $log->Warn("Failure: vendorName was empty(File: " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "customProperties.php?error");
        exit();
    }
}
/* end 'add' */

/* begin delete check */
elseif (isset($_POST['del'])) {

$customProperty = str_replace("_", "", $_POST['id']); // remove spaces for alpha validation

	if (ctype_alpha($customProperty)) {
			$customProperty = mysql_real_escape_string($_POST['id']); // reset $command var to actual input
	} else {
			$log->Warn("Failure: invalid id sent to delete in (File: " . $_SERVER['PHP_SELF'] . ")");
			exit();
	} 
	
    /* the query*/
    $q = "ALTER TABLE `nodes` DROP COLUMN " . $customProperty  . ";";
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted Custom Property " . $customProperty  . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete Custom Property " . $customProperty  . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
    echo $response;
    
}
/* end 'delete' if*/
?>