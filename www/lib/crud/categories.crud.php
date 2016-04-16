<?php
/* Add ../../classes and instantiate */
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();

/* Add Categories Here */

if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
    if (!empty($_POST['categoryName'])) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        /* Validate Input from Form */
        if (!ctype_alnum($_POST['categoryName'])) {
            $errors['categoryName'] = "Input was not a valid string!";
            $log->Warn("Failure: categoryName Input was not a valid string! (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "categories.php?error");
            exit();
        } else {
            $categoryName = mysql_real_escape_string($_POST['categoryName']);
        }
        /* end validate */
        
        if (empty($_POST['editid'])) { // becuase editid as set in form is empty, this is an Add and NOT an Edit
            
            $q = "INSERT INTO categories (categoryName, status) VALUES ('" . $categoryName . "', '1')";
            
            if ($result = $db->q($q)) {
                $errors['Success'] = "Added category to DB";
                $log->Info("Success: Added category to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "categories.php");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "categories.php?error");
                exit();
            }
            
        } else { // end empty_$POST['editid'] check : next section is an actual edit
            
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
            
            $q = "UPDATE categories SET categoryName = '" . $categoryName . "'	WHERE id = $id";
            
            if ($result = $db->q($q)) { // if Q was good, send back a sucess to the file
                $errors['Success'] = "Edited category to DB";
                $log->Info("Success: Edited category to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "categories.php");
                exit();
            } else { // else Q failed, send back an error
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "categories.php?error");
                exit();
            }
            
        }
        /* end 'id' post check*/
        
    } else { // categoryName was not filed in, and so end back error and kill script
        $errors['categoryName'] = "Category Field cannot be empty";
        $log->Warn("Failure: Category Name Field cannot be empty (File: " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "categories.php?error");
        exit();
    }
    
}
/* end 'add/editid' if*/


/* begin delete check */
elseif (isset($_POST['del'])) {
    if (ctype_digit($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for del";
        $log->Fatal("Fatal: id not of type int  for del - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "categories.php?error");
        exit();
    }
    
    /* the query*/
    $q = "UPDATE categories SET status = 2 WHERE id = " . $id . ";";
    
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted category in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete category in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'failure' => true
        ));
    }
    
    echo $response;
} /* end 'delete' if*/ 

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
    $q = $db->q("SELECT 
			id,
			categoryName
		FROM categories
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