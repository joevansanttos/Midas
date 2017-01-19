<?php


// ----------------------------------------------------------------------------------
// Class: Database
// ----------------------------------------------------------------------------------


/**
 * 
 * 
 * <BR><BR>History:<UL>
 * <LI>01-06-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package map
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

/**
 * A Database class holds the configuration parameter of a database specified in a Map
 *
 */

class Database{
	
	/**
	 * Enter description here...
	 *
	 * @var unknown_type
	 * @access public
	 */
	var $odbcDSN;
	var $jdbcDSN;
	var $jdbcDriver;
	var $username;
	var $password;
	var $columnTypes;
	
	/**
	 * Database connection object
 	*
 	* @var     object ADOConnection
 	* @access	private
 	*/
	var $dbConnection;
	
	/**
	 * Flag: connected to Database or not
	 *
	 * @var boolean
	 */
	var $connected = FALSE;
	
	var $invalidColumnType=-1;

	
	// ------------------------------------------------------------------------------------------------------------
	
	
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $odbcDSN
	 * @param unknown_type $jdbcDSN
	 * @param unknown_type $jdbcDriver
	 * @param unknown_type $username
	 * @param unknown_type $password
	 * @param unknown_type $columnTypes
	 * @return Database
	 */
	function Database($odbcDSN, $jdbcDSN, $jdbcDriver, $username, $password, $columnTypes){
		$this->odbcDSN     = $odbcDSN;
		$this->jdbcDSN     = $jdbcDSN;
	 	$this->jdbcDriver  = $jdbcDriver;
	 	$this->username    = $username;
		$this->password    = $password;
		$this->columnTypes = $columnTypes;
	}
	
	
	/**
	 * checks if connected to DB. if not a new AdoDB connection is established.
	 * Funtions returns a pointer to the connection
	 *
	 * @return object ADOConnection
	 */
	function getConnection(){
		if(!$this->connected)
			$this->connectToDB();
		return $this->dbConnection;  
	}
	
	function getOdbc(){
		return $this-odbcDSN;
	}
	
	function getJdbc(){
		return $this->jdbcDSN;
	}
	
	function getJdbcDriver(){
		return $this->jdbcDriver;
	}
	
	function getUser(){
		return $this->username;
	}
	
	function getPassword(){
		return $this->password;
	}

	/**
	 * Enter description here...
	 *
	 * @param string $ColumnName qualified Name
	 * @return unknown
	 */
	function getquColumnType($ColumnName){
		$type = (int)$this->columnTypes[$ColumnName];
		if($type == null)
			return DB_noColumnType;
		
		return $type;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Column $Column
	 */
	function getColumnType($Column){
		$quName = $Column->getQualifiedName();
		return $this->getquColumnType($quName);
	}
	
	var $expressionTranslator=null;
	
	function setExpressionTranslator($expressionTranslator){
		$this->expressionTranslator = $expressionTranslator;
	}
	
	function getExpressionTranslator(){
		return $this->expressionTranslator;
	}
	
	
	/** 
	 * defines that a Database may use "DISTINCT" or not
	 * 
	 * @access public
	 * @var boolean
	 */
	var $dbUseDistinct = TRUE;
	
	function setAllowDistinct($distinct){
		$this->dbUseDistinct = $distinct;
	}
	
	function connectToDB(){
		
		$url = "";
		$driver="";
		
		if($this->odbcDSN!=null){
			
			$url = tools::cutDbNameFromURL($this->odbcDSN);
			
			//$url="jdbc:odbc:".$url;
			
			//$driver="sun.jdbc.odbc.JdbcOdbcDriver";
			$driver = tools::prepareDriver($this->odbcDSN);
			
			$dbName = tools::searchDBName($this->odbcDSN);
		}
		else if ($this->jdbcDSN !=null){

			$url= tools::cutDbNameFromURL($this->jdbcDSN);

			$dbName = tools::searchDBName($this->jdbcDSN);
			
			if($this->jdbcDriver != null){
				//$driver = $this->jdbcDriver;
				$driver = tools::prepareDriver($this->jdbcDriver);
			}
			else{
				echo "Error: Could not connect to database because of missing JDBC driver! <br>\n";
				return;
			}
		}
		$this->dbConnection = null;
		if ($url!=""){

		
				$this->DbCon($driver,$url,$dbName,$this->getUser(),$this->getPassword());
		}
		else{
			echo "Error: Could not connect to database because of missing URL! <br>\n";
			return;
		}
		$this->connected = TRUE;
				
	}
	
	/**
 	* Set the database connection with the given parameters.
 	*
 	* @param   string   $dbDriver
 	* @param   string   $host
 	* @param   string   $dbName
 	* @param   string   $user
 	* @param   string   $password
 	* @access	public
 	*/
 function DbCon ($dbDriver, $host, $dbName,
                  $user, $password) {
              	
   // include DBase Package
   require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);
                   	
   // create a new connection object
   $this->dbConnection =& ADONewConnection($dbDriver);
   
   // connect to database
   if ($user == null) $user = '';
   if ($password == null) $password = '';
   $this->dbConnection->connect($host, $user, $password, $dbName);
   
   // optimized for speed
   $this->dbConnection->setFetchMode(ADODB_FETCH_NUM);
   $ADODB_COUNTRECS = FALSE;
   
   //activate the ADOdb DEBUG mode
   if (ADODB_DEBUG_MODE =='1')
	  $this->dbConnection->debug = TRUE;
 }
	
	/**
 	* Close the DbStore.
	* !!! Warning: If you close the DbStore all active instances of DbModel from this
 	* !!!          DbStore will lose their database connection !!!
 	*
 	* @access	public
 	*/
 	function close() {
  		 $this->dbConnection->close();
  		 unset($this);
 	}

 	/**
 	 * Enter description here...
 	 *
 	 * @param Join $join
 	 */
 	function searchColumnTypes($join){
 		$columns = Join::getFromColumn();
 		for($i=0; $i<count($columns);$i++){
 			// get current Column
 			$curColumn = $columns[$i];  
 			$curColumn = $curColumn->getQualifiedName();

 			// get other column which belongs to current column
 			$relColumn = $join->getJoinRelation($curColumn);
 			$relColumn = $relColumn->getQualifiedName();
 			 			
 			if ( array_key_exists($this->columnTypes,$curColumn) )
 				if ( !array_key_exists($this->columnTypes,$relColumn) )
 					$this->columnTypes[$relColumn] = $this->columnTypes[$curColumn];
 			else if ( array_key_exists($this->columnTypes,$relColumn) )
 					$this->columnTypes[$curColumn] = $this->columnTypes[$relColumn];
 			
 		}
 	}
 	
 	/**
 	 * Enter description here...
 	 *
 	 * @param Column $column
 	 */
 	function assertHasType($column){
 		if($this->getColumnType($column) == DB_noColumnType)
 			logging::error("Column \"".$column->getQualifiedName()."\" has no corresponding d2rq:numericColumn or d2rq:textColumn statement");
 	}
 	
 	
}



?>