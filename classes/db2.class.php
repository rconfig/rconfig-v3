<?php
/**
   * PDO DB Class
   * @version 0.1
   * @author Stephen Stack <stephen@rconfig.com>
   */
include_once("/home/rconfig/config/config.inc.php");

class db2{
    public	$connection		=	false;
    public	$debug			=	false; //debuging all
    public	$res			=	0; //last result data
    public	$line			=	0; //last line data
    public	$one			=	0; //last one data
    public	$queryAll		= 	array();
    public	$queryCount		= 	0; //tatal query count
    public	$queryTime		= 	0; //total query time
    public	$cacheDir		=	'./dbcache/';
    public	$utf8Cache		=	false; //use only when you have 

    public function __construct(){

    /* Make connection to database */
        $dsn = 'mysql:host='.DB_HOST.';port='.DB_PORT.';dbname='.DB_NAME.';charset=utf8';
        $opt = array(
                PDO::ATTR_ERRMODE            	=> PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT 			=> true,
                PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC
        );
        // test MYSQL access or throw an error and end the script
        try {
                $this->connection = new PDO($dsn, DB_USER, DB_PASSWORD, $opt);
        } catch (PDOException $e) {
                error_log($e->getMessage());
                die();
        }
    }

    /**
       * @function 		q  (shortening for query) 
       * @description 		runs mysql query and returns php array.
       * @param string 		$qry 	Mysql Code.
       * @return 		mysql result in assoc array;
       */
    public function q($qry) {
        try {
            $stmt = $this->connection->prepare($qry); 
            $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die();
        }
        // push rows to $items array
        if ($stmt->rowCount() > 0) { 
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else if ($stmt->fetchAll(PDO::FETCH_ASSOC) == false){
            $items = array(); //send back blank array if query returns false/ blank
        }
        return $items;
    }

    /**
       * @function 		UPDATE  (shortening for update) 
       * @description 		runs mysql update query
       * @param string 		$qry 	Mysql Code.
       * @return 		nothing
       */
    public function update($qry) {
        try {
            $stmt = $this->connection->prepare($qry); 
            $stmt->execute();
        } catch (PDOException $e) {
            error_log($e->getMessage());
            die();
        }
    }
    
    /**
     * mysql_get_server_info - with a PDO Twist
     * http://www.php.net/manual/en/function.mysql-get-server-info.php
     */
    public function mysql_get_server_info_PDO() {
        return $this->connection->getAttribute(PDO::ATTR_SERVER_VERSION);
    }                

    /**
     * mysql_get_host_info - with a PDO Twist
     * http://www.php.net/manual/en/function.mysql-get-server-info.php
     */
    public function mysql_get_host_info_PDO() {
        return $this->connection->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    }

////////////////////////////////////////////////////////// ANYTHING BELOW HERE MAY NOT BE USED ///////////////////////////      
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
//            public function update($a,$b,$c){
//                    $q = "UPDATE `$a` SET ";
//                    foreach($b as $v=>$k){
//                            $q .= "`$v`='$k',";
//                    }
//                    $q = substr($q,0,-1);
//                    $q .= " WHERE 1";
//                    foreach($c as $v=>$k){
//                            $q .= " AND `$v`='$k'";
//                    }
//                    return $this->s($q);
//            }
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
		$this->connection = null;
	}
}