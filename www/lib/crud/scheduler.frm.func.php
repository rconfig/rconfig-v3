<?php
require_once("../classes/db.class.php");
require_once("../classes/ADLog.class.php");

function reportsOptions() {
    $db  = new db();
    $log = ADLog::getInstance();
    
    /*
     * Extract all Policy Elements for select list below
     */
	$q 		= "SELECT id, reportsName,  reportsDesc
						FROM complianceReports 
						WHERE status = 1
						ORDER BY id ASC";    
	$result   = $db->q($q);
    $num_rows = mysql_numrows($result);
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying complianceReports options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for reportsOptions() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying reportsOptions() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id   = mysql_result($result, $i, "id");
        $reportsName = mysql_result($result, $i, "reportsName");
		echo "<option value=compliance-" . $id . ">" . $reportsName . "</option>";
    }
}

function snippetsOptions() {
    $db  = new db();
    $log = ADLog::getInstance();
    
    /*
     * Extract all snippets for select list below
     */
	$q 		= "SELECT id, snippetName
						FROM snippets 
						ORDER BY snippetName ASC";    
	$result   = $db->q($q);
    $num_rows = mysql_numrows($result);
    if (!$result || ($num_rows < 0)) {
        $log->Warn("Failure: Problem Displaying snippetsOptions() options (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Error displaying info for reportsOptions() function";
        return;
    }
    if ($num_rows == 0) {
        $log->Warn("Failure: Problem Displaying snippetsOptions() - no options returned (File: " . $_SERVER['PHP_SELF'] . ")");
        echo "Database table empty";
        return;
    }

    for ($i = 0; $i < $num_rows; $i++) {
        $id   = mysql_result($result, $i, "id");
        $snippetName = mysql_result($result, $i, "snippetName");
		echo "<option value=snippetId-" . $id . ">" . $snippetName . "</option>";
    }
}
?>