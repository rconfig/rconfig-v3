<?php

/**
 * rConfig Connection class
 * 
 * Class for managing connections via telnet and SSH to devices
 * This class is heavily modified from the 'Telnet for PHP' class from Ray Soucy <rps@soucy.org>
 * see http://www.soucy.org/ for original file
 *
 *
 * @package   rConfigConnectionClass
 * @originalauthor    Ray Soucy <rps@soucy.org>
 * @modifiedauthor    Stephen Stack <www.rconfig.com>
 * @version   1.0.0
 * @link      http://www.rconfig.com/
 * @license   http://www.rconfig.com/  
 */
class Connection {

    private $_hostname;
    private $_username;
    private $_password;
    private $_connection;
    private $_port;
    private $_data;
    private $_timeout;
    private $_prompt;

    const TELNET_OK = TRUE;

    /**
     * Class Constructor
     * @param  string  $hostname Hostname or IP address of the device
     * @param  string  $username Username used to connect
     * @param  string  $password Password used to connect
     * @param  string  $enableModeOn Enable Mode On/Off as set in database
     * @param  string  $enablePassword Enable Mode password
     * @param  integer  $connPort port to be used when connecting to the device
     * @param  integer $timeout  Connection timeout (seconds)
     * @return object  Telnet object
     */
    public function __construct($hostname, $username = "", $password, $enableMode, $enableModePassword, $connPort, $timeout = 60) {
        $this->_hostname = $hostname;
        $this->_username = $username;
        $this->_password = $password;
        $this->_timeout = $timeout;
        $this->_enableMode = $enableMode;
        $this->_enableModePassword = $enableModePassword;
        $this->_port = $connPort;
        $this->_use_usleep = 0; // change to 1 for faster execution
        // don't change to 1 on Windows servers unless you have PHP 5
        $this->_sleeptime = 125000;

        // below are headers that telnet requires for proper session setup - google 'fsockopen php telnet' for more info
        // and per here http://www.phpfreaks.com/forums/index.php?topic=201740.0
        // Not currenty used in this version of the class
        $this->_header1 = chr(0xFF) . chr(0xFB) . chr(0x1F) . chr(0xFF) . chr(0xFB) . chr(0x20) . chr(0xFF) . chr(0xFB) . chr(0x18) . chr(0xFF) . chr(0xFB) . chr(0x27) . chr(0xFF) . chr(0xFD) . chr(0x01) . chr(0xFF) . chr(0xFB) . chr(0x03) . chr(0xFF) . chr(0xFD) . chr(0x03) . chr(0xFF) . chr(0xFC) . chr(0x23) . chr(0xFF) . chr(0xFC) . chr(0x24) . chr(0xFF) . chr(0xFA) . chr(0x1F) . chr(0x00) . chr(0x50) . chr(0x00) . chr(0x18) . chr(0xFF) . chr(0xF0) . chr(0xFF) . chr(0xFA) . chr(0x20) . chr(0x00) . chr(0x33) . chr(0x38) . chr(0x34) . chr(0x30) . chr(0x30) . chr(0x2C) . chr(0x33) . chr(0x38) . chr(0x34) . chr(0x30) . chr(0x30) . chr(0xFF) . chr(0xF0) . chr(0xFF) . chr(0xFA) . chr(0x27) . chr(0x00) . chr(0xFF) . chr(0xF0) . chr(0xFF) . chr(0xFA) . chr(0x18) . chr(0x00) . chr(0x58) . chr(0x54) . chr(0x45) . chr(0x52) . chr(0x4D) . chr(0xFF) . chr(0xF0);
        $this->_header2 = chr(0xFF) . chr(0xFC) . chr(0x01) . chr(0xFF) . chr(0xFC) . chr(0x22) . chr(0xFF) . chr(0xFE) . chr(0x05) . chr(0xFF) . chr(0xFC) . chr(0x21);
    }

    /**
     * Establish a connection to an IOS based device check for enable mode also and enter enable cmds if needed
     */
    public function connectTelnet() {

        $log = ADLog::getInstance();

        // Ensure port is set to a valid number.  If not, use default
        if ($this->_port == null || $this->_port <= 0 || $this->_port > 65535) {
            $this->_port = 23;
        }

        $this->_connection = fsockopen($this->_hostname, $this->_port, $errno, $errstr, $this->_timeout);

        if ($this->_connection === false) {

            $log->Conn("Failure: Unable to connect to " . $this->_hostname . " on port " . $this->_port . " - fsockopen Error:$errstr ($errno) (File: " . $_SERVER['PHP_SELF'] . ")");
            return false;
        }
        stream_set_timeout($this->_connection, $this->_timeout);

        $this->_readTo(':');
<<<<<<< HEAD
        if (substr($this->_data, -9) == 'Username:' || substr($this->_data, -9) == 'username:' || substr($this->_data, -21) == 'FortiGate-VM64 login:') { // lowercase username for Cisco ACS5 implementations
//            echo 'test';
=======
        if (substr($this->_data, -9) == 'Username:' || substr($this->_data, -9) == 'username:') { // lowercase username for Cisco ACS5 implementations
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
            $this->_send($this->_username);
            $this->_readTo(':');
        }
        $this->_send($this->_password);

        if ($this->_enableMode === 'on') {

            $this->_prompt = '>';
            $this->_readTo($this->_prompt);

            if (strpos($this->_data, $this->_prompt) === false) {
                fclose($this->_connection);

<<<<<<< HEAD
=======
//                echo "Error: Authentication Failed for $this->_hostname\n";
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
                $log->Conn("Error: Authentication Failed for $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
                return false;
            } else {

                $this->_send('enable');
                $this->_readTo(':');
                $this->_send($this->_enableModePassword);
                $this->_prompt = '#';
                $this->_readTo($this->_prompt);
                if (strpos($this->_data, $this->_prompt) == false) {
<<<<<<< HEAD
=======
//                    echo "Error: Authentication Failed for enable mode for $this->_hostname\n";
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
                    $log->Conn("Error: Authentication Failed for enable mode for  enable mode for or $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
                    return false;
                }
                // set term pager 0 for ASAs to avoid paging issues _readTo does not work too well for long command output
                // will not take for IOS, but not an issue
                $this->termLen('0');
                sleep(1);
            }
        } else {
            $this->_prompt = '#';
            $this->_readTo($this->_prompt);
            if (strpos($this->_data, $this->_prompt) === false) {
                fclose($this->_connection);
<<<<<<< HEAD
=======

>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
//                echo "Error: Authentication Failed for $this->_hostname\n";
                $log->Conn("Error: Authentication Failed for $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
                return false;
            }
        }
    }

    /**
     * Establish a connection to an IOS based device on SSHv2 check for enable mode also and enter enable cmds if needed
     */
<<<<<<< HEAD
    public function connectSSH($command, $prompt, $profile) {
=======
    public function connectSSH($command, $prompt) {
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c

        $log = ADLog::getInstance();

        // Ensure port is set to a valid number.  If not, use default
        if ($this->_port == null || $this->_port <= 0 || $this->_port > 65535) {
            $this->_port = 22;
        }

<<<<<<< HEAD
        if (!$ssh = new \phpseclib\Net\SSH2($this->_hostname, $this->_port, $this->_timeout)) {
=======
        // This does not seem to be processed when unable to connect to the address
        // Modifying SSH2.php to return false did not work
        // Will need to use custom error handler to explicitly handle this explicitly
        if (!$ssh = new Net_SSH2($this->_hostname, $this->_port, $this->_timeout)) {
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
            echo "Failure: Unable to connect to " . $this->_hostname . " on port " . $this->_port . "\n";
            $log->Conn("Failure: Unable to connect to " . $this->_hostname . " on port " . $this->_port . " - (File: " . $_SERVER['PHP_SELF'] . ")");
            return false;
        }

        // Updated this failure string to include the above error case as it does manifest here
        if (!$ssh->login($this->_username, $this->_password)) {
//            echo "Error: Authentication Failed or unable to connect to " . $this->_hostname . "\n";
            $log->Conn("Error: Authentication Failed or unable to connect to " . $this->_hostname . " on port " . $this->_port . " - (File: " . $_SERVER['PHP_SELF'] . ")");
            return false;
        }

        $output = '';
<<<<<<< HEAD
        
        include $profile;
        
=======

        if ($this->_enableMode === 'on') {
            // $ssh->write("\n"); // 1st linebreak after above prompt check
            $ssh->read('/.*>/', NET_SSH2_READ_REGEX); // read out to '>'
            $ssh->write("enable\n");
            $ssh->read('/.*:/', NET_SSH2_READ_REGEX);
            $ssh->write($this->_enableModePassword . "\n");
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write("terminal pager 0\n");
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write("terminal length 0\n");
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write($command . "\n");
            $output = $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write("\n"); // to line break after command output
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
        } else {
            // $ssh->write("\n"); // 1st linebreak after above prompt check		
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write("terminal pager 0\n"); //set in case device is ASA
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write("terminal length 0\n"); //set in case device is ASA
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write($command . "\n");
            $output = $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            $ssh->write("\n"); // to line break after command output
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
        }
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
        $ssh->disconnect();
        $result = array();
        $this->_data = explode("\r\n", $output);
        array_shift($this->_data);
        array_pop($this->_data);
        if (count($this->_data) > 0) {
            foreach ($this->_data as $line) {
                $line = explode("\r\n", $line); // changed from 3xSpaces to /r/n as a delimiter for explode
                array_push($result, $line[0]);
            } // foreach
        }
        $this->_data = $result;
        return $this->_data;
    }

    /**
     *  Write a config snippet to to a device using SSH
     */
    public function writeSnippetSSH($snippetArr, $prompt) {

        $log = ADLog::getInstance();

<<<<<<< HEAD
        if (!$ssh = new \phpseclib\Net\SSH2($this->_hostname, 22, $this->_timeout)) {
=======
        if (!$ssh = new Net_SSH2($this->_hostname, 22, $this->_timeout)) {
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
            $output = "Failure: Unable to connect to $this->_hostname\n";
            $log->Conn("Failure: Unable to connect to " . $this->_hostname . " - (File: " . $_SERVER['PHP_SELF'] . ")");
            return false;
        }

        if (!$ssh->login($this->_username, $this->_password)) {
            $output = "Error: Authentication Failed for $this->_hostname\n";
            $log->Conn("Error: Authentication Failed for $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
            return false;
        }
        $output = '';
        if ($this->_enableMode === 'on') {
            // $ssh->write("\n"); // 1st linebreak after above prompt check
<<<<<<< HEAD
            $ssh->read('/.*>/', 'NET_SSH2_READ_REGEX'); // read out to '>'
            $ssh->write("enable\n");
            $ssh->read('/.*:/', 'NET_SSH2_READ_REGEX');
            $ssh->write($this->_enableModePassword . "\n");
            $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX');
            foreach ($snippetArr as $key => $command) {
                $ssh->write($command . "\n");
                $output .= $ssh->read('/.*#/', 'NET_SSH2_READ_REGEX'); // read out to '#'
            }
            $ssh->write("\n"); // to line break after command output
            $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX');
        } else {
            // $ssh->write("\n"); // 1st linebreak after above prompt check		
            $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX');
            foreach ($snippetArr as $key => $command) {
                $ssh->write($command . "\n");
                $output .= $ssh->read('/.*#/', 'NET_SSH2_READ_REGEX'); // read out to '#' because the prompt will change depending on the deployed config
            }
            $ssh->write("\n"); // to line break after command output
            $ssh->read('/' . $prompt . '/', 'NET_SSH2_READ_REGEX');
=======
            $ssh->read('/.*>/', NET_SSH2_READ_REGEX); // read out to '>'
            $ssh->write("enable\n");
            $ssh->read('/.*:/', NET_SSH2_READ_REGEX);
            $ssh->write($this->_enableModePassword . "\n");
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            foreach ($snippetArr as $key => $command) {
                $ssh->write($command . "\n");
                $output .= $ssh->read('/.*#/', NET_SSH2_READ_REGEX); // read out to '#'
            }
            $ssh->write("\n"); // to line break after command output
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
        } else {
            // $ssh->write("\n"); // 1st linebreak after above prompt check		
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
            foreach ($snippetArr as $key => $command) {
                $ssh->write($command . "\n");
                $output .= $ssh->read('/.*#/', NET_SSH2_READ_REGEX); // read out to '#' because the prompt will change depending on the deployed config
            }
            $ssh->write("\n"); // to line break after command output
            $ssh->read('/' . $prompt . '/', NET_SSH2_READ_REGEX);
>>>>>>> 8ef92c9be261ba14bf1446102672c898602bb96c
        }
        $ssh->disconnect();
        return $output;
    }

    /**
     * Telnet Show Command Input
     * @param  string        $cmd The command to execute
     * @param  string        $prompt The device exec mode prompt
     * @return array|boolean On success returns an array, false on failure.
     */
    public function showCmdTelnet($cmd, $prompt, $cliDebugOutput = false) {

        $this->_send($cmd);
        $this->_prompt = $prompt;
        $this->_readTo($this->_prompt, $cliDebugOutput);

        $result = array();
        $this->_data = explode("\r\n", $this->_data);
        array_shift($this->_data);
        array_pop($this->_data);
        if (count($this->_data) > 0) {
            foreach ($this->_data as $line) {
                $line = explode("\r\n", $line);
                array_push($result, $line[0]);
            } // foreach
        }
        $this->_data = $result;
        return $this->_data;
    }

    // next 3 functions are from http://www.geckotribe.com/php-telnet/#download
    /**
     * Telnet Do Command Input
     * @param  string        $cmd The command to execute
     * @param  string        $result to send back for output
     * @return read data from connection
     */
    public function writeSnippetTelnet($c, &$r) {
        if ($this->_connection) {
            fputs($this->_connection, "$c\r");
            $this->_sleep();
            $this->_getResponse($r);
            $r = preg_replace("/^.*?\n(.*)\n[^\n]*$/", "$1", $r);
        }
        return $this->_connection ? 1 : 0;
    }

    private function _getResponse(&$r) {
        $r = '';
        do {
            $r.=fread($this->_connection, 1000);
            $s = socket_get_status($this->_connection);
        } while ($s['unread_bytes']);
    }

    private function _sleep() {
        if ($this->_use_usleep)
            usleep($this->_sleeptime);
        else
            sleep(1);
    }

    /**
     * Close an active connection for a FWSM/ASA and set term len value on the way out of the device
     */
    public function close($termLen) {
        sleep(1);
        $this->termLen($termLen); // set term pager $termLen for ASAs
        $this->_send('quit');
        fclose($this->_connection);
    }

// close

    /**
     * Issue a command to the device
     */
    private function _send($command) {
        fputs($this->_connection, $command . "\r\n");
    }

// _send

    /**
     * Clears internal command buffer
     * 
     * @return void
     */
    private function _clearBuffer() {
        $this->_data = '';
    }

    /**
     * Read from socket until $prompt
     * @param string $prompt Single character or string
     */
    private function _readTo($prompt, $cliDebugOutput = false) {

        if (!$this->_connection) {
            throw new Exception("Telnet connection closed");
        }
        // clear the buffer 
        $this->_clearBuffer();

        while (($c = fgetc($this->_connection)) !== false) {

            $this->_data .= $c;
            if ($cliDebugOutput == true) {
                echo $c;
            }
            // we've encountered the prompt. send TELNET_OK
            if ((substr($this->_data, strlen($this->_data) - strlen($prompt))) == $prompt) {
                return self::TELNET_OK;
                // break;
            }
            // if ($c == $char[0]) break; // old code
            if ($c == '-') {
                // Continue at --More-- prompt
                if (substr($this->_data, -8) == '--More--')
                    fputs($this->_connection, ' ');
            }

            // Remove --More-- and backspace and whitespace
            $this->_data = str_replace('--More--', "", $this->_data);
            $this->_data = str_replace(chr(8), "", $this->_data);
            $this->_data = str_replace('     ', "", $this->_data);
            // Set $_data as false if previous command failed.
            if (strpos($this->_data, '% Invalid input detected') !== false) {
                $this->_data = false;
            }
        } // while
    }

    /*
     * send termLen value to console for ASAs only	
     */

    public function termLen($value) {
        $result = false;
        // if ($this->_prompt == '#') {
        $this->_send('terminal pager ' . $value);
        // }
        if ($this->_data !== false) {
            $this->_prompt = '#';
            $result = true;
        }
        $this->_readTo($this->_prompt);
        return $result;
    }

// _send
}
// Telnet Class
// trailing PHP tag omitted to prevent accidental whitespace
