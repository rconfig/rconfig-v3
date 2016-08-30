<?php
if ($this->_enableMode === 'on') { 
	$ssh->read('/.*>/', 'NET_SSH2_READ_REGEX'); // read out to '>' 
	$ssh->write("enable\n"); 
	$ssh->read('/.*:/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write($this->_enableModePassword . "\n"); 
	$ssh->read( '/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("terminal length 1000\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write($command . "\n"); 
	$output = $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("\n"); // to line break after command output 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
} else { 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("terminal length 1000\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write($command . "\n"); 
	$output = $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("\n"); // to line break after command output 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
    
}
/* Important Syntax Notes */
/* Please be careful with commas, quotation marks, semi-colons, etc.. */
/* The editor in rConfig will wanr you in case of syntax errors */
/* Line2/16: if you have selected this device for enable mode on the 'device' page. */
/* The code after  } else { on lin 16 will run of enable mode is NOT checked */
/* Line3: ssh will read everything up to a > character. This is regex code. (exlucding quotes) '/.*>/' */
/* Line4: send the command 'enable' directly followed by a carraige return command
/* Line5: ssh will read everything up to a : character. This is regex code. (exlucding quotes) '/.*:/' */
/* above line maybe redundant depending on your login to the devices */
/* Line6: Sends the enable password command for the device as retirived by the DB */
/* Line7: Read ssh output until the code finds the $prompt as set for this device in the DB or devices configuration page */
/* Line8: send the text terminal length 1000\n to the device. This is very device dependant, but the intention is that if a show run */
/* command is being sent, that the entire output will be saved to a single file */
/* Line9: Read ssh output until the code finds the $prompt as set for this device in the DB or devices configuration page */
/* Line10: Send the command as configured in the DB for this scheduled task. */
/* Line11: Read ssh output until the code finds the $prompt as set for this device in the DB or devices configuration page */
/* Line12: to line break after command output  */
/* Line13: Read ssh output until the code finds the $prompt as set for this device in the DB or devices configuration page */
   