<?php
require_once("../classes/db.class.php");
require_once("../classes/ADLog.class.php");

function availablePolicies() {
    $db  = new db();
    $log = ADLog::getInstance();
    
    /*
     * Extract all Policy Elements for select list below
     */
    $q        = "SELECT id, policyName FROM compliancePolicies WHERE status = 1 ORDER BY policyName ASC";
    $result   = $db->q($q);
    $num_rows = mysql_numrows($result);
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying compliancePolicies options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for availablePolicies() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying availablePolicies() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id   = mysql_result($result, $i, "id");
        $policyName = mysql_result($result, $i, "policyName");
		echo "<option value=" . $id . ">" . $policyName . "</option>";
    }
}
?>