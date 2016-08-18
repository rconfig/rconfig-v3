<?php

require_once("/home/rconfig/classes/usersession.class.php");
require_once("/home/rconfig/classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

$log = ADLog::getInstance();
if (!$session->logged_in) {
    echo 'Don\'t bother trying to hack me!!!!!<br /> This hack attempt has been logged';
    $log->Warn("Security Issue: Some tried to access this file directly from IP: " . $_SERVER['REMOTE_ADDR'] . " & Username: " . $session->username . " (File: " . $_SERVER['PHP_SELF'] . ")");
    // need to add authentication to this script
    header("Location: " . $config_basedir . "login.php");
} else {
    require_once("../../../classes/db2.class.php");
    require_once("../../../classes/imageResize.class.php");

    $db2 = new db2();
    $log = ADLog::getInstance();

    /* Add Profiles Here */
    if (isset($_POST['add'])) {
        $errors = array();

        if (!empty($_POST['profileName'])) {
            /* Validate Input from Form */
            if (!ctype_alnum($_POST['profileName'])) {
                $errors['profileName'] = "Input was not a valid string - alphaNumeric Characters only, and no spaces!";
                $log->Warn("Failure: categoryName Input was not a valid string! (File: " . $_SERVER['PHP_SELF'] . ")");
            }

        // validate deviceAccessMethodId field
        if (ctype_digit($_POST['deviceAccessMethodId'])) {
            $deviceAccessMethodId = $_POST['deviceAccessMethodId'];
        } else {
            $errors['deviceAccessMethodId'] = "You must select telnet or SSH";
            $log->Warn("Failure: deviceAccessMethodId input is incorrect (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        // validate vendors field
        if (ctype_digit($_POST['vendorId'])) {
            $vendorId = $_POST['vendorId'];
        } else {
            $errors['vendorId'] = "You must select a vendor";
            $log->Warn("Failure: vendorId input is incorrect (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        
        if (!empty($errors)) {
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "profiles.php?error");
                exit();
            } else {
                $profileName = $_POST['profileName'];
                $profileDescription = $_POST['profileDescription'];
            }
               
            // set text for deviceAccessMethodId so that proper path can be built
            if($deviceAccessMethodId == 1){
                $path = 'telnet';
            } elseif ($deviceAccessMethodId == 3){
                $path = 'ssh';
            }
            if (!empty($_FILES["profileFile"]["name"])) {

                if ($_FILES["profileFile"]["type"] == "application/octet-stream" && $_FILES["profileFile"]["size"] < 20000) {
                    
                    if ($_FILES["profileFile"]["error"] > 0) {
           
                        $errors['fileError'] = "File Error Return Code: " . $_FILES["profileFile"]["error"];
                        $log->Warn("File Error Return Code: " . $_FILES["profileFile"]["error"] . " (File: " . $_SERVER['PHP_SELF'] . ")");
                    } else {
                        $location = "/home/rconfig/classes/connectionProfiles/" . $path. "/" . $_FILES["profileFile"]["name"];
                        if (file_exists($location)) {
                            // if we have a duplicate filename, alert, and terminate the script
                            $duplicateFileError = "Failure: " . $_FILES["profileFile"]["name"] . " already exists (Script: " . $_SERVER['PHP_SELF'] . ")";
                            $log->Warn($duplicateFileError);
                            $errors['duplicatefile'] = $duplicateFileError;
                            $_SESSION['errors'] = $errors;
                            session_write_close();
                            header("Location: " . $config_basedir . "profiles.php?error");
                            exit();
                        } else {
                            move_uploaded_file($_FILES['profileFile']['tmp_name'], $location);
                        }
                    }
                } else {
                    $errors['fileInvalid'] = "Invalid File";
                    $log->Warn("Failure: Invalid File(File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "profiles.php?error");
                    exit();
                }
            } 
            /* end validate */

            /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
            if (empty($_POST['editid'])) { // do the add/ INSERT
                if (ctype_alnum($profileName)) {
                    $db2->query("INSERT INTO profiles "
                            . "(profileName, profileDescription, profileLocation, deviceAccessMethodId, vendorId, profileDateAdded, profileAddedBy, status) "
                            . "VALUES (:profileName, :profileDescription, :filename, :deviceAccessMethodId, :vendorId, :profileDateAdded, :profileAddedBy, '1')");
                    $db2->bind(':profileName', $profileName);
                    $db2->bind(':profileDescription', $profileDescription);
                    $db2->bind(':filename', $location);
                    $db2->bind(':deviceAccessMethodId', $deviceAccessMethodId);
                    $db2->bind(':vendorId', $vendorId);
                    $db2->bind(':profileDateAdded', date('Y-m-d'));
                    $db2->bind(':profileAddedBy', $_SESSION['username']);
                    $queryResult = $db2->execute();
                    if ($queryResult) { // if Q was good, send back a sucess to the file
                        $errors['Success'] = "Added new profile " . $profileName . " to Database";
                        $log->Info("Success: Added new profile, " . $profileName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "profiles.php");
                    } else {
                        $errors['Fail'] = "ERROR: Could not add new profile " . $profileName . " to Database";
                        $log->Fatal("Fatal: ERROR: Could not add new profile " . $profileName . " to Database (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "profiles.php?error");
                        exit();
                    }
                } else {
                    $errors['profileName'] = "Profile Name Field was not a string";
                    $log->Warn("Failure: profileName was not a string (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "profiles.php?error");
                    exit();
                }
            } else { // do the UPDATE/EDIT
                if (ctype_alnum($profileName)) {
                    $id = $_POST['editid'];
                    $db2->query("SELECT profileName, profileDescription, profileLocation, deviceAccessMethodId, vendorId, status FROM profiles WHERE status = 1 AND id = :id");
                    $db2->bind(':id', $id);
                    $queryResult = $db2->resultset();
                    
                    $db2->query("UPDATE profiles SET "
                            . "profileName = :profileName, "
                            . "profileDescription = :profileDescription, "
                            . "deviceAccessMethodId = :deviceAccessMethodId, "
                            . "vendorId = :vendorId, "
                            . "profileLastEditBy = :profileLastEditBy, "
                            . "profileLastEdit = :profileLastEdit "
                            . "WHERE id = :id");
                    $db2->bind(':profileName', $profileName);
                    $db2->bind(':profileDescription', $profileDescription);
                    $db2->bind(':deviceAccessMethodId', $deviceAccessMethodId);
                    $db2->bind(':vendorId', $vendorId);
                    $db2->bind(':profileLastEditBy', $_SESSION['username']);
                    $db2->bind(':profileLastEdit', date('Y-m-d H:i:s'));
                    $db2->bind(':id', $id);
                    $queryResult = $db2->execute();
                    if ($queryResult) {
                        $errors['Sucess'] = "Edited profile " . $profileName . " in Database";
                        $log->Info("Success: Edited profile, " . $profileName . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "profiles.php");
                    } else {
                        $errors['Fail'] = "ERROR: Could not edit profile " . $profileName . " in Database";
                        $log->Fatal("Fatal: ERROR: Could not edit profile " . $profileName . " in Database (File: " . $_SERVER['PHP_SELF'] . ")");
                        $_SESSION['errors'] = $errors;
                        session_write_close();
                        header("Location: " . $config_basedir . "profiles.php?error");
                        exit();
                    }
                } else {
                    $errors['profileName'] = "Profile Name Field was not a string";
                    $log->Warn("Failure: profileName was not a string (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "profiles.php?error");
                    exit();
                }
            }
            /* end 'id' post check */
        } else {
            $errors['profileName'] = "Profile Name Field cannot be empty";
            $log->Warn("Failure: profileName was empty(File: " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "profiles.php?error");
            exit();
        }
    }
    /* end 'add' if */

    /* begin delete check */ elseif (isset($_POST['del'])) {
        if (ctype_digit($_POST['id'])) {
            $id = $_POST['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "profiles.php?error");
            exit();
        }
        // get the file name and rename it to .old as we actually do not want to delete files.
        $db2->query("SELECT profileLocation FROM profiles WHERE id = :id");
        $db2->bind(':id', $id);
        $queryResultFileName = $db2->resultsetCols();
        //then rename the files
        rename($queryResultFileName[0], $queryResultFileName[0] . ".old");
        /* the query */
        $db2->query("UPDATE profiles SET status = 2 WHERE id = :id");
        $db2->bind(':id', $id);
        $queryResult = $db2->execute();
        if ($queryResult) {
            $log->Info("Success: Deleted profile in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete profile in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ /* Below is used for an ajax call from profiles update 
      jquery function to get row information to present back to profile edit form */ 
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
        $db2->query("SELECT profileName, profileDescription, profileLocation, deviceAccessMethodId, vendorId, status FROM profiles WHERE status = 1 AND id = :id");
        $db2->bind(':id', $id);
        $queryResult = $db2->resultset();
        $items = array();
        foreach ($queryResult as $row) {
            array_push($items, $row);
        }
        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}