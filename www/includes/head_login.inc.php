<?php 
// PHP Includes
include("../config/config.inc.php"); 
include("../config/functions.inc.php"); 
include("../classes/usersession.class.php");

/* Turn on event logging */
include("../classes/ADLog.class.php");
$log = ADLog::getInstance();

	/**
	 * User has already logged in, so direct to main dashboard page - 
	 * applicable to login page only
	 */
	if($session->logged_in){
         header("Location: ". $config_basedir ."dashboard.php");
	}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<title>rConfig - Configuration Management</title>
	<meta name="description" content="Configuration management utility for CLI based devices">
	<meta name="copyright" content="Copyright (c) 2015 - rConfig">
	<meta name="author" content="Stephen Stack">
	
	<!-- Add ICO -->
	<link rel="Shortcut Icon" href="<?php echo $config_basedir; ?>favicon.ico"> 
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	<link rel="stylesheet" type="text/css" href="css/content.css" />
	<link rel="stylesheet" type="text/css" href="css/forms.css" />

	<!-- jQuery -->
	<script type="text/javascript" src="js/jquery/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.validate.min.js"></script>
	<!-- jQuery UI -->
	<script type="text/javascript" src="js/jquery/jquery.ui.core.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.position.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="js/jquery/jquery.ui.autocomplete.js"></script>

</head>