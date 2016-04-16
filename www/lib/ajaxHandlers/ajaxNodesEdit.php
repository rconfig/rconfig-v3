<?php
include("login/include/userdatabase.php");

$id = $_GET['id'];
/*
 * for the edit node, below query will be an UPDATE statement
 */
global $database;
$q      = mysql_query("SELECT 
			n.id,
			nm.nodeMakeName,
			n.deviceName,
			nc.nodeCatName,
			n.nodeIpAddr
		FROM nodes n
		LEFT OUTER JOIN nodesMake nm ON n.nodeMakeId = nm.id
		LEFT OUTER JOIN nodesCategory nc ON n.nodeCatId = nc.id
		WHERE n.status = 1 
		AND n.id = $id");
//$result = $database->query($q);
$result = array();
while ($row = mysql_fetch_object($q)) {
    array_push($result, $row);
}

echo json_encode($result);
?>
