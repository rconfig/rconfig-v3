<?php
if(isset($_GET['server'])){$server = $_GET['server'];}
if(isset($_GET['port'])){$port = $_GET['port'];}
if(isset($_GET['dbName'])){$dbName = $_GET['dbName'];}
if(isset($_GET['dbUsername'])){$dbUsername = $_GET['dbUsername'];}
if(isset($_GET['dbPassword'])){$dbPassword = $_GET['dbPassword'];}
$sqlHost = $server . ":" . $port;
$dbFile = '../../rconfig.sql';
$configFilePathOriginal = '/home/rconfig/www/install/config.inc.php.template';
$configFilePathInstalled = '/home/rconfig/config/config.inc.php';
$array = array();

// add DB
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_PERSISTENT => true,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
$error = ''; // set error var to blank
try {
    $dbh = new PDO('mysql:port=' . $port . ';host=' . $server, $dbUsername, $dbPassword, $options);
    $dbh->exec("CREATE DATABASE `$dbName`;") 
    or die(print_r($dbh->errorInfo(), true));
    $dbCreated = 1;
} catch (PDOException $e) {
    $error = "DB ERROR: ". $e->getMessage();
    echo $error;
}
        
if ($dbCreated = 1) {
    // rewrite the 'DATABASE_NAME' tag from the SQL file into memory
    $templateFile = file_get_contents($dbFile);
    $templateFile = str_replace('DATABASE_NAME', $dbName, $templateFile);
    $sql = file_get_contents($dbFile); //file name should be name of SQL file with .sql extension. 
    try {
        $dsn = 'mysql:host=' . $server . ';dbname=' . $dbName . ';port=' . $port;
        $dbh = new PDO($dsn, $dbUsername, $dbPassword, $options);
        $sqlArray = explode(';',$templateFile);
        foreach ($sqlArray as $stmt) { //loop through each line of the sql file and execute
            if (strlen($stmt) > 3 && substr(ltrim($stmt), 0, 2) != '/*') {
                $sth = $dbh->prepare($stmt);
                $sth->execute() or die(print_r($sth->errorInfo(), true));
            }
        }
        $qr = 0;
    } catch (PDOException $e) {
        echo $e->getMessage();
            $error = "DB ERROR: ". $e->getMessage();
            echo $error;
            $qr == 1; // set query result to fail and it returns false below
    }
    echo $qr;
/* Add details to /includes/config.inc.php file */
            $configFile = file_get_contents($configFilePathOriginal);
            // re-write config file in memory
            $configFile = str_replace('_DATABASEHOST', $server, $configFile);
            $configFile = str_replace('_DATABASEPORT', $port, $configFile);
            $configFile = str_replace('_DATABASENAME', $dbName, $configFile);
            $configFile = str_replace('_DATABASEUSERNAME', $dbUsername, $configFile);
            $configFile = str_replace('_DATABASEPASSWORD', $dbPassword, $configFile);

            chmod($configFilePathInstalled, 0777);
            file_put_contents($configFilePathInstalled, $configFile);
            chmod($configFilePathInstalled, 0644);
            shell_exec('chown -R apache /home/rconfig'); // set all dir permissions correctly

    $array['success'] = '<strong><font class="Good">rConfig database installed successfully</strong></font><br/>';
            
} else {
    $array['error'] = '<strong><font class="bad">Fail - ' . $error . '</strong></font><br/>';
    // DROP DB on failure
        try {
            $dbh = new PDO('mysql:port=' . $port . ';host=' . $server, $dbUsername, $dbPassword, $options);
            $dbh->exec("DROP DATABASE `$dbName`;") 
            or die(print_r($dbh->errorInfo(), true));
        } catch (PDOException $e) {
            $error = "DB ERROR: ". $e->getMessage();
            echo $error;
        }
}
echo json_encode($array);