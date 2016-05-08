<?php
// Processor and method for general settings options
class Process
{
    /* Class constructor */
    public function __construct()
    {
        /* User adjust debugging Option*/
        if (isset($_GET['debugOnOff'])) {
            $this->procDebugOnOff();
        } else if (isset($_GET['deviceToutVal'])) {
            $this->procDeviceTimeout();
        } else if (isset($_GET['pageTimeoutVal'])) {
            $this->procPageTimeout();
        }  else if (isset($_GET['timeZoneChange'])) {
            $this->procTimeZoneChange();
        } else if (isset($_GET['getTimeZone'])) {
            $this->getTimeZone();
        } else if (isset($_GET['getDebugStatus'])) {
            $this->getDebugStatus();
        } else if (isset($_GET['phpLoggingOnOff'])) {
            $this->phpLoggingOnOff();
        } else if (isset($_GET['getPhpLoggingStatus'])) {
            $this->getPhpLoggingStatus();
        } else if (isset($_GET['defaultCredsManualSet'])) {
            $this->procDefaultCredsManualSet();
        } else if (isset($_GET['getDefaultCredsManualSet'])) {
            $this->getDefaultCredsManualSet();
        }
    } // end process function

    /**
     * procDebugOnOff - Change the debug value in the settings table to 1 or 0 to turn
     device output debugging to on or off respectively
     */
    function procDebugOnOff()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2 = new db2();
        $log = ADLog::getInstance();
        
        if ($_GET['debugOnOff'] == '1') {
            $status = "On";
            $debugOnOff = $_GET['debugOnOff'];
        } else {
            $status = "Off";
            $debugOnOff = 0;
        }
        
        $db2->query("UPDATE `settings` SET `commandDebug` = :debugOnOff");
        $db2->bind(':debugOnOff', $debugOnOff);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<font color='red'>Debugging status changed successfully to " . $status . "</font>";
        }
        /* Update failed */
        else {
            $response = "failed";
            $log->Warn("Failure: Could not update debugSetting in DB for ajaxSettingsProcess.php:".$queryResult);
        }
        echo json_encode($response);
    }    
	
	
	/**
     * phpLoggingOnOff - Change the php logging value in the settings tbl to 1 or 0 to turn
     php logging to on or off respectively
     */
    function phpLoggingOnOff()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if ($_GET['phpLoggingOnOff'] == '1') {
            $status = "On";
            $phpLoggingOnOff = $_GET['phpLoggingOnOff'];
        } else {
            $status = "Off";
            $phpLoggingOnOff = 0;
        }

        $db2->query("UPDATE `settings` SET `phpErrorLogging` = :phpLoggingOnOff");
        $db2->bind(':phpLoggingOnOff', $phpLoggingOnOff);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<font color='red'>PHP Error Logging status changed successfully to " . $status . "</font>";
        }
        /* Update failed */
        else {
            $response = "failed";
            $log->Warn("Failure: Could not update phpErrorLogging in DB for ajaxSettingsProcess.php:".$queryResult);
        }
        echo json_encode($response);
    }
    
    
    /**
     * procDeviceTimeout - Change the device connection timeout value
     */
    function procDeviceTimeout()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['deviceToutVal'])) {
            $timeout = $_GET['deviceToutVal'];
        }
        $db2->query("UPDATE `settings` SET `deviceConnectionTimout` = :deviceToutVal");
        $db2->bind(':deviceToutVal', $timeout);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>Device Connection Timeout changed successfully to " . $timeout . " Seconds</font>";
        }
        /* Update failed */
        else {
            $response = "failed";
            $log->Warn("Failure: Could not update deviceConnectionTimout in DB for ajaxSettingsProcess.php:".$queryResult);
        }
        echo json_encode($response);
    }  
    
    /**
     * procPageTimeout - Change the webpage timeout value
     */
    function procPageTimeout()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['pageTimeoutVal'])) {
            $timeout = $_GET['pageTimeoutVal'];
        }
        $db2->query("UPDATE `settings` SET `pageTimeout` = :pageTimeoutVal");
        $db2->bind(':pageTimeoutVal', $timeout);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>Webpage Timeout changed successfully to " . $timeout . " Seconds</font>";
        }
        /* Update failed */
        else {
            $response = "failed";
            $log->Warn("Failure: Could not update procPageTimeout in DB for ajaxSettingsProcess.php:".$queryResult);
        }
        echo json_encode($response);
    }
    
    
    
/**
 * procTimeZoneChange - Change the server timezone
 */
    function procTimeZoneChange()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['timeZoneChange'])) {
            $timeZone = $_GET['timeZoneChange'];
        }

        $db2->query("UPDATE `settings` SET `timeZone` = :timeZone");
        $db2->bind(':timeZone', $timeZone);
        $queryResult = $db2->execute();        
        /* Update successful */
        if ($queryResult) {
            $response = "<br/><font color='green'>Timezone changed successfully to " . $timeZone . "</font>";
        }
        /* Update failed */
        else {
            $response = "failed";
            $log->Warn("Failure: Could not update Timezone in DB for ajaxSettingsProcess.php:".$queryResult);
        }
        
        echo json_encode($response);
        
    }
    
    /**
     * getDebugStatus - Change the device debug status
     */
    function getDebugStatus()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['getDebugStatus'])) {
            $db2->query("SELECT commandDebug FROM settings");
            $result = $db2->single();
            $status = $result['commandDebug'];
            /* Update successful */
            if ($status == '1') {
                $response = "<font color='red'><strong>Debugging Status: </strong>On<br/></font>";
            }
            /* Update failed */
            else if ($status == '0') {
                $response = "<font color='green'><strong>Debugging Status: </strong>Off</font>";
            }
            
            echo json_encode($response);
        }
    }  
    
    /**
     * getTimeZoneStatus - Change the device connection timeout value
     */
    function getTimeZone()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['getTimeZone'])) {
            $db2->query("SELECT timeZone FROM settings");
            $result = $db2->single();
            $timeZone = $result['timeZone'];
            /* Update successful */
            if (!empty($timeZone)) {
                $response = $timeZone;
            }
            /* Update failed */
            else if (empty($timeZone)) {
                $response = "";
            }
            echo json_encode($response);
        }
    }
	
    /**
     * procDeviceTimeout - Change the device connection timeout value
     */
    function getPhpLoggingStatus()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['getPhpLoggingStatus'])) {
            $db2->query("SELECT phpErrorLogging FROM settings");
            $result = $db2->single();    
            $status = $result['phpErrorLogging'];
            /* Update successful */
            if ($status == '1') {
                $response = "<font color='red'><strong>PHP Error Logging Status: </strong>On<br/></font>";
            }
            /* Update failed */
            else if ($status == '0') {
                $response = "<font color='green'><strong>PHP Error Logging Status: </strong>Off</font>";
            }
            
            echo json_encode($response);
        }
    }
	
    /**
     * procDefaultCredsManualSet - Change the status for using the default credential set when manually uploading/downloading configs to/from devices
     */
    function procDefaultCredsManualSet()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if ($_GET['defaultCredsManualSet'] == '1') {
            $status = "enabled";
            $defaultCredsManualSet = $_GET['defaultCredsManualSet'];
        } else {
            $status = "disabled";
            $defaultCredsManualSet = 0;
        }
        $db2->query("UPDATE `settings` SET `useDefaultCredsManualSet` = :defaultCredsManualSet");
        $db2->bind(':defaultCredsManualSet', $defaultCredsManualSet);
        $queryResult = $db2->execute();
        /* Update successful */
        if ($queryResult) {
            if ($_GET['defaultCredsManualSet'] == '1') {
                    $response = "<font color='red'>Default credentials are disabled and individual users will have to input their credentials for manual config uploads & downloads</font>";
            } else {
                    $response = "<font color='red'>Default credentials are enabled and will be used for manual config uploads & downloads</font>";
            }
        }
        /* Update failed */
        else {
            $response = "failed";
            $log->Warn("Failure: Could not update useDefaultCredsManualSet in DB for ajaxSettingsProcess.php:".$queryResult);
        }
        echo json_encode($response);
    }
	
    /**
     * getDefaultCredsManualSet - Get value set for using default credentials with manual uploads & downloads
     */
    function getDefaultCredsManualSet()
    {
        session_start();
        require_once("../../../classes/db2.class.php");
        require_once("../../../classes/ADLog.class.php");
        $db2  = new db2();
        $log = ADLog::getInstance();
        
        if (isset($_GET['getDefaultCredsManualSet'])) {
             $db2->query("SELECT useDefaultCredsManualSet FROM settings");
            $result = $db2->single();    
            $useDefaultCredsManualSet = $result['useDefaultCredsManualSet'];
            /* Update successful */
            $response = $useDefaultCredsManualSet;
            echo json_encode($response);
        }
    }
}; //end Class
/* Initialize process */
$process = new Process;