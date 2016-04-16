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
    
    
    /* validations */
    
    // validate command field
    
    if (!empty($_POST['command'])) {
        $command = str_replace(" ", "", $_POST['command']); // remove spaces for alpha validation
        if (ctype_alpha($command)) {
            $command = $_POST['command']; // reset $command var to actual input
        } else {
            $errors['command'] = "Command input invalid";
        }
        
    } else {
        $errors['command'] = "Command field cannot be empty";
    }
    
    
    if (preg_match('/(?:\\\[trn])+/', $_POST['command'])) {
        $errors['command'] = "Command input invalid";
    } else {
        $command = mysql_real_escape_string($_POST['command']); // reset $command var to actual input and escape
    }
    
    
    /* if command inputted and no cat selected - return error */
    $catId = $_POST['catId'];
    
    if ($catId['0'] == 0) {
        $errors['catId'] = "You must select a category";
    } else {
        $catId = $_POST['catId'];
    }
    
    
    if (!empty($command) && $catId['0'] != 0) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        if (empty($_POST['editid'])) { // actual add
            
            // add command to configcommand table
            $q = "INSERT INTO configcommands
							(command, 
							status) 
							VALUES 
								('" . $command . "', 
								'1'
								)";
            
            if ($result = $db->q($q)) {
                $cmdIdQ = $db->q('SELECT id FROM configcommands WHERE command = "' . $command . '"');
                while ($row = mysql_fetch_assoc($cmdIdQ)) {
                    $cmdId = $row['id'];
                }
                
                // next loop over catId Post and get all IDs selected and insert to cmdCatTbl
                $catIds = $_POST['catId'];
                for ($i = 0; $i < count($catIds); $i++) {
                    $catId = $catIds[$i];
                    $db->q('INSERT INTO cmdCatTbl (configCmdId, nodeCatId) VALUES (' . $cmdId . ', ' . $catId . ')');
                }
                
                $errors['Success'] = "Added command '" . $command . "' to Database";
                $log->Info("Success: Added command " . $command . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "commands.php");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "commands.php?error");
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
                header("Location: " . $config_basedir . "categories.php?error");
                exit();
            }
            
            $q = "UPDATE configcommands SET command = '" . $command . "'
						WHERE id = " . $id;
            
            if ($result = $db->q($q)) {
                //then delete all entires from the cmdCatTbl with related ID first. Then update with new values
                $db->q("DELETE FROM cmdCatTbl WHERE configCmdId = " . $id);
                
                // next loop over catId Post and get all IDs selected and insert to cmdCatTbl
                $catIds = $_POST['catId'];
                for ($i = 0; $i < count($catIds); $i++) {
                    $catId = $catIds[$i];
                    $db->q('INSERT INTO cmdCatTbl (configCmdId, nodeCatId) VALUES (' . $id . ', ' . $catId . ');');
                }
                
                // return success
                $errors['Success'] = "Edited command '" . $command . "' in Database";
                $log->Info("Success: Edited command " . $command . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "commands.php");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "commands.php?error");
                exit();
            }
        }
        
    }
    /* end 'id' post check*/
}

// set the session id if any errors occur and redirect back to devices page with ?error set for JS on that page to keep form open 

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    session_write_close();
    header("Location: " . $config_basedir . "commands.php?errors");
    exit();
}

/* end 'add' if*/


/* begin delete check */
elseif (isset($_POST['del'])) {
    /* the query*/
    $q = "UPDATE configcommands SET status = 2 WHERE id = " . $_POST['id'] . ";";
    
    if ($result = $db->q($q)) {
        //then delete all entires from the cmdCatTbl with related ID first. Then update with new values
        $db->q("DELETE FROM cmdCatTbl WHERE configCmdId = " . $_POST['id']);
        $log->Info("Success: Deleted Command " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to deleted Command " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
        header("Location: " . $config_basedir . "categories.php?error");
        exit();
    }
    
    $q     = $db->q("SELECT 
			command
		FROM configcommands
		WHERE status = 1
		AND id =" . $id);
    $items = array();
    while ($row = mysql_fetch_assoc($q)) {
        array_push($items, $row);
    }
    
    $result["rows"] = $items;
    echo json_encode($result);
    
}
/* end GetId */

?>