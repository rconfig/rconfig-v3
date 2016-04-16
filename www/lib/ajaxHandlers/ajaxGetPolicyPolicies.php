<?php
/* this will retrieve previously saved queries on a per user basis - based on the userid */

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();
$q   = $db->q("SELECT r.polId, cp.policyName
					FROM complianceReportPolTbl as r
					LEFT JOIN compliancePolicies AS cp ON r.polId = cp.id
					WHERE reportId = '" . $_GET['id'] . "' 
					ORDER BY policyName ASC");

$return_arr = array();

while ($row = mysql_fetch_assoc($q)) {
    $row_array['polId']      = $row['polId'];
    $row_array['policyName'] = $row['policyName'];
    array_push($return_arr, $row_array);
}

// loop through results and create HTML for the 'selected' select box
$options = '';
for ( $i=0; $i < count($return_arr); $i++)

    { 
        $options .= '<option value='.$return_arr[$i]['polId'] .'>'.$return_arr[$i]['policyName'] .'</option>';
    } 
	
echo json_encode($options);

?> 