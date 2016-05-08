<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

/* Gets pageTimeout Value from settings table */
session_start();
require_once("../../../classes/db2.class.php");
$db2 = new db2();
$q = $db2->query("SELECT pageTimeout
        FROM settings 
        WHERE id = 1");
$result = $db2->resultsetCols();
echo json_encode($result);
