<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<?php 
include('/home/rconfig/config/config.inc.php');
?>
<html>

 <head>
 <?php
 header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
// echo "Hi, I am not cached. Current date time according to server:".date("r");
?>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	
	<title>rConfig - Documentation</title>
	<meta name="description" content="Configuration management utility for CLI based devices">
	<meta name="copyright" content="Copyright (c) 2012 - rConfig">
	<meta name="author" content="Stephen Stack">
	
	<!-- Add ICO -->
	<link rel="Shortcut Icon" href="<?php echo $config_basedir; ?>favicon.ico"> 
	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="../css/style.css" />
	<link rel="stylesheet" type="text/css" href="../css/content.css" />
	<link rel="stylesheet" type="text/css" href="../css/forms.css" />
	<!-- Help CSS -->
	<link rel="stylesheet" type="text/css" href="css/help-style.css" />
	<link rel="stylesheet" type="text/css" href="css/nav.css" />
	<!-- jQuery -->
	<script type="text/javascript" src="../js/jquery/jquery-1.7.min.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery.validate.min.js"></script>
	<!-- jQuery UI -->
	<script type="text/javascript" src="../js/jquery/jquery.ui.core.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery.ui.position.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="../js/jquery/jquery.ui.autocomplete.js"></script>
	<script type="text/javascript" src="js/nav.js"></script>
 </head>