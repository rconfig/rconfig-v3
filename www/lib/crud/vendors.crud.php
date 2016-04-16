<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/imageResize.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();

/* Add Vendors Here */
if (isset($_POST['add'])) {
    session_start();
    $errors = array();
    
    
    if (!empty($_POST['vendorName'])) {
        /* Validate Input from Form */
        if (!ctype_alnum($_POST['vendorName'])) {
            $errors['vendorName'] = "Input was not a valid string - alphaNumeric Characters only!";
            $log->Warn("Failure: categoryName Input was not a valid string! (File: " . $_SERVER['PHP_SELF'] . ")");
        }
        
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
            session_write_close();
            header("Location: " . $config_basedir . "vendors.php?error");
            exit();
        } else {
            $vendorName = mysql_real_escape_string($_POST['vendorName']);
        }
        
        if (!empty($_FILES["vendorLogo"]["name"])) {
            if ((($_FILES["vendorLogo"]["type"] == "image/gif") || ($_FILES["vendorLogo"]["type"] == "image/jpeg") || ($_FILES["vendorLogo"]["type"] == "image/pjpeg")) && ($_FILES["vendorLogo"]["size"] < 20000)) {
                if ($_FILES["vendorLogo"]["error"] > 0) {
                    $errors['fileError'] = "File Error Return Code: " . $_FILES["vendorLogo"]["error"];
                    $log->Warn("File Error Return Code: " . $_FILES["vendorLogo"]["error"] . " (File: " . $_SERVER['PHP_SELF'] . ")");
                } else {
                    // echo "Upload: " . $_FILES["vendorLogo"]["name"] . "<br />";
                    // echo "Type: " . $_FILES["vendorLogo"]["type"] . "<br />";
                    // echo "Size: " . ($_FILES["vendorLogo"]["size"] / 1024) . " Kb<br />";
                    // echo "Temp file: " . $_FILES["vendorLogo"]["tmp_name"] . "<br />";
					$filename = $config_basedir . "images/vendor/" . $_FILES["vendorLogo"]["name"];
					$location = $config_web_basedir . "images/vendor/" . $_FILES["vendorLogo"]["name"];

                    if (file_exists($location)) {
                        $log->Warn("Failure: " . $_FILES["vendorLogo"]["name"] . " already exists (File: " . $_SERVER['PHP_SELF'] . ")");
                    } else {

						move_uploaded_file($_FILES['vendorLogo']['tmp_name'], $location);

							// *** 1) Initialize / load image  
							$resizeObj = new resize($location);  
							// *** 2) Resize image (options: exact, portrait, landscape, auto, crop)  
							$resizeObj -> resizeImage(16, 16, 'auto');  
							// *** 3) Save image  
							$resizeObj -> saveImage($location, 100);  
						
                    }
                }
            } else {
                $errors['fileInvalid'] = "Invalid File";
                $log->Warn("Failure: Invalid File(File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "vendors.php?error");
                exit();
            }
        } else {
            /* set location variable as defaultImg for later use in SQL statement, reason is user is not obliged to upload a file*/
            $filename = "images/logos/rconfig16.png";
        }
        /* end validate */
        
        /* Begin DB query. This will either be an Insert if $_POST editid is not set - or an edit/Update if editid is set in POST */
        if (empty($_POST['editid'])) { // do the add/ INSERT
            if (ctype_alnum($vendorName)) {
                $q = "INSERT INTO vendors
							(vendorName, 
							vendorLogo,
							status) 
							VALUES 
								('" . $vendorName . "', 
								' $filename ',				
								'1'
								)";
                if ($result = $db->q($q)) {
                    $errors['Success'] = "Added new vendor " . $vendorName . " to Database";
                    $log->Info("Success: Added new vendor, " . $vendorName . " to DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php");
                } else {
                    $errors['Fail'] = "ERROR: " . mysql_error();
                    $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php?error");
                    exit();
                }
            } else {
                $errors['vendorName'] = "Vendor Name Field was not a string";
                $log->Warn("Failure: vendorName was not a string (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "vendors.php?error");
                exit();
            }
        } else { // do the UPDATE/EDIT
            if (ctype_alnum($vendorName)) {
                $id = $_POST['editid'];
                $q  = "UPDATE vendors SET 
						vendorName = '" . $vendorName . "',
						vendorLogo = '" . $location . "'
						WHERE id = $id";
                echo $q;
                if ($result = $db->q($q)) {
                    $errors['Sucess'] = "Edited vendor " . $vendorName . " in Database";
                    $log->Info("Success: Edited vendor, " . $vendorName . " in DB (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php");
                } else {
                    $errors['Fail'] = "ERROR: " . mysql_error();
                    $log->Fatal("Fatal: " . mysql_error() . " (File: " . $_SERVER['PHP_SELF'] . ")");
                    $_SESSION['errors'] = $errors;
                    session_write_close();
                    header("Location: " . $config_basedir . "vendors.php?error");
                    exit();
                }
            } else {
                $errors['vendorName'] = "Vendor Name Field was not a string";
                $log->Warn("Failure: vendorName was not a string (File: " . $_SERVER['PHP_SELF'] . ")");
                $_SESSION['errors'] = $errors;
                session_write_close();
                header("Location: " . $config_basedir . "vendors.php?error");
                exit();
            }
        }
        /* end 'id' post check*/
        
    } else {
        $errors['vendorName'] = "Vendor Name Field cannot be empty";
        $log->Warn("Failure: vendorName was empty(File: " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "vendors.php?error");
        exit();
    }
}
/* end 'add' if*/

/* begin delete check */
elseif (isset($_POST['del'])) {
    if (ctype_digit($_POST['id'])) {
        $id = $_POST['id'];
    } else {
        $errors['Fail'] = "Fatal: id not of type int for getRow";
        $log->Fatal("Fatal: id not of type int for getRow - " . $_SERVER['PHP_SELF'] . ")");
        $_SESSION['errors'] = $errors;
        session_write_close();
        header("Location: " . $config_basedir . "vendors.php?error");
        exit();
    }
    /* the query*/
    $q = "UPDATE vendors SET status = 2 WHERE id = " . $id . ";";
    if ($result = $db->q($q)) {
        $log->Info("Success: Deleted vendor in DB (File: " . $_SERVER['PHP_SELF'] . ")");
        $response = json_encode(array(
            'success' => true
        ));
    } else {
        $log->Warn("Failure: Unable to delete vendor in DB (File: " . $_SERVER['PHP_SELF'] . ")");
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
			id,
			vendorName,
			vendorLogo
		FROM vendors
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