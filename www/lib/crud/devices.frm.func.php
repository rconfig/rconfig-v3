<?php

require_once("../classes/db2.class.php");
require_once("../classes/ADLog.class.php");
require_once("/home/rconfig/config/functions.inc.php");

function myFilter($string) {
  return strpos($string, '.old') === false;
}

function profileSelect() {
    // load profile files from /home/rconfig/classes/connectionProfiles/ into option select for devices page
    $sshdir = '/home/rconfig/classes/connectionProfiles/ssh';
    $telnetdir = '/home/rconfig/classes/connectionProfiles/telnet';
    $ssh_scanned_directory = array_values(array_diff(scandir($sshdir), array('..', '.')));
    $ssh_scanned_directory = array_filter($ssh_scanned_directory, 'myFilter'); // remove .old files from view
    $telnet_scanned_directory = array_values(array_diff(scandir($telnetdir), array('..', '.')));
    $telnet_scanned_directory = array_filter($telnet_scanned_directory, 'myFilter'); // remove .old files from view
    echo "<option value=\"\">Select a profile</option>";
    if ($ssh_scanned_directory) {
        echo "<option value=\"\">--- SSH Profiles ---</option>";

        foreach ($ssh_scanned_directory as $sshK => $sshV) {
            echo "<option value=\"connectionProfiles/ssh/" . $sshV . "\">" . $sshV . "</option>";
        }

        if ($telnet_scanned_directory) {
            echo "<option value=\"\">--- Telnet Profiles ---</option>";
            foreach ($telnet_scanned_directory as $telnetK => $telnetV) {
                echo "<option value=\"connectionProfiles/telnet/" . $telnetV . "\">" . $telnetV . "</option>";
            }
        }
    }
}

function vendorId($id = null) {
    // $id is set if from is reloaded with errors so that selected item is pre-populated after form reload
    $db2 = new db2();
    $log = ADLog::getInstance();

    //Extract Nodes vendorId for select list below
    $db2->query("SELECT * FROM vendors WHERE status = 1 ORDER BY vendorname ASC");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying vendorId options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying vendorId - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }
    if ($id == null) {
        echo "<option value=\"\" selected></option>";
    } else {
        echo "<option value=\"\"></option>";
    }
    for ($i = 0; $i < $num_rows; $i++) {
        $vendorId = $result[$i]['id'];
        $vendorName = $result[$i]['vendorName'];
        if ($id == $vendorId && $id != null) {
            echo "<option value=" . $vendorId . " selected>" . $vendorName . "</option>";
        } else {
            echo "<option value=" . $vendorId . ">" . $vendorName . "</option>";
        }
    }
}

function accessMethod($id = null) {
// $id is set if from is reploaded with errors so that selected item is pre-populated after form reload

    $db2 = new db2();
    $log = ADLog::getInstance();
    //Extract Nodes accessMethod for select list below
    $db2->query("SELECT * FROM devicesaccessmethod");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying devicesaccessmethod options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying devicesaccessmethod - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }
    if ($id == null) {
        echo "<option selected=\"selected\" value=\"\">- Select an option -</option>";
    } else {
        echo "<option value=\"\">- Select an option -</option>";
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $accessId = $result[$i]['id'];
        $accessName = $result[$i]['devicesAccessMethod'];
        if ($id == $accessId && $id != null) {
            echo "<option value=" . $accessId . " selected>" . $accessName . "</option>";
        } else {
            echo "<option value=" . $accessId . ">" . $accessName . "</option>";
        }
    }
}

function categories($id = null) {
    // $id is set if from is reloaded with errors so that selected item is pre-populated after form reload	
    $db2 = new db2();
    $log = ADLog::getInstance();
    //Extract Categories for select list below

    $db2->query("SELECT * FROM categories WHERE status = 1");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();
    if (!$result || ($num_rows < 0)) {
        echo "Error displaying info";
        $log->Warn("Failure: Problem Displaying categories options (File: " . $_SERVER['PHP_SELF'] . ")");
        return;
    }
    if ($num_rows == 0) {
        echo "Database table empty";
        $log->Warn("Failure: Database table returned empty on categories - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        return;
    }
    if ($id == null) {
        echo "<option value=\"\" selected>Select a Category </option>";
    } else {
        echo "<option value=\"\">Select a Category </option>";
    }
    for ($i = 0; $i < $num_rows; $i++) {
        $catId = $result[$i]['id'];
        $catName = $result[$i]['categoryName'];
        if ($id == $catId && $id != null) {
            echo "<option value=" . $catId . " selected>" . $catName . "</option>";
        } else {
            echo "<option value=" . $catId . ">" . $catName . "</option>";
        }
    }
}

// end categories function
function customProp() {
    $db2 = new db2();
    $db2->query("SELECT * FROM customProperties");
    $result = $db2->resultset();
    $num_rows = $db2->rowCount();

    for ($i = 0; $i < $num_rows; $i++) {
        $custprop = $result[$i]['customProperty'];
        // remove 'custom_' bit for display purposes
        $newcustprop = substr($custprop, 7);
        echo "<label>" . $newcustprop . ":</label>  
            <input type=\"text\" name=\"$custprop\" id=\"$custprop\" tabindex=12  style=\"width:150px;\"/> ";
    }
}

// end custom properties function