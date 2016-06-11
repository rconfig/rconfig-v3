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

    $db2 = new db2();
    $log = ADLog::getInstance();

    /* Add Custom Property Here */
    if (isset($_POST['add'])) {
        $errors = array();

        // validate deviceName field
        if (!empty($_POST['deviceName'])) {
            $deviceName = $_POST['deviceName'];
            // check device name for whitespace
            if (!chkWhiteSpaceInStr($deviceName) === false) {
                $errors['deviceName'] = "Device Name cannot contain spaces";
                $log->Warn("Failure: Device Name cannot contain spaces (File: " . $_SERVER['PHP_SELF'] . ")");
                $deviceName = ""; // set back to blank so text with spaces is not returned to devices form
            }
        } else {
            $errors['deviceName'] = "Device Name cannot be empty";
            $log->Warn("Failure: Device Name cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        if (!empty($_POST['deviceIpAddr'])) {
            // validate deviceIpAddr IP address
            if (!filter_var($_POST['deviceIpAddr'], FILTER_VALIDATE_IP)) {
                $errors['deviceIpAddr'] = "IP Address is not valid ";
                $log->Warn("Failure: IP Address is not valid (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $deviceIpAddr = $_POST['deviceIpAddr'];
            }
        } else {
            $errors['deviceIpAddr'] = "IP Address cannot be empty ";
            $log->Warn("Failure: IP Address cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate devicePrompt field
        if (!empty($_POST['devicePrompt'])) {
            $devicePrompt = str_replace(' ', '', $_POST['devicePrompt']);
        } else {
            $errors['devicePrompt'] = "Device Prompt cannot be empty";
            $log->Warn("Failure: Device Prompt cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate vendorId field
        if (!empty($_POST['vendorId']) && ctype_digit($_POST['vendorId'])) {
            $vendorId = $_POST['vendorId'];
        } else {
            $errors['vendorId'] = "Vendor field cannot be empty";
            $log->Warn("Failure: Vendor Field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate deviceModel field
        if (!empty($_POST['deviceModel'])) {
            $deviceModel = $_POST['deviceModel'];
        } else {
            $errors['deviceModel'] = "Device Model cannot be empty";
            $log->Warn("Failure: Device Model cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate defaultCreds check boxes
        if (isset($_POST['defaultCreds'])) {
            $defaultCreds = '1';
        } else {
            $defaultCreds = '0';
        }

        // validate deviceUsername field
        if (!empty($_POST['deviceUsername']) && is_string($_POST['deviceUsername'])) {
            $deviceUsername = $_POST['deviceUsername'];
        } else {
            $errors['deviceUsername'] = "Username cannot be empty";
            $log->Warn("Failure: Username field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate devicePassword field
        if (!empty($_POST['devicePassword']) && is_string($_POST['devicePassword'])) {
            $devicePassword = $_POST['devicePassword'];
        } else {
            $errors['devicePassword'] = "Password cannot be empty";
            $log->Warn("Failure: Password field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate devicePassConf field
        if (!empty($_POST['devicePassConf']) && is_string($_POST['devicePassConf'])) {
            if ($_POST['devicePassConf'] !== $_POST['devicePassword']) {
                $errors['devicePassConf'] = "Passwords to not match";
                $log->Warn("Failure: Passwords to not match (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $devicePassConf = $_POST['devicePassConf'];
            }
        } else {
            $errors['devicePassword'] = "Confirm Password field cannot be empty";
            $log->Warn("Failure: Confirm Password field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // if 'deviceEnableMode' is checked - deviceEnablePassword field must be populated
        if (isset($_POST['deviceEnableMode'])) {
            if ($_POST['deviceEnableMode'] == 'on' && empty($_POST['deviceEnablePassword'])) {
                $errors['deviceEnableMode'] = "Enable mode checked but password was not entered";
                $log->Warn("Failure: Enable mode checked but password was not entered (File: " . $_SERVER['PHP_SELF'] . ")");
            } else {
                $deviceEnableMode = $_POST['deviceEnableMode'];
                $deviceEnablePassword = $_POST['deviceEnablePassword'];
            }
        } else {
            $deviceEnableMode = 'off';
            $deviceEnablePassword = '';
        }

        // validate catId field
        if (ctype_digit($_POST['catId'])) {
            $catId = $_POST['catId'];
        } else {
            $errors['catId'] = "Category field cannot be empty";
            $log->Warn("Failure: Category field did not pass numeric value i.e. catId OR awas empty (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        if (isset($_POST['username'])) {
            $username = $_POST['username'];
        } else {
            $errors['username'] = "Username passed to devices.crud.php was not valid";
            $log->Warn("Failure: Username passed to devices.crud.php was not valid (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        /* See if category is added to any scheduled tasks and get correct column name if it is */
        $db2->query("SELECT id, catId FROM tasks WHERE status = '1'");
        $resultCatSelect = $db2->resultset();
        $taskIdColumns = '';
        $taskValue = '';
        foreach ($resultCatSelect as $taskRow) {
            if (!empty($taskRow['catId']) && in_array($catId, unserialize($taskRow['catId']))) {
                $taskIdColumns .= "taskId" . $taskRow['id'] . ", ";
                $taskValue .= "'1',";
            }
        }

        // add query variables
        $taskIdColumns = rtrim($taskIdColumns, ","); // format values for Query 
        $taskValue = rtrim($taskValue, ","); // remove trailing comma for building the SQL query
        // validate deviceAccessMethodId field
        if (ctype_digit($_POST['deviceAccessMethodId'])) {
            $deviceAccessMethodId = $_POST['deviceAccessMethodId'];
        } else {
            $errors['deviceAccessMethodId'] = "You must select telnet or SSH";
            $log->Warn("Failure: deviceAccessMethodId input is incorrect (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        // validate connPort field
        if (ctype_digit($_POST['connPort'])) {
            $connPort = $_POST['connPort'];
        } else {
            $errors['connPort'] = "connPort input is incorrect";
            $log->Warn("Failure: connPort input is incorrect (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        /* No validation on Custom_ Fields */

        // set the session id if any errors occur and redirect back to devices page with ?error set for JS on that page to keep form open 
        if (!empty($errors)) {
            if (isset($deviceName)) {
                $_SESSION['deviceName'] = $deviceName;
            }
            if (isset($deviceIpAddr)) {
                $_SESSION['deviceIpAddr'] = $deviceIpAddr;
            }
            if (isset($devicePrompt)) {
                $_SESSION['devicePrompt'] = $devicePrompt;
            }
            if (isset($vendorId)) {
                $_SESSION['vendorId'] = $vendorId;
            }
            if (isset($deviceModel)) {
                $_SESSION['deviceModel'] = $deviceModel;
            }
            if (isset($defaultCreds)) {
                $_SESSION['defaultCreds'] = $defaultCreds;
            }
            if (isset($deviceUsername)) {
                $_SESSION['deviceUsername'] = $deviceUsername;
            }
            if (isset($devicePassword)) {
                $_SESSION['devicePassword'] = $devicePassword;
            }
            if (isset($devicePassConf)) {
                $_SESSION['devicePassConf'] = $devicePassConf;
            }
            if (isset($deviceEnableMode)) {
                $_SESSION['deviceEnableMode'] = $deviceEnableMode;
            }
            if (isset($deviceEnablePassword)) {
                $_SESSION['deviceEnablePassword'] = $deviceEnablePassword;
            }
            if (isset($catId)) {
                $_SESSION['catId'] = $catId;
            }
            if (isset($deviceAccessMethodId)) {
                $_SESSION['deviceAccessMethodId'] = $deviceAccessMethodId;
            }
            if (isset($connPort)) {
                $_SESSION['connPort'] = $connPort;
            }

            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "devices.php?error");
            exit();
        } else {
            // Search POST for any key with partial string 'custom_' to get the names of the 
            // custom fields column names in DB
            $custom_results = array();
            foreach ($_POST as $k => $v) {
                if (strstr($k, 'custom_')) {
                    // create new 'custom_results' array with key and values from the post matching 'custom'
                    $custom_results[$k] = $v;
                }
            }

            /* http://php.net/manual/en/function.extract.php */
            /* Extract Keys as Column Names for dynamic Query
             * and extract values as DB values for dynamic query */
            $dynamicValues = array();
            $dynamicTbls = array();
            $customPropEditQueryStr = '';
            foreach ($custom_results as $key => $value) {
                $customPropEditQueryStr .= $key . " = " . "'" . $value . "', "; // create the edit query for any custom properties fields
                array_push($dynamicValues, $value);
                array_push($dynamicTbls, $key);
            }
            // Output above arrays to simple string variables for use in the query
            $dynamicValuesBlk = implode("', '", $dynamicValues);
            $dynamicTblsBlk = implode(", ", $dynamicTbls);

            // create part of the UPDATE query for custom_ fields
            $customPropQueryStr = "";
            foreach ($custom_results as $k => $v) {
                $customPropQueryStr = $customPropQueryStr . $k . " = '" . $v . "', ";
            }

            // next if vars are not empty, add a comma to complete SQL statement
            // because if no custom props, or Tasks added errors will occur
            if (!empty($dynamicTblsBlk)) {
                $dynamicTblsBlk = $dynamicTblsBlk . ",";
                if (empty($dynamicValuesBlk)) {
                    $dynamicValuesBlk = "NULL" . ",";
                } else {
                    if (!empty($dynamicValuesBlk)) {
                        $dynamicValuesBlk = "'" . $dynamicValuesBlk . "',";
                    }
                }
            }

            if (!empty($taskIdColumns)) {
//            $taskIdColumns = $taskIdColumns . ",";
            }
            if (!empty($taskValue)) {
                $taskValue = $taskValue . ",";
            } else {
                $taskValue = '';
            }
            /* Begin DB query. This will either be an Insert if $_POST ID is not set - or an edit/Update if ID is set in POST */
            if (empty($_POST['editid'])) {
                $db2->query("INSERT INTO nodes
            (deviceName, 
            deviceIpAddr,
            devicePrompt,
            deviceUsername,
            devicePassword,
            deviceEnableMode,
            deviceEnablePassword,
            deviceAccessMethodId,
            connPort,
            model,
            vendorId,
            nodeCatId,
            nodeAddedBy,
            defaultCreds,
            " . $dynamicTblsBlk . "
            " . $taskIdColumns . "
            deviceDateAdded,
            status
            )
            VALUES 
                (:deviceName,
                :deviceIpAddr,
                :devicePrompt,
                :deviceUsername,
                :devicePassword,
                :deviceEnableMode,
                :deviceEnablePassword,
                :deviceAccessMethodId,
                :connPort,
                :deviceModel,
                :vendorId,
                :catId,
                :username,
                :defaultCreds,
                $dynamicValuesBlk
                $taskValue
                CURDATE(),
                '1')");
                $db2->bind(':deviceName', $deviceName);
                $db2->bind(':deviceIpAddr', $deviceIpAddr);
                $db2->bind(':devicePrompt', $devicePrompt);
                $db2->bind(':deviceUsername', $deviceUsername);
                $db2->bind(':devicePassword', $devicePassword);
                $db2->bind(':deviceEnableMode', $deviceEnableMode);
                $db2->bind(':deviceEnablePassword', $deviceEnablePassword);
                $db2->bind(':deviceAccessMethodId', $deviceAccessMethodId);
                $db2->bind(':connPort', $connPort);
                $db2->bind(':deviceModel', $deviceModel);
                $db2->bind(':vendorId', $vendorId);
                $db2->bind(':catId', $catId);
                $db2->bind(':username', $username);
                $db2->bind(':defaultCreds', $defaultCreds);
                $db2->debugDumpParams();
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $errors['Success'] = "Added new device " . $deviceName . " to Database";
                    $log->Info("Success: Added new device, " . $deviceName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php");
                } else {
                    $errors['Fail'] = "ERROR: Could not Insert Record to Database, Check Logs";
                    $log->Fatal("Fatal: Error executing Node Insert (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php?error");
                    exit();
                }
            } else { // if ID is set in post when running a save from the form do an UPDATE
                $id = $_POST['editid'];
                // reset all taskID*  columns to 2 before updating them in the UPDATE query for correct taskId assignments
                $db2->query("SELECT column_name FROM information_schema.COLUMNS c
                        WHERE c.table_schema = 'rconfig35' 
                        AND c.table_name='nodes' 
                        AND c.column_name LIKE '%taskId%'");
                $taskColNames = $db2->resultsetCols();
                foreach ($taskColNames as $taskColName) {
                    $db2->query("UPDATE nodes SET " . $taskColName . " = 2 WHERE id = :id");
                    $db2->bind(":id", $id);
                    $db2->execute();
                }
                // updated taskId Columns partial query for the update statement
                //  explode to array for previously built string. array_filter to remove blanks
                // array_flip to move all values from $taskIdColumns to key names

                $taskIdColumns = rtrim($taskIdColumns, ", "); // delete last comma and space
                $taskIdColumns = array_flip(array_filter(explode(",", $taskIdColumns)));
                // set all values in array to 1 as these are the correct taskId assignments for this device when updating
                $taskIdColumns = array_fill_keys(array_keys($taskIdColumns), 1);
                //iterate over $taskIdColumn array and chnage it to a string for the sql update
                $prefix = '';
                $taskIdColumnList = '';
                foreach ($taskIdColumns as $taskIdColumnK => $taskIdColumnV) {
                    $taskIdColumnList .= $prefix . $taskIdColumnK . ' = ' . $taskIdColumnV;
                    $prefix = ', ';
                }
                // add a trailing comma if the $taskIdColumnList is not empty, otherwise the query is broken
                if ($taskIdColumnList != '') {
                    $taskIdColumnList = $taskIdColumnList . ',';
                }
                $db2->query("UPDATE nodes SET 
                            deviceName = :deviceName,
                            deviceIpAddr = :deviceIpAddr,
                            devicePrompt = :devicePrompt,
                            deviceUsername = :deviceUsername, 
                            devicePassword = :devicePassword, 
                            deviceEnableMode = :deviceEnableMode, 
                            deviceEnablePassword = :deviceEnablePassword, 
                            deviceAccessMethodId = :deviceAccessMethodId,
                            connPort = :connPort, 
                            model = :deviceModel, 
                            vendorId = :vendorId, 
                            nodeCatId = :catId, 
                            defaultCreds = :defaultCreds,
                            $customPropEditQueryStr
                            $taskIdColumnList
                            deviceDateAdded = CURDATE()
                            WHERE id = :id");
                $db2->bind(':deviceName', $deviceName);
                $db2->bind(':deviceIpAddr', $deviceIpAddr);
                $db2->bind(':devicePrompt', $devicePrompt);
                $db2->bind(':deviceUsername', $deviceUsername);
                $db2->bind(':devicePassword', $devicePassword);
                $db2->bind(':deviceEnableMode', $deviceEnableMode);
                $db2->bind(':deviceEnablePassword', $deviceEnablePassword);
                $db2->bind(':deviceAccessMethodId', $deviceAccessMethodId);
                $db2->bind(':connPort', $connPort);
                $db2->bind(':deviceModel', $deviceModel);
                $db2->bind(':vendorId', $vendorId);
                $db2->bind(':catId', $catId);
                $db2->bind(':defaultCreds', $defaultCreds);
                $db2->bind(':id', $id);
                $queryResult = $db2->execute();
                if ($queryResult) {
                    $errors['Success'] = "Edit device " . $deviceName . " successful";
                    $log->Info("Success: Edit device " . $deviceName . " in DB successful (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php");
                } else {
                    $errors['Fail'] = "ERROR: Could not Edit device " . $deviceName;
                    $log->Fatal("Fatal: ERROR: Could not Edit device " . $deviceName . " (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "devices.php?error");
                    exit();
                }
            }
            /* end check if 'id' is iset in input field */
        }
        /* end '!empty($errors)' check */
    }
    /* end 'add' if */


    /* begin delete check */ elseif (isset($_POST['del'])) {

        /* the query */
        $q = "UPDATE nodes SET status = 2 WHERE id = " . $_POST['id'] . ";";
        $db2->query("UPDATE nodes SET status = 2 WHERE id = :id");
        $db2->bind(':id', $_POST['id']);
        $result = $db2->execute();
        if ($result) {
            $log->Info("Success: Deleted Node ID = " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'success' => true
            ));
        } else {
            $log->Warn("Failure: Unable to delete node id:" . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
            $response = json_encode(array(
                'failure' => true
            ));
        }
        echo $response;
    } /* end 'delete' if */ elseif (isset($_GET['getRow']) && isset($_GET['id'])) {

        if (ctype_digit($_GET['id'])) {
            $id = $_GET['id'];
        } else {
            $errors['Fail'] = "Fatal: id not of type int for getRow";
            $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "devices.php?error");
            exit();
        }


        /* first get custom fieldnames  and impode to create part of final SQL query */
        $db2->query("SELECT customProperty FROM customProperties");
        $qCustProp = $db2->resultset();
        $items = array();
        foreach ($qCustProp as $row) {
            $customProperty = $row['customProperty'];
            array_push($items, $customProperty);
            $customProp_string = implode(", ", $items) . ', ';
        }

        $db2->query("SELECT 
                    n.id,
                    n.deviceName,
                    n.deviceIpAddr,
                    n.devicePrompt,
                    v.id vendorId,
                    n.model,
                    n.defaultCreds,
                    n.deviceUsername,
                    n.devicePassword,
                    n.deviceEnableMode,
                    n.deviceEnablePassword,
                    n.termLength,
                    a.id accessMeth,
                    n. connPort,
                    " . $customProp_string . "
                    cat.id catId
		FROM nodes n
		LEFT OUTER JOIN vendors v ON n.vendorId = v.id
		LEFT OUTER JOIN categories c ON n.nodeCatId = c.id
		LEFT OUTER JOIN devicesaccessmethod a ON n.deviceAccessMethodId = a.id
		LEFT OUTER JOIN categories cat ON n.nodeCatId = cat.id
		WHERE n.status = 1
		AND n.id = :id");
        $db2->bind(':id', $id);
        $qSelectnodeData = $db2->resultset();

        $items = array();
        foreach ($qSelectnodeData as $row) {
            array_push($items, $row);
        }
        $result["rows"] = $items;
        echo json_encode($result);
    }
    /* end GetId */
}