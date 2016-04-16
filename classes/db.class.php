<?php
/**
   * EDB Class -- Easy Database Class
   * @version 0.1.3
   * @author Eduards Marhelis <eduards.marhelis@gmail.com>
   * @link http://code.google.com/p/edb-php-class/
   * @copyright Copyright 2010 Eduards Marhelis
   * @license http://www.opensource.org/licenses/mit-license.php MIT License
   * @package EDB Class
   */
include_once("/home/rconfig/config/config.inc.php");


class db{
	private	$connection		=	false;
	public	$debug			=	false; //debuging all
	public	$res			=	0; //last result data
	public	$line			=	0; //last line data
	public	$one			=	0; //last one data
	public	$queryAll		= 	array();
	public	$queryCount		= 	0; //tatal query count
	public	$queryTime		= 	0; //total query time
	public	$cacheDir		=	'./dbcache/';
	public	$utf8Cache		=	false; //use only when you have 
	public 	$host			=	DB_HOST;
	public 	$user			=	DB_USER;
	public 	$pass			=	DB_PASSWORD;
	public 	$db				=	DB_NAME;
	
	/**
	   * @function 			__Construct 
	   * @description 		Connects to database when created new edb(); object.
	   * @param string 		$host 	Database Host.
	   * @param string 		$user 	Database user.
	   * @param string 		$pass 	Database pass.
	   * @param string 		$db 	Database name.
	   * @return 			nothing.
	   */
	public function __construct(){
	$host = DB_HOST;
	$user = DB_USER;
	$pass = DB_PASSWORD;
	$db = DB_NAME;
		$data = $host;
		if(is_array($data)){
			$host = $data[0];
			$user = $data[1];
			$pass = $data[2];
			$db   = $data[3];
		}
			$this->connection = mysql_connect($host, $user, $pass) or die(mysql_error());
			mysql_select_db($db) or die(mysql_error());
		
	}
	/**
	   * @function 			q  (shortening for query) 
	   * @description 		runs mysql query and returns php array.
	   * @param string 		$qry 	Mysql Code.
	   * @return 			mysql result;
	   */
    public function q($qry)
    {
        return mysql_query($qry);
    }
	/**
	   * @function 			line   
	   * @description 		runs mysql query and returns php array with line from db.
	   * @param string 		$a 	Mysql Code.
	   * @return 			array();
	   */
	public function line($a,$c=0,$t=30){
		$cacheFile = $this->cacheDir . md5($a) .'.cache';
		if($c && is_file($cacheFile) && (time()-filemtime($cacheFile))<$t){
			$this->line = $this->getCache($cacheFile,$a);
		}else{
			$start	=	microtime(1);
			$query = mysql_query("$a", $this->connection);
			$this->line = mysql_fetch_array( $query );
			$end	=	microtime(1);
			if($c) { $this->setCache($cacheFile,$this->line,$a); }
			$this->debugData($start,$end,$a);
			
		}
		return $this->line;
	}
	/**
	   * @function 			s   
	   * @description 		runs mysql query and returns result from mysql query. used for inserts and updates. 
	   * @param string 		$a 	Mysql Code.
	   * @return 			string.
	   */
	public function s($a){
		$start	=	microtime(1);
		$q = mysql_query("$a", $this->connection) or die(mysql_error());  
		$end	=	microtime(1);
		$this->debugData($start,$end,$a);
		return $q;
	}
	
	private function setCache($file,$result,$q,$o=true){
		$fh = fopen($file, 'w') or die("can't open file");
		if($o) { fwrite($fh, json_encode($result)); }
		else{ fwrite($fh, $result); }
		fclose($fh);
	}

	private function getCache($file,$a,$o=true){
		$start	=	microtime(1);
		$fh = fopen($file, 'r');
		$data = fread($fh, filesize($file));
		fclose($fh);
		if($o) { $data = (array)json_decode($data); }
		$end	=	microtime(1);
		$this->debugData($start,$end,$a,'cache');
		return $data;
	}
	   
	private function debugData($start,$end,$a,$b='DB'){
		$this->queryCount++;
		$t = number_format($end - $start, 8);
		$this->queryTime = $this->queryTime + $t;
		$this->queryAll[ $this->queryCount ] = array('query'=>$a,'time'=>$t,'type'=>$b);
	}
	
	//select * from table
	public function selectAll($a,$c=0,$t=30){
		$query = "SELECT * FROM `$a`";
		return $this->q($query,$c,$t);
	}
	
	//insert data $db->insert($table,$data);
	public function insert($a,$b){
		$q = "INSERT INTO $a (";
		foreach($b as $c=>$d){
			$q .= "`$c`,";
		}
		$q = substr($q,0,-1);
		$q .= ") values (";
		foreach($b as $c=>$d){
			$q .= "'$d',";
		}
		$q = substr($q,0,-1);
		return $this->s($q.');');
	}
	
	//update row or rows, $db->update($tableName,$updateValues,$whereValues);
	public function update($a,$b,$c){
		$q = "UPDATE `$a` SET ";
		foreach($b as $v=>$k){
			$q .= "`$v`='$k',";
		}
		$q = substr($q,0,-1);
		$q .= " WHERE 1";
		foreach($c as $v=>$k){
			$q .= " AND `$v`='$k'";
		}
		return $this->s($q);
	}
	//get last inserted ID	
	public function lastID()
    {
      return mysql_insert_id();
    }
	/**
	   * @function 			__destruct   
	   * @description 		closes mysql connection.
	   */
	public function __destruct(){
		mysql_close($this->connection);
	}
}
?>