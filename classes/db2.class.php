<?php
/**
   * PDO DB Class
   * @version 0.1
   * @author GEoffrey Hale
   * @URL https://gist.github.com/geoffreyhale/57ca48bc97a7a954e9d5
   */
include_once("/home/rconfig/config/config.inc.php");

class db2{
    private $host   = DB_HOST;
    private $port   = DB_PORT;
    private $user   = DB_USER;
    private $pass   = DB_PASSWORD;
    private $dbname = DB_NAME;
    
    private $dbh;
    private $error;
    
    private $stmt;

    public function __construct()
    {
        // Set DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';port=' . $this->port;
        // Set options
        $options =  array(
                PDO::ATTR_ERRMODE            	=> PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT 		=> true,
                PDO::ATTR_DEFAULT_FETCH_MODE 	=> PDO::FETCH_ASSOC
        );
        //Create a new PDO instance
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        }
        // Catch any errors
        catch(PDOException $e) {
            $this->error = $e->getMessage();
        }
    }

 
    public function query($query)
    {
        $this->stmt = $this->dbh->prepare($query);
    }
    
    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        $this->stmt->bindValue($param, $value, $type);
    }
    
    public function execute()
    {
        return $this->stmt->execute();
    }
    
    public function resultset()
    {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function single()
    {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function rowCount()
    {
        return $this->stmt->rowCount();
    }
    
    public function lastInsertId()
    {
        return $this->dbh->lastInsertId();
    }
    
    /**
     * Transactions allow multiple changes to a database all in one batch.
     */
    public function beginTransaction()
    {
        return $this->dbh->beginTransaction();
    }
     
    public function endTransaction()
    {
        return $this->dbh->commit();
    }
    
    public function cancelTransaction()
    {
        return $this->dbh->rollBack();
    }
    
    public function debugDumpParams()
    {
        return $this->stmt->debugDumpParams();
    }
 
    /**
     * mysql_get_server_info - with a PDO Twist
     * http://www.php.net/manual/en/function.mysql-get-server-info.php
     */
    public function mysql_get_server_info_PDO() {
        return $this->dbh->getAttribute(PDO::ATTR_SERVER_VERSION);
    }                

    /**
     * mysql_get_host_info - with a PDO Twist
     * http://www.php.net/manual/en/function.mysql-get-server-info.php
     */
    public function mysql_get_host_info_PDO() {
        return $this->dbh->getAttribute(PDO::ATTR_CONNECTION_STATUS);
    }


    /**
       * @function 			__destruct   
       * @description 		closes mysql connection.
       */
    public function __destruct(){
            $this->dbh = null;
    }
    
    
    
     /**
       * @function 		q  (shortening for query) 
       * @description 		runs mysql query and returns php array.
       * @param string 		$qry 	Mysql Code.
       * @return 		mysql result in assoc array;
       */
    public function q($qry) {
        try {
            $stmt = $this->dbh->prepare($qry); 
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
}
 
    


    /**
       * @function 		UPDATE  (shortening for update) 
       * @description 		runs mysql update query
       * @param string 		$qry 	Mysql Code.
       * @return 		nothing
       */
//    public function update($qry) {
//        try {
//            $stmt = $this->dbh->prepare($qry); 
//            $stmt->execute();
//            return true;
//        } catch (PDOException $e) {
//            error_log($e->getMessage());
//            return false;
//        }
//    }
    

