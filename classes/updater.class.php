<?php 
/**
 * updater.class.php
 * 
 * The updater class will perfomr some functions related to the update process of rConfig
 * 
 * Written by: Stephen Stack, rConfig
 * Last Updated: 31 dec 2012
 */

class updater {
	
	/**
	 * Checks that file is uploaded
	 *
	 * @param updateFile 
	 * @return boolean
	 */
	public  function checkForUpdateFile($updateFile) {
		
		if (is_file($updateFile)) {
			return true;
		} else {
			return false;
		}	
	}
	
	
	/**
	 * Extracts Xip archive
		*
	 * @param updateFile 
	 * @param extractDir 
	 * @return boolean
	 */
    public  function extractUpdate($updateFile, $extractDir) {
		
		$zip = new ZipArchive;
		if ($zip->open($updateFile) === TRUE) {
			$zip->extractTo($extractDir);
			$zip->close();
			return true;	
		} else {
			return false;			
		}
	}
		
	/**
	 * Backup config file to tmp rconfig update config dir
	 *
	 * @param sourceConfigFile 
	 * @param destinationConfigFile 
	 * @return boolean
	 */
	public function backupConfigFile($sourceConfigFile, $destinationConfigFile) {
		
		if (!copy($sourceConfigFile, $destinationConfigFile)) {
			return false;
		} else {
			return true;
		}	
	}		


	/**
	 * Updayes tmp config file with current install version
		*
	 * @param latestVer 
	 * @param destinationConfigFile 
	 */
	public function updateConfigVersionInfo($latestVer, $destinationConfigFile) {
		
		$lines = file($destinationConfigFile);
		foreach($lines as $k=>$v){
			if (strpos($v,'$config_version') !== false) {
				// echo $k;
				$lines[$k] = '$config_version = "'.$latestVer.'";' . PHP_EOL;
			}
		}
		// 
		file_put_contents($destinationConfigFile,  $lines);
    }	
	
	/**
	 * Copy tmp app dirs to prod rConfig folder
		*
	 * @param sourceConfigFile 
	 * @param destinationConfigFile 
	 * @return boolean
	 */
	public function copyAppDirsToProd($latestVer, $folderstoCopy) {
		
		foreach ($folderstoCopy as $fK=>$fV){
			$destinationCopyFolder = '/home/rconfig/'.$fV;
			$sourceCopyFolder = '/home/rconfig/tmp/update-'.$latestVer.'/rconfig/'.$fV;
			
			$this->recurse_copy($sourceCopyFolder, $destinationCopyFolder);
		}
	}

	/**
	 * Copy tmp app dirs to prod rConfig folder
		*
	 * @param sourceConfigFile 
	 * @param destinationConfigFile 
	 * @return boolean
	 */
	public function createDirs($dirsToCreateArr) {
		
		foreach ($dirsToCreateArr as $dK=>$dV){
			mkdir($dV, 0755);
		}
		
	}
	
	/**
	 * Load SQL File to DB
	 *
	 * @param path sqlFileToExecute
	 * @return errors
	 */
	public function loadSqlFile ($sqlFileToExecute, $sqlServer, $sqlUser, $sqlPass, $sqlDb) {
		// from http://www.phptoys.com/tutorial/creating-sql-executor.html
		mysql_connect($sqlServer, $sqlUser, $sqlPass) or die('Error connecting to MySQL server: ' . mysql_error());
		// Select database
		mysql_select_db($sqlDb) or die('Error selecting MySQL database: ' . mysql_error());
		
		$sqlFile = file_get_contents($sqlFileToExecute);
		$sqlArray = explode(';',$sqlFile); 
		//Process the sql file by statements
		foreach ($sqlArray as $stmt) {
			if (strlen($stmt)>3){
				$result = mysql_query($stmt);
				if (!$result){
					$sqlErrorCode = mysql_errno();
					$sqlErrorText = mysql_error();
					$sqlStmt      = $stmt;
					break;
				}
			}
		} 
		if ($sqlErrorCode == 0){
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * check if tmp dir is empty
		*
	 * @param path dir
	 * 
	 */
	public function dirIsEmpty($dir){
		 return (($files = @scandir($dir)) && count($files) <= 2); 	
	}
	
	/**
	 * Recursive Directory copy
		*
	 * @param path srcFolder
	 * @param path dstFolder
	 * 
	 */
	private function recurse_copy($source, $destination) { 
		if ( is_dir( $source ) ) {
			@mkdir( $destination );
			$directory = dir( $source );
			while ( FALSE !== ( $readdirectory = $directory->read() ) ) {
				if ( $readdirectory == '.' || $readdirectory == '..' ) {
					continue;
				}
				$PathDir = $source . '/' . $readdirectory; 
				if ( is_dir( $PathDir ) ) {
					$this->recurse_copy( $PathDir, $destination . '/' . $readdirectory );
					continue;
				}
				copy( $PathDir, $destination . '/' . $readdirectory );
			}
			
			$directory->close();
			}else {
			copy( $source, $destination );
		}
	} 

} // end class
?>