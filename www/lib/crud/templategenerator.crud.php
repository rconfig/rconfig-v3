<?php
require_once("../../../classes/db.class.php");
require_once("../../../classes/ADLog.class.php");
require_once("../../../classes/crontab.class.php");
require_once("../../../config/config.inc.php");
require_once("../../../config/functions.inc.php");

// Returns true if the number of user inputted substituted variables matches the number of variables found in the config template
function verifySubstitutionCount($parsedTemplate, $templateVarSubs){
	// $parsedTemplate is each @variable@ (no symbols) separated by a CR
	// $templateVarSubs is each user inputted value to replace each listed @variable@, at a 1:1 ratio, in order

	// Store each @variable@, including symbols in array $templateVarSymArr
	$templateVarArr = explode("\n", $parsedTemplate);
	// Store each user submitted substitution in array $substitutedVarsArr
	$substitutedVarsArr = explode("\n", $templateVarSubs);
	
	// Find number of strings in $templateVarSymArr
	$templateVarCount = count($templateVarArr);
	// Find number of strings in $substitutedVarsArr
	$substitutedVarsCount = count($substitutedVarsArr);

	if ($templateVarCount == $substitutedVarsCount){
		return true;
	}else {
		return false;
	}
}

function generateNewConfig($template, $parsedTemplateSymbols, $templateVarSubs){
	// $template is the full config with @variables@ in place
	// $parsedTemplateSymbols is each @variable@ separated by a CR
	// $templateVarSubs is each user inputted value to replace each listed @variable@, at a 1:1 ratio, in order

	// Store each @variable@, including symbols in array $templateVarSymArr
	//$templateVarSymArr = explode('\n', $parsedTemplateSymbols);  //not working at all
	$templateVarSymArr = preg_split('/\n/', $parsedTemplateSymbols);  // This works!
	// Store each user submitted substitution in array $substitutedVarsArr
	$substitutedVarsArr = explode('\r\n', $templateVarSubs);

	$n = 0;
	
	foreach ($templateVarSymArr as $vars) {
		if (strpos($template, $vars) !== FALSE) {
			$template = str_replace($vars, $substitutedVarsArr[$n++], $template);
		}
	}
	
	return $template;
}

?>