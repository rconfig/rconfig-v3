<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");

$db  = new db();
$log = ADLog::getInstance();

/* Add snippets Here */

if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
// escaped variables
$snippetName = mysql_real_escape_string($_POST['snippetName']);
$snippetDesc = mysql_real_escape_string($_POST['snippetDesc']);
$snippet = strip_tags($_POST['snippet']);
$snippet = mysql_real_escape_string($snippet);

/* validations */

// validate snippetName field
if (empty($snippetName)) {
	$errors['snippetName'] = "Snippet Name field cannot be empty";
}

// validate snippetDesc field
if (empty($snippetDesc)) {
	$snippetDesc = $snippetName;
//	$errors['snippetDesc'] = "Snippet Description field cannot be empty";
}
	
// validate single snippet is not empty
if(empty($snippet)) {
	$errors['snippet'] = "Snippet cannot be empty";
}

// var_dump($_POST);die();
	
// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
if (!empty($errors)) {
	$errors['snippetNameVal'] = $snippetName;
	$errors['snippetDescVal'] = $snippetDesc;
	$errors['snippetVal'] = $_POST['snippet'];
    $_SESSION['errors'] = $errors;
    session_write_close();
    header("Location: " . $config_basedir . "snippet.php?errors&snippet=".$snippetValue);
    exit();
}

    if (empty($errors)) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        if (empty($_POST['editid'])) { // actual add because there is NOT an edit id value set
            
            // add snippet to compliancePolElem table
            $q = "INSERT INTO snippets
							(snippetName, snippetDesc, snippet) 
							VALUES 
							(	'" . $snippetName . "', 
								'" . $snippetDesc . "', 							
								'" . $snippet . "'
							)";

            if ($result = $db->q($q)) {
                $errors['Success'] = "Added Snippet to DB";
                $log->Info("Success: Added Snippet to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "snippets.php?success");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "snippets.php?errors&elem=".$snippetValue);
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
				header("Location: " . $config_basedir . "snippets.php?errors&snippet=".$snippetValue);
                exit();
            }
            
            $q = "UPDATE snippets SET 
					snippetName = '" . $snippetName . "', 
					snippetDesc = '" . $snippetDesc . "', 
					snippet = '" . $snippet . "'
					WHERE id = " . $id;
            
            if ($result = $db->q($q)) {

                // return success
                $errors['Success'] = "Edited Snippet '" . $snippetName . "' in Database";
                $log->Info("Success: Edited Snippet " . $snippetName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "snippets.php?errors");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "snippets.php?errors&snippet=".$snippetValue);
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
        header("Location: " . $config_basedir . "snippets.php?error");
        exit();
    }
    /* the query*/
    $q = "DELETE FROM snippets WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted Snippet in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete Snippet in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
		header("Location: " . $config_basedir . "snippets.php?errors");
        exit();
    }
    
    $q = $db->q("SELECT snippetName, snippetDesc, snippet
		FROM snippets
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
