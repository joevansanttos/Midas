<?php
// ----------------------------------------------------------------------------------
// Class: database
// ----------------------------------------------------------------------------------


/**
 * 
 * contains all meta informations about a database to create a generic Map.
 * Contains the classe DBinfo, Table and Column.
 * A DBinfo object contains a set of tables and every table contains a set of columns.
 * 
 * <BR><BR>History:<UL>
 * <LI>01-08-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package createMap
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

class DBinfo{
	var $dbsystem;
	var $Host;
   	var $dbName;
	var $username;
	var $password;

	var $jdbcAdress;
	
	var $tables;
	
	var $dbConnection;
	
	/**
	 * constructor of class database
	 *
	 * @param string $dbDriver  correct driver name
	 * @param string $AdoDriver drivername for creating a ado connection
	 * @param string $host		full adress of the database
	 * @param string $dbName	database name
	 * @param string $user    	user name
	 * @param string $password  
	 * @return Database
	 */
	function DBinfo($dbsystem,$host,$dbname, $user = "root",$password = ""){
		$this->dbsystem = $dbsystem;
		$this->Host = $host;
		$this->dbName = $dbname;
		$this->username = $user;
		$this->password = $password;
		
		$this->setjdbcAdress();
		
		$this->tables = array();
	}
	
	
	/**
	 * connects to a database
	 *
	 */
	function DBConnection(){
		// create a new connection object
   		$this->dbConnection =& ADONewConnection($this->dbsystem);
   
   		// connect to database
   		if ($this->username == null) $this->username = '';
   		if ($this->password == null) $this->password = '';
   		
   		$this->dbConnection->connect($this->getHost(),$this->getUser(), $this->getPWD(), $this->getDBname()) or die("Can not connect to Database");
   		
   		// optimized for speed
   		$this->dbConnection->setFetchMode(ADODB_FETCH_NUM);
  		 $ADODB_COUNTRECS = FALSE;
   
   		//activate the ADOdb DEBUG mode
   		if (ADODB_DEBUG_MODE =='1')
			  $this->dbConnection->debug = TRUE;
	}
	
	function DBClose(){
		$this->dbConnection->close();
	}
	
	function getConnectionID(){
		return $this->dbConnection;
	}
	
	function readTables(){
		 $tables =  $this->dbConnection->MetaTables(); 
		 return $tables;
	}
	
	function readPrimaryKeys($table){
		 return $this->dbConnection->MetaPrimaryKeys($table); 
	}
	
	function readForeignKeys($table){
		 return $this->dbConnection->MetaForeignKeys($table); 
	}
	
	function readColumns($table){
		return $this->dbConnection->MetaColumns($table);
	}
	
	function addTable($table){
		$this->tables[$table->getTableName()]=$table;
	}
	
	function getHost(){
		return $this->Host;
	}
	
	function getDBname(){
		return $this->dbName;
	}
	
	function getUser(){
		return $this->username;
	}
	
	function getPWD(){
		return $this->password;
	}
	
	function getDBSystem(){
		return $this->dbsystem;
	}
	
	function getjdbcAdress(){
		return $this->jdbcAdress;
	}
	
	function getDriver(){
		return "com.".$this->getDBSystem().".jdbc.Driver";
	}
	
	
	
	/**
	 * returns the foreigns keys of a table
	 *
	 * @param unknown_type $table
	 * @return unknown
	 */
	function getForeignKeys($table){
		$tablename = $this->tables[$table]; 
		$foreignKeys = $tablename->getForeignKeys();
		if(count($foreignKeys)===0)
		 return null;
		else return $foreignKeys;
	}
	
	/**
	 * @return string host adress
	 * @access private
	 *
	 */
	function setjdbcAdress(){
		//$host = "jdbc:mysql://127.0.0.1:3306/wordpress";
		$host = "jdbc:".$this->dbsystem."://".$this->Host."/".$this->dbName;
		$this->jdbcAdress = $host;
	}	
	
}


/**
 * contains information about a database table
 *
 */
class DBTable {
	var $tablename;
	var $Columns;
	var $primaryKeys;
	var $foreignKeys;
	
	function DBTable($name){
		$this->tablename = $name;
		$this->Columns = array();
		$this->primaryKeys = array();
		$this->foreignKeys = array();
	}
	
	function addColumn($column){
		$this->Columns[$column->getColumnName()] = $column;
	}
	
	function addPrimaryKey($key){
		array_push($this->primaryKeys,$key);
	}
	
	function addForeignKey($keyref,$foreignkey){
		$this->foreignKeys[$keyref] = $foreignkey;
	}
	
	function getTableName(){
		return $this->tablename;
	}
	
	function getForeignKeys(){
		return $this->foreignKeys;
	}
	
}

/**
 * contains information about a database column
 *
 */
class DBColumn{
			var $name;
			var $autoIncrement; //bool
			var $binary;//bool
			var $has_default; //bool
			var $length;
			var $notNull; //bool
			var $primaryKey; //bool
			var $scale;
			var $datatype;
			

			/**
			 * Enter description here...
			 *
			 * @param string $name
			 * @param boolean $autoIncrement
			 * @param boolean $binary
			 * @param boolean $has_default
			 * @param int $length
			 * @param boolean $notNull
			 * @param string $primaryKey
			 * @param string $scale
			 * @param string $datatype
			 * @param boolean $unsigned
			 * @return Column
			 */
			function DBColumn($name, $autoIncrement,$binary,	$has_default,
			$length,$notNull,$primaryKey,$scale,$datatype){
				
			$this->name = $name;
			$this->autoIncrement = $autoIncrement; 
			$this->binary = $binary;
			$this->has_default = $has_default; 
			$this->length = $length;
			$this->notNull = $notNull; 
			$this->primaryKey = $primaryKey; 
			$this->scale = $scale;
			$this->datatype = $datatype;
			}
			
			function getColumnName(){
				return $this->name;
			}
			
			
}

?>