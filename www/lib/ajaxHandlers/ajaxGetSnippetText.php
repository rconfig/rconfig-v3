<?php
// Get Snippet from DB based on Snippet ID
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$id = $_GET['id'];
$db2->query("SELECT snippet FROM snippets WHERE id = :id");
$db2->bind(':id', $id); //bind here and create wildcard search term here also
$rows = $db2->resultsetCols();
echo json_encode($rows[0]); // send value in the array only