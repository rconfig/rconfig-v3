<?php
/* this will retrieve commands based on submitted CatId */
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../config/config.inc.php");
$db2  = new db2();
$log = ADLog::getInstance();
$q   = $db2->q("SELECT id, command FROM configcommands 
                WHERE id IN (SELECT DISTINCT configCmdId 
                FROM cmdCatTbl 
                WHERE nodeCatId = '" . $_GET['catId'] . "')
                AND status = 1
                ORDER BY command ASC");

echo json_encode($q);