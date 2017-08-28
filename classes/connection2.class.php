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
 * @version   1.5.0
 * @link      http://www.rconfig.com/
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
    public function __construct($hostname, $username = "", $password, 
            $enableModePassword, $connPort, $timeout = 60, 
            $userPrmpt, $passPrmpt, $enable, 
            $enableCmd, $enablePrompt, $enablePassPrmpt, $prompt,
            $paging, $pagingCmd, $pagerPrompt, $pagerPromptCmd, $resetPagingCmd) {
        $this->_hostname = $hostname;
        $this->_username = $username;
        $this->_password = $password;
        $this->_timeout = $timeout;
        $this->_enableModePassword = $enableModePassword;
        $this->_port = $connPort;
        $this->_userPrmpt = $userPrmpt;
        $this->_passPrmpt = $passPrmpt;
        $this->_enable = $enable;
        $this->_enableCmd = $enableCmd;
        $this->_enablePrompt = $enablePrompt;
        $this->_enablePassPrmpt = $enablePassPrmpt;
        $this->_prompt = $prompt;
        $this->_paging = $paging;
        $this->_pagingCmd = $pagingCmd;
        $this->_pagerPrompt = $pagerPrompt;
        $this->_pagerPromptCmd = $pagerPromptCmd;
        $this->_resetPagingCmd = $resetPagingCmd;
        $this->_use_usleep = 0; // change to 1 for faster execution
        // don't change to 1 on Windows servers unless you have PHP 5
        $this->_sleeptime = 125000;

        // below are headers that telnet requires for proper session setup - google 'fsockopen php telnet' for more info
        // and per here http://www.phpfreaks.com/forums/index.php?topic=201740.0
        // Not currenty used in this version of the class
//        $this->_header1 = chr(0xFF) . chr(0xFB) . chr(0x1F) . chr(0xFF) . chr(0xFB) . chr(0x20) . chr(0xFF) . chr(0xFB) . chr(0x18) . chr(0xFF) . chr(0xFB) . chr(0x27) . chr(0xFF) . chr(0xFD) . chr(0x01) . chr(0xFF) . chr(0xFB) . chr(0x03) . chr(0xFF) . chr(0xFD) . chr(0x03) . chr(0xFF) . chr(0xFC) . chr(0x23) . chr(0xFF) . chr(0xFC) . chr(0x24) . chr(0xFF) . chr(0xFA) . chr(0x1F) . chr(0x00) . chr(0x50) . chr(0x00) . chr(0x18) . chr(0xFF) . chr(0xF0) . chr(0xFF) . chr(0xFA) . chr(0x20) . chr(0x00) . chr(0x33) . chr(0x38) . chr(0x34) . chr(0x30) . chr(0x30) . chr(0x2C) . chr(0x33) . chr(0x38) . chr(0x34) . chr(0x30) . chr(0x30) . chr(0xFF) . chr(0xF0) . chr(0xFF) . chr(0xFA) . chr(0x27) . chr(0x00) . chr(0xFF) . chr(0xF0) . chr(0xFF) . chr(0xFA) . chr(0x18) . chr(0x00) . chr(0x58) . chr(0x54) . chr(0x45) . chr(0x52) . chr(0x4D) . chr(0xFF) . chr(0xF0);
//        $this->_header2 = chr(0xFF) . chr(0xFC) . chr(0x01) . chr(0xFF) . chr(0xFC) . chr(0x22) . chr(0xFF) . chr(0xFE) . chr(0x05) . chr(0xFF) . chr(0xFC) . chr(0x21);
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

        $this->_readTo($this->_userPrmpt); // add $cliDebugOutput = true for cli debug
        if (strpos($this->_data, $this->_userPrmpt) !== false) { 
            $this->_send($this->_username);
            $this->_readTo($this->_passPrmpt); // reads to password prompt and script will continue to pass if statements
        } else {
            $userPrmpterrorText = 'Something is wrong with the username prompt';
            $log->Conn("Failure: ".$userPrmpterrorText." (File: " . $_SERVER['PHP_SELF'] . ")");
        } 
        
        if (strpos($this->_data, $this->_passPrmpt) !== false) { // check password prompts
            $this->_send($this->_password);
        } else {
            $passPrmpterrorText =  'Something is wrong with the password prompt';
            $log->Conn("Failure: ".$passPrmpterrorText." (File: " . $_SERVER['PHP_SELF'] . ")");
        }

        if ($this->_enable === true) {
            $this->_readTo($this->_enablePrompt);
            if (strpos($this->_data, $this->_enablePrompt) === false) {
                fclose($this->_connection);
                $log->Conn("Error: Authentication Failed for $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
                return false;
            } else {
                $this->_send($this->_enableCmd);
                $this->_readTo($this->_enablePassPrmpt);
                $this->_send($this->_enableModePassword);
                $this->_readTo($this->_prompt);
                if (strpos($this->_data, $this->_prompt) == false) {
                    $log->Conn("Error: Authentication Failed for enable mode for  enable mode for or $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
                    return false;
                }
                // enable paging if set
                $this->_readTo($this->_prompt);
                if($this->_paging === true){
                   $this->_send($this->_pagingCmd); 
                   sleep(1);
                   $this->_readTo($this->_prompt);
                }
            }
        } else {
            /* NEED TO TEST ENABLE AND NON_ENABLE MODE WITHOUT PAGING ENABLED ALSO> CHECK OUT THE _readto CODE FOR THE --More-- PROMPT */
            /* NEXT LINES TO BE UPDATED. NEED TO TEST NON_ENABLE MODE */
            $this->_readTo($this->_prompt);
            if (strpos($this->_data, $this->_prompt) === false) {
                fclose($this->_connection);
                $log->Conn("Error: Authentication Failed for $this->_hostname (File: " . $_SERVER['PHP_SELF'] . ")");
                return false;
            }
            // enable paging if set
            $this->_readTo($this->_prompt);
            if($this->_paging === true){
               $this->_send($this->_pagingCmd); 
               sleep(1);
               $this->_readTo($this->_prompt);
            }
        }
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
//                                                                        var_dump($this->_data);
            if ($cliDebugOutput == true) {
                echo $c;
            }
            // we've encountered the prompt. send TELNET_OK
            if ((substr($this->_data, strlen($this->_data) - strlen($prompt))) == $prompt) {
                return self::TELNET_OK;
                // break;
            }
            
            // if device pager prompt, send defined string to continue manually paging
            if ((substr($this->_data, strlen($this->_data) - strlen($this->_pagerPrompt))) == $this->_pagerPrompt) {
               $this->_send($this->_pagerPromptCmd); 
            }

            // Remove --More-- and backspace and whitespace from output so that it does not copy to text files
            $this->_data = str_replace($this->_pagerPrompt, "", $this->_data);
            $this->_data = str_replace(chr(8), "", $this->_data);
            $this->_data = str_replace('     ', "", $this->_data);
            // Set $_data as false if previous command failed.
            if (strpos($this->_data, '% Invalid input detected') !== false) {
                $this->_data = false;
            }
        } // while
    }    
    
    /**
     * Issue a command to the device
     */
    private function _send($command) {
        fputs($this->_connection, $command . "\r\n");
    }    
    
    /**
     * 
     * Close an active telnet connection and reset the term len if set
     */
    public function closeTelnet($resetPagingCmd, $saveConfig, $exitCmd) {
        if ($this->_connection) {
            if(!empty($resetPagingCmd)){
                $this->_readTo($this->_prompt);
                $this->_send($resetPagingCmd); 
                $this->_readTo($this->_prompt);
            }
            if(!empty($saveConfig)){
                $this->_readTo($this->_prompt);
                $this->_send($saveConfig); 
                $this->_readTo($this->_prompt);
            }
            $this->_send($exitCmd);
            fclose($this->_connection);    

        } else {
            echo "Telnet connection already closed";
            throw new Exception("Telnet connection closed");
        }
    }
    
   
    
    /**
     * Establish a connection to an IOS based device on SSHv2 check for enable mode also and enter enable cmds if needed
     */
    public function connectSSH($command, $prompt, $debugOnOff) {

        $log = ADLog::getInstance();
        // debugging check - real time output on CLI
        if ($debugOnOff === '1' || isset($cliDebugOutput)) {
            define('NET_SSH2_LOGGING', NET_SSH2_LOG_REALTIME);
        }
        
        // Ensure port is set to a valid number.  If not, use default
        if ($this->_port == null || $this->_port <= 0 || $this->_port > 65535) {
            $this->_port = 22;
        }        
        if (!$ssh = new Net_SSH2($this->_hostname, $this->_port, $this->_timeout)) {
            echo "Failure: Unable to connect to " . $this->_hostname . " on port " . $this->_port . "\n";
            $log->Conn("Failure: Unable to connect to " . $this->_hostname . " on port " . $this->_port . " - (File: " . $_SERVER['PHP_SELF'] . ")");
            $ssh->disconnect();
            return false;
        }

        if (!$ssh->login($this->_username, $this->_password)) {
//          
            echo "Error: Authentication Failed or unable to connect to " . $this->_hostname . "\n";
            $log->Conn("Error: Authentication Failed or unable to connect to " . $this->_hostname . " on port " . $this->_port . " - (File: " . $_SERVER['PHP_SELF'] . ")");
            $ssh->disconnect();
            return false;
        }

        $output = '';
        if ($this->_enable === true) {
            $ssh->write($this->_enableCmd . "\n");
            $ssh->read($this->_enablePassPrmpt);
            $ssh->write($this->_enableModePassword . "\n");
            $ssh->read($this->_prompt);
            if($this->_paging === true){
               $ssh->write($this->_pagingCmd . "\n"); 
            }
            $ssh->read($this->_prompt);
            $ssh->write($command . "\n");
            $output = $ssh->read($this->_prompt);
            $ssh->write("\n"); // to line break after command output
            $ssh->read($this->_prompt);
        } else {
            $ssh->read($this->_prompt);
            if($this->_paging === true){
               $ssh->write($this->_pagingCmd . "\n"); 
               sleep(1);
               $ssh->read($this->_prompt);
            }
            $ssh->write($command . "\n");
            $output = $ssh->read($this->_prompt);
            $ssh->write("\n"); // to line break after command output
            $ssh->read($this->_prompt);
        }
        // reset paging if paging is set
        if($this->_paging === true){
            $ssh->write($this->_resetPagingCmd . "\n"); 
            sleep(1);
            $ssh->read($this->_prompt);
        }
        $ssh->disconnect(); // Chnage this
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

        if (!$ssh = new Net_SSH2($this->_hostname, 22, $this->_timeout)) {
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
        if ($this->_enableMode === true) {
            // $ssh->write("\n"); // 1st linebreak after above prompt check
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
    public function showCmdTelnet($cmd, $cliDebugOutput = false) {

        $this->_readTo($this->_prompt, $cliDebugOutput);
        $this->_send($cmd);
        $this->_readTo($this->_prompt, $cliDebugOutput);

        $result = array();
        $this->_data = explode("\r\n", $this->_data);
        array_shift($this->_data);
        array_pop($this->_data);
        if (count($this->_data) > 0) {
            foreach ($this->_data as $line) {
                $line = explode("\r\n", $line);
                array_push($result, $line[0]);
            } 
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
        if ($this->_use_usleep){
            usleep($this->_sleeptime);
        } else {
            sleep(1);
        }
    }




    /**
     * Clears internal command buffer
     * 
     * @return void
     */
    private function _clearBuffer() {
        $this->_data = '';
    }

}
