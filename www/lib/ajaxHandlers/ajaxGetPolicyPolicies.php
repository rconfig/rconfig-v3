<?php
// Gets all Policies for a given Policy Report ID

session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2  = new db2();
$log = ADLog::getInstance();
$q   = $db2->q("SELECT r.polId, cp.policyName
                FROM complianceReportPolTbl as r
                LEFT JOIN compliancePolicies AS cp ON r.polId = cp.id
                WHERE reportId = '" . $_GET['id'] . "' 
                ORDER BY policyName ASC");

// loop through results and create HTML for the 'selected' select box
$options = '';
for ( $i=0; $i < count($q); $i++)

    { 
        $options .= '<option value='.$q[$i]['polId'] .'>'.$q[$i]['policyName'] .'</option>';
    } 
	
echo json_encode($options);