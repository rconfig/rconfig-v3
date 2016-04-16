<?php
/* this will retrieve previously saved queries on a per user basis - based on the userid */

session_start();
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");

$db  = new db();
$log = ADLog::getInstance();
$q   = $db->q("SELECT e.elemId, cpe.elementName
					FROM compliancePolElemTbl as e
					LEFT JOIN compliancePolElem AS cpe ON e.elemId = cpe.id
					WHERE polId = '" . $_GET['id'] . "' 
					ORDER BY elementName ASC");

$return_arr = array();

while ($row = mysql_fetch_assoc($q)) {
    $row_array['elemId']      = $row['elemId'];
    $row_array['elementName'] = $row['elementName'];
    array_push($return_arr, $row_array);
}

// loop through results and create HTML for the 'selected' select box
$options = '';
for ( $i=0; $i < count($return_arr); $i++)

    { 
        $options .= '<option value='.$return_arr[$i]['elemId'] .'>'.$return_arr[$i]['elementName'] .'</option>';
    } 
	
echo json_encode($options);

?> 