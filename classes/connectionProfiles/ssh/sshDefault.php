<?php
if ($this->_enableMode === 'on') { 
	$ssh->read('/.*>/', 'NET_SSH2_READ_REGEX'); // read out to '>' 
	$ssh->write("enable\n"); 
	$ssh->read('/.*:/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write($this->_enableModePassword . "\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("terminal pager 0\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write($command . "\n"); 
	$output = $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("\n"); // to line break after command output 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
} else { 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("terminal pager 0\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("terminal length 0\n"); 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write($command . "\n"); 
	$output = $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
	$ssh->write("\n"); // to line break after command output 
	$ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX'); 
}