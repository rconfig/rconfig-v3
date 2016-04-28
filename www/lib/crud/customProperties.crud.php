<?php
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2 = new db2();
$log = ADLog::getInstance();


/* Add Custom Property Here */
if (isset($_POST['add'])) {
    session_start();
    $errors = array();

    if (!empty($_POST['customProperty'])) {
        /* Begin DB query. This will either be an Insert if $_POST ID is not set - or an edit/Update if ID is set in POST */

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
            $customProperty = $_POST['customProperty'];
        }
        /* end validate */

        $property = str_replace(' ', '', $customProperty);
            $db2->query("ALTER TABLE `nodes`  
                            ADD COLUMN `custom_" . $property . "` 
                            VARCHAR(255) 
                            NULL 
                            COMMENT 'Custom Property - " . $property . "' 
                            AFTER `status`;");
            $queryResult = $db2->execute();
        if($queryResult){
            $errors['Success'] = "Added new Custom Property " . $customProperty . " to Database";
            $log->Info("Success: Added new Custom Property, " . $customProperty . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "customProperties.php");
        } else {
            $errors['Fail'] = "ERROR: Could not add new Custom Property " . $customProperty . " to Database";
            $log->Fatal("Fatal: Could not add new Custom Property " . $customProperty . " to Database (File: " . $_SERVER['PHP_SELF'] . ")");
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
    $customProperty = $_POST['id'];
    /* the query */  
        $db2->query('ALTER TABLE `nodes` DROP COLUMN '.$customProperty); //not binding due to structural query
        $queryResult = $db2->execute();
        if($queryResult){
            $log->Info("Success: Deleted Custom Property " . $customProperty . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete Custom Property " . $customProperty . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
        ));
    }
    echo $response;
}
/* end 'delete' if */