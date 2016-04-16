<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");

$db  = new db();
$log = ADLog::getInstance();

/* SPECIAL NOTE - due to the use of Login System 2.0, i am using this ../../classes
features to add/ edit users from the DB. It incorportates ready made error handling, validation and emailing etc..

There are not any ADD or EDIT CRUD fuctions on this page

*/

/* begin delete check */
if (isset($_POST['delete'])) {

    if (ctype_digit($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "useradmin.php?error");
        exit();
    }
    
    /* the query*/
    $q = "UPDATE users SET status = 2 WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted user of ID: " . $id . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete user of ID: " . $id . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
    echo $response;
    
}
/* end 'delete' if*/

/* Below is used for an ajax call from vendors update 
jquery function to get row information to present back to vendor edit form*/
elseif (isset($_GET['getRow']) && isset($_GET['id'])) {

    if (ctype_digit($_GET['id'])) {
        $id = $_GET['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "useradmin.php?error");
        exit();
    }

    $q     = $db->q("SELECT 
			id,
			username,
			email,
			userlevel
		FROM users
		WHERE status = 1
		AND id = $id");
    $items = array();
    while ($row = mysql_fetch_assoc($q)) {
        array_push($items, $row);
    }
    $result["rows"] = $items;
    echo json_encode($result);
}
/* end GetId */
?>