<?php
// Gets all Policies for a given Policy Report ID
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$id = $_GET['id'];
$db2->query("SELECT r.polId, cp.policyName
                FROM complianceReportPolTbl as r
                LEFT JOIN compliancePolicies AS cp ON r.polId = cp.id
                WHERE reportId = :id 
                ORDER BY policyName ASC");
$db2->bind(':id', $id); //bind here and create wildcard search term here also
//$db2->debugDumpParams();
$rows = $db2->resultset();
// loop through results and create HTML for the 'selected' select box
$options = '';
for ( $i=0; $i < count($rows); $i++)

    { 
        $options .= '<option value='.$rows[$i]['polId'] .'>'.$rows[$i]['policyName'] .'</option>';
    } 
echo json_encode($options);