<?php
// various DB checks during the install process
if(isset($_GET['server'])){$server = $_GET['server'];}
if(isset($_GET['port'])){$port = $_GET['port'];}
if(isset($_GET['dbName'])){$dbName = $_GET['dbName'];}
if(isset($_GET['dbUsername'])){$dbUsername = $_GET['dbUsername'];}
if(isset($_GET['dbPassword'])){$dbPassword = $_GET['dbPassword'];}

$sqlHost = $server . ":" . $port;
$array = array();

// chech server connectivity
$handle = fsockopen($server, $port);

if ($handle) {
    $array['connTest'] = '<strong><font class="good">Pass</strong> </font>';
} else {
    $array['connTest'] = '<strong><font class="bad">Fail - Cannot connect to ' . $server . ':' . $port . '</strong></font>';
}
fclose($handle);

// check Username/Password 
try {
        $conn = new PDO("mysql:host=$server", $dbUsername, $dbPassword);
        // set the PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $link = true; 
    }
catch(PDOException $e)
    {
        $link = false; 
    }
if ($link) {
    $array['credTest'] = '<strong><font class="good">Pass</strong></font>';
} else {
    $array['credTest'] = '<strong><font class="bad">Fail -  Could not connect to Database Server. Check your settings!</strong></font>';
}

//check if DB exists
if(isset($dbName)){
    $dsn = 'mysql:host=' . $server . ';dbname=' . $dbName . ';port=' . $port;
    // Set options
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_PERSISTENT => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    //Create a new PDO instance
    try {
        $conn = new PDO($dsn, $dbUsername, $dbPassword, $options);
    }
    // Catch any errors
    catch (PDOException $e) {
        $sqlError = $e->getMessage();
    }    
    $stmt = $conn->query("SELECT COUNT(*) FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '".$dbName."'");
    $db_selected = $stmt->fetchColumn();
    if ($db_selected == 1) {
        $array['dbTest'] = '<strong><font class="bad">Fail - Database already installed</strong></font>';
    } elseif ($db_selected == 0) {
            $array['dbTest'] = '<strong><font class="good">Pass - '.$dbName.' not in use</strong></font>';
        }
} else {
    $array['dbTest'] = '<strong><font class="bad">Fail - Database Name was not entered</strong></font>';
}

$conn = null;
echo json_encode($array);