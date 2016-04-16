<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");

$db  = new db();
$log = ADLog::getInstance();

/* begin delete check */
if (isset($_POST['del'])) {
    $tid = $_POST['id'];
	
    /* the DB query*/
	// Set relevant row in MySQL databse table to a status of 2
    $delTaskQ    = "UPDATE generatedConfigs SET status = 2 WHERE id = " . $tid;

    if ($result = $db->q($delTaskQ)) {
        $log->Info("Success: Set generated config file " . $_POST['id'] . " in DB status to 2 (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to set generated config file " . $_POST['id'] . " in DB status to 2 (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
	
	// Pull the config file location (directory) and filename from the MySQL table where status is 2 (previously set above)
	$delFileSelectQ = "SELECT configLocation, configFilename FROM generatedConfigs WHERE status = 2";
	$delResult = $db->q($delFileSelectQ) or die ("ERROR: " .mysql_error());
	while ($delRow = mysql_fetch_assoc($delResult)) {
		// Delete the local file. Double quotes around directory and filename needed in case of spaces in filename
		exec('rm -rf "' . $delRow['configLocation'] . $delRow['configFilename'] . '"');
	}
	
	// Remove the file data from the MySQL database since it no longer exists
	$delStatusRow = "DELETE FROM generatedConfigs WHERE status = 2";
	if ($delStatusRowResult = $db->q($delStatusRow)) {
        $log->Info("Success: Deleted generated config file from database " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
	} else {
        $log->Warn("Failure: Unable to delete generated config file from database " . $_POST['id'] . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
    }
	
    echo $response;
    
} /* end 'delete' if*/

/* jquery function to get row information to present back to vendor edit form*/ 
elseif (isset($_GET['getRow']) && isset($_GET['id'])) {
    if (ctype_digit($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
		header("Location: " . $config_basedir . "templategenconfig.php?errors");
        exit();
    }
    
    $q = $db->q("SELECT configName, templateName, configDesc, newConfig, configLocation, configFilename, configDate
		FROM generatedConfigs
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