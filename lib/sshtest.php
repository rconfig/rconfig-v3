<?php
require('/home/rconfig/classes/sshlib/Net/SSH2.php'); // this will be used in connection.class.php 
define('NET_SSH2_LOGGING', NET_SSH2_LOG_COMPLEX);
$ssh = new Net_SSH2('192.168.1.171', '22') or die();
$ssh->login('YourUserName', 'YourPassword')or die();
           echo $ssh->read('User:'); 
            $ssh->write("YourUserName\n"); // make sure there is not whitespace between the username and the '\n'
			echo $ssh->read('Password:');
			$ssh->write("YourPassword\n"); // make sure there is not whitespace between the password and the '\n'
			$ssh->write("\n"); // because an anter is required right after the who run command is run
			$ssh->setTimeout(2);
			echo $ssh->read('(wlc) >');
            $ssh->write("config paging disable\r\n");
			echo $ssh->read('(wlc) >');
			$ssh->write("show run-config\r\n"); // try any other command that does not need paging, and you will get the full output.
			$ssh->write("\r\n");
			echo $ssh->read();
			$ssh->write("\r\n");
			$ssh->write("\r\n");
			$ssh->write("\r\n");
			$ssh->write("\r\n");
			$ssh->write("\r\n"); // add more of these to page further down the running config

			echo $ssh->read();
			$ssh->disconnect();
// echo $ssh->getLog(); // for debugging
// echo $ssh->getErrors(); // for debugging
?>



