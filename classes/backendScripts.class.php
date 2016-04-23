<?php
/**
 * This class serves to reduce repeating code in the rconfig/lib/* scripts folder
 *
 * @author stephen stack
 */

class backendScripts {
    protected $conn;
    
    // setup construct with db2 passed from script 
    public function __construct($db2){
         $this->conn = $db2;   
    }
    
    // get & set time
    public function getTime(){
        $this->conn->query("SELECT timeZone FROM settings");
        $result = $this->conn->resultsetCols();
        $timeZone = $result[0];
        return date_default_timezone_set($timeZone);
    }
    
    public function startTime(){
        // script startTime
        $startTime = date('h:i:s A');
        $date = date('Ymd');
        $time_start = microtime(true);
        return array('startTime' => $startTime, 'date' => $date, 'time_start' => $time_start);
    }
    
    public function errorId($log, $errorType){
        $errorText = $errorType." not Set - unable to run script";
        $error = $errorText . "\n";
        $log->Fatal("Error: " . $errorText . " (File: " . $_SERVER['PHP_SELF'] . ")");
        return $error;
        die();
    } 
    
    public function debugOnOff($db2, $argv){
        // get debugging switch from DB - 1 = on, 0 = off
        $db2->query("SELECT commandDebug, commandDebugLocation FROM settings");
        $debugRes = $db2->resultset();
        if ($argv == true) {
            $debugOutputArray = true;
        } else {
            $debugOutputArray = false;
        }
        return array('debugOnOff'=>$debugRes[0]['commandDebug'], 'debugPath'=>$debugRes[0]['commandDebugLocation'], 'cliDebugOutput' => false, 'cliDebugOutput' => $debugOutputArray);
    }
    
}
