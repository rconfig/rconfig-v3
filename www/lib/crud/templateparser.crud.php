<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/crontab.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");

// begin template parsing section

// Returns all variables in config template submitted between two @ symbols, including the symbols in the returned values
function templateParserSymbols($template){
	$n = 0;
	$parsed = "";
	
	/* Start here */
	// Extract all strings between two @ symbols from configuration template input field, store in array $matches.  $count is number of matches found
	$count = preg_match_all('/\@(.*?)\@/', $template, $matches);
	
	// For each match in $matches array, run loop
	foreach ($matches as $key=>$val) {
		// There are 2 keys: key 0 is string with @ symbols on either side. Key 1 is without the @ symbols. If $key is 0, add return values
		if ($key == "0") {
			for ($n = 0; $n < $count; $n++){
				if (!strstr($parsed, $matches[$key][$n])){
					$parsed .= $matches[$key][$n] . "\n";
				}
			}
		}
	}
	
	return $parsed;
}

// Returns all variables in config template submitted between two @ symbols, excluding the symbols in the returned values
function templateParser($template){
	$n = 0;
	$parsed = "";
	
	/* Start here */
	// Extract all strings between two @ symbols from configuration template input field, store in array $matches.  $count is number of matches found
	$count = preg_match_all('/\@(.*?)\@/', $template, $matches);
	
	// For each match in $matches array, run loop
	foreach ($matches as $key=>$val) {
		// There are 2 keys: key 0 is string with @ symbols on either side. Key 1 is without the @ symbols. If $key is 1, add return values
		if ($key == "1") {
			for ($n = 0; $n < $count; $n++){
				if (!strstr($parsed, $matches[$key][$n])){	// If current variable from $matches array has already been added to the $parsed variable, ignore it, as it's a duplicate
					$parsed .= $matches[$key][$n] . "\n";
				}
			}
		}
	}
	
	return $parsed;
}

?>