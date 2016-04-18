<?php
/* this will retrieve commands based on submitted CatId */
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$catId = $_GET['catId'];
$db2->query("SELECT id, command FROM configcommands 
                WHERE id IN (SELECT DISTINCT configCmdId 
                FROM cmdCatTbl 
                WHERE nodeCatId = :catId)
                AND status = 1
                ORDER BY command ASC");

$db2->bind(':catId', $catId); //bind here and create wildcard search term here also
//$db2->debugDumpParams();
$rows = $db2->resultset();
echo json_encode($rows);