<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");
include "templategenerator.crud.php";
include "templateparser.crud.php";

$db  = new db();
$log = ADLog::getInstance();

/* Add config templates Here */

if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
// escaped variables
$templateName = mysql_real_escape_string($_POST['templateName']);
$templateDesc = mysql_real_escape_string($_POST['templateDesc']);
$template = strip_tags($_POST['template']);
$template = mysql_real_escape_string($template);
$templateVars = strip_tags($_POST['templateVars']);
$templateVars = mysql_real_escape_string($templateVars);
$newConfigName = strip_tags($_POST['newConfigName']);
$newConfigName = mysql_real_escape_string($newConfigName);
$templateVarSubsRaw = $_POST['templateVarSubs'];
$templateVarSubs = strip_tags($templateVarSubsRaw);
$templateVarSubs = mysql_real_escape_string($templateVarSubs);

/* validations *** No longer needed ***

// validate templateName field
if (empty($templateName) && empty($_POST['genid'])) {
	$errors['templateName'] = "Template Name field cannot be empty";
}

// validate templateDesc field
if (empty($templateDesc) && empty($_POST['genid'])) {
	$templateDesc = $templateName;
//	$errors['templateDesc'] = "Template Description field cannot be empty";
}
	
// validate single template is not empty
if(empty($template) && empty($_POST['genid'])) {
	$errors['template'] = "Template cannot be empty";
}

// validate templateVarSubs field
if (empty($templateVarSubs) && !empty($_POST['genid'])) {
	$errors['templateVarSubs'] = "Variable Substitution field cannot be empty";
}
// validate newConfigName field
if (empty($newConfigName) && !empty($_POST['genid'])) {
	$errors['newConfigName'] = "New Config Name field cannot be empty";
} */

/* Validation for variable substitution count matches detected variable count by script
	** need to fix **
if (!empty($_POST['genid'])) {
	if (verifySubstitutionCount(templateParser($template), $templateVarSubs)){
		$errors['templateVarSubs'] = "Number of substituted variables must match variable count in config template";
	}
}
*/

// var_dump($_POST);die();
	
// set the session id if any errors occur and redirect back to devices page with ?error and update fields 
if (!empty($errors)) {
	$errors['templateNameVal'] = $templateName;
	$errors['templateDescVal'] = $templateDesc;
	$errors['templateVal'] = $_POST['template'];
	$errors['newConfigNameVal'] = $newConfigName;
	$errors['templateVarSubsVal'] = $_POST['templateVarSubs'];
    $_SESSION['errors'] = $errors;
    session_write_close();
    header("Location: " . $config_basedir . "templategen.php?errors&template=".$templateValue);
    exit();
}

    if (empty($errors)) {
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        
        if (empty($_POST['editid']) && empty($_POST['genid'])) { // add new template
			
			/* Call parsing file/function to parse out variables */
			$parsedTemplateSymbols = templateParserSymbols($template);
			$parsedTemplate = templateParser($template);
			
            // add template to compliancePolElem table
            $q = "INSERT INTO configtemplates
							(templateName, templateDesc, template, templateVars, templateVarSyms)
							VALUES 
							(	'" . $templateName . "', 
								'" . $templateDesc . "', 							
								'" . $template . "',
								'" . $parsedTemplate . "',
								'" . $parsedTemplateSymbols . "'
							)";

            if ($result = $db->q($q)) {
                $errors['Success'] = "Added Template to DB";
                $log->Info("Success: Added Template to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "templategen.php?success");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "templategen.php?errors&elem=".$templateValue);
                exit();
            }

        } elseif (empty($_POST['editid']) && !empty($_POST['genid'])) { // generate new config from existing template
			
            /* validate genid is numeric */
            if (ctype_digit($_POST['genid'])) {
                $id = $_POST['genid'];
            } else {
                $errors['Fail'] = "Fatal: genid not of type int for edit";
                $log->Fatal("Fatal: genid not of type int for edit - " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "templategen.php?errors&template=".$templateValue);
                exit();
            }
			
			// Query template and variables with symbols from DB
			$q = mysql_query("SELECT template, templateVarSyms FROM configtemplates WHERE id = " . $id);
			$row = mysql_fetch_array($q);
			$template = $row['template'];
			$parsedTemplateSymbols = $row['templateVarSyms'];
			
			// Call config generating function from templategenerator.crud.php to generate new config based off template and substituted variables, then save as text file
			$newTemplate = generateNewConfig($template, $parsedTemplateSymbols, $templateVarSubs);
			
			/*$q = "UPDATE configtemplates SET
					templateVarSubs = '" . $templateVarSubs . "',
					newTemplate = '" . $newTemplate . "'
					WHERE id = " . $id;*/
					
			$q = "INSERT INTO generatedConfigs
					(configName, templateName, linkedId, newConfig, configLocation, configFilename, configDate, status)
					VALUES
					(	'" . $newConfigName . "',
						'" . $templateName . "',
						'" . $id . "',
						'" . $newTemplate . "',
						'/home/rconfig/www/templateconfigs/',
						'" . $newConfigName . ".txt',
						NOW(),
						'1'
					)";
					
			$file = fopen('/home/rconfig/www/templateconfigs/'.$newConfigName.'.txt', "w");
			fwrite($file, $newTemplate);
			fclose($file);
							
			if ($result = $db->q($q) /*&& $result1 = $db->q($qq)*/) {
				// return success
				$errors['Success'] = "Generated config Template '" . $templateName . "' in Database";
				$log->Info("Success: Generated config Template " . $templateName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
				$_SESSION['errors'] = $errors;
				session_write_close();
				header("Location: " . $config_basedir . "templategen.php?success");
				exit();
			} else {
				$errors['Fail'] = "ERROR: " . mysql_error();
				$log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
				$_SESSION['errors'] = $errors;
				session_write_close();
				header("Location: " . $config_basedir . "templategen.php?errors&template=".$templateValue);
				exit();
			}
			
		} elseif (!empty($_POST['editid']) && empty($_POST['genid'])) { // edit existing template
            
            /* validate editid is numeric */
            if (ctype_digit($_POST['editid'])) {
                $id = $_POST['editid'];
            } else {
                $errors['Fail'] = "Fatal: editid not of type int for edit";
                $log->Fatal("Fatal: editid not of type int for edit - " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "templategen.php?errors&template=".$templateValue);
                exit();
            }
			
			/* Call parsing file/function to parse out variables */
			$parsedTemplateSymbols = templateParserSymbols($template);
			$parsedTemplate = templateParser($template);
			
            $q = "UPDATE configtemplates SET 
					templateName = '" . $templateName . "', 
					templateDesc = '" . $templateDesc . "', 
					template = '" . $template . "',
					templateVars = '" .$parsedTemplate . "',
					templateVarSyms = '" .$parsedTemplateSymbols . "'
					WHERE id = " . $id;
            
            if ($result = $db->q($q)) {
                // return success
                $errors['Success'] = "Edited Template '" . $templateName . "' in Database";
                $log->Info("Success: Edited Template " . $templateName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "templategen.php?success");
                exit();
            } else {
                $errors['Fail'] = "ERROR: " . mysql_error();
                $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
				header("Location: " . $config_basedir . "templategen.php?errors&template=".$templateValue);
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
        header("Location: " . $config_basedir . "templategen.php?error");
        exit();
    }
    /* the query*/
    $q = "DELETE FROM configtemplates WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted Template in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete Template in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
		header("Location: " . $config_basedir . "templategen.php?errors");
        exit();
    }
    
    $q = $db->q("SELECT templateName, templateDesc, template, templateVars, templateVarSubs
		FROM configtemplates
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