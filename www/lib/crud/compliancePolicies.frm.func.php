<?php
require_once("../classes/db.class.php");
require_once("../classes/ADLog.class.php");

function availableElems() {
    $db  = new db();
    $log = ADLog::getInstance();
    
    /*
     * Extract all Policy Elements for select list below
     */
    $q        = "SELECT id, elementName FROM compliancePolElem WHERE status = 1 ORDER BY elementName ASC";
    $result   = $db->q($q);
    $num_rows = mysql_numrows($result);
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying compliancePolElem options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for availableElems() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying availableElems() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id   = mysql_result($result, $i, "id");
        $elementName = mysql_result($result, $i, "elementName");
		echo "<option value=" . $id . ">" . $elementName . "</option>";
    }
}
?>