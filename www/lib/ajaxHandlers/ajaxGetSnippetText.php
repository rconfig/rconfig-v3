<?php
// Get Snippet from DB based on Snippet ID
session_start();
require_once("../../../classes/db2.class.php");
require_once("../../../config/config.inc.php");

$db2  = new db2();
$q   = $db2->q("SELECT snippet FROM snippets 
                WHERE id = " . $_GET['id'] );

echo json_encode($q);