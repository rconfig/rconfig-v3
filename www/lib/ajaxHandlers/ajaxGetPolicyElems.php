<?php
// Gets all Policy Elements for a given Policy ID
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2  = new db2();
$id = $_GET['id'];
$db2->query("SELECT e.elemId, cpe.elementName
                FROM compliancePolElemTbl as e
                LEFT JOIN compliancePolElem AS cpe ON e.elemId = cpe.id
                WHERE polId = :id
                ORDER BY elementName ASC");
$db2->bind(':id', $id); //bind here and create wildcard search term here also
//$db2->debugDumpParams();
$rows = $db2->resultset();

// loop through results and creates needed HTML for the renedered 'selected' select box
$options = '';
for ( $i=0; $i < count($rows); $i++)

    { 
        $options .= '<option value='.$rows[$i]['elemId'] .'>'.$rows[$i]['elementName'] .'</option>';
    } 
	
echo json_encode($options);
