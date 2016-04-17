<?php
// Gets all Policy Elements for a given Policy ID
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db2  = new db2();
$log = ADLog::getInstance();
$q   = $db2->q("SELECT e.elemId, cpe.elementName
                FROM compliancePolElemTbl as e
                LEFT JOIN compliancePolElem AS cpe ON e.elemId = cpe.id
                WHERE polId = '" . $_GET['id'] . "' 
                ORDER BY elementName ASC");


// loop through results and creates needed HTML for the renedered 'selected' select box
$options = '';
for ( $i=0; $i < count($q); $i++)

    { 
        $options .= '<option value='.$q[$i]['elemId'] .'>'.$q[$i]['elementName'] .'</option>';
    } 
	
echo json_encode($options);