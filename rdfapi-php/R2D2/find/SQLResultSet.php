<?php
// ----------------------------------------------------------------------------------
// Class: SQLResultSet
// ----------------------------------------------------------------------------------


/**
 * 
 * This class offers methods to access the result set of a SQL query
 * 
 * <BR><BR>History:<UL>
 * <LI>01-06-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package find
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);

class SQLResultSet{
	
	/**
	 * map: Columnname <-> internal number
	 *
	 * @var array string
	 * @access protected
	 */
	var $columnNameNumberMap = array();
	
	
	/**
	 * a row
	 *
	 * @var array string
	 * @access protected
	 */
	var $currentRow = array();
	
	/**
	 * contains the database
	 *
	 * @var Database
	 * @access protected
	 */
	var $database;
	
	/**
	 * a SQL result set
	 *
	 * @var    array()
	 * @access private
	 */
	var $resultSet=null;
	
	var $SQL;
	
	var $numCol = 0;
	
	/**
	 * a list with all result of an SQL resultSet
	 *
	 * @var array string
	 */
	var $resultList=null;
	
	
	
	/**
	 * constructor of class SQLResultSet
	 *
	 * @param string $SQL
	 * @param array string $ColumnNameNumberMap
	 * @param Database $database
	 * @return SQLResultSet
	 * @access protected
	 */
	function SQLResultSet($SQL,$ColumnNameNumberMap,$database){
		$this->SQL = $SQL;
		$this->columnNameNumberMap = $ColumnNameNumberMap;
		$this->database = $database;
	}
	
	
	/**
	 * execution of an SQL query on database.
	 * The funtion checks, if query has been executed yet and connects to database if a generated query don´t exist.
	 * After all the resultSet is stored.
	 * @access private
	 *
	 */
	function execSQLQuery(){
		
		// check if SQL query has been executed yet
		/*if(array_key_exists($this->SQL,$this->SQLlist))	
			// SQL has been executed -> take saves results/ dont query the database
			$this->resultSet = $this->SQLlist[$this->SQL];
		else{*/
		
			// connect to DB
			$con = $this->database->getConnection();
	    
			$this->resultSet = &$con->execute($this->SQL);
		    if (!$this->resultSet)
      				echo $con->errorMsg();
   		
			// get MetaData
			$this->numCol = $this->resultSet->FieldCount();
			$numRows = $this->resultSet->RecordCount();
			//echo "Number of rows in ResultSet: ".$numRows."<br>";
			
			// save result set in the SQL queries list
			//$this->SQLlist[$this->SQL] = $this->resultSet;
			
	//	}
	}
	
	/**
	 * close current resultSet
	 * @access public
	 *
	 */
	function close(){
		if ( $this->resultSet == null)
			return;
			
		$this->resultSet->close();
		$this->resultSet = null;
		$this->queryHasBeenExecuted = false;
	}
	
	/**
	 * get the current Row from result set as an array to generate a triple
	 *
	 * @return array string
	 * @access protected
	 */
	function nextRow(){

		if($this->resultSet->EOF)
			return null;	

		$result = array();
		$i=0;
		for ( $i;$i<$this->numCol;$i++){
				$result[$i] = $this->resultSet->fields[$i];
		}
		
		
		// move internal cursor no next row
		$this->resultSet->moveNext();
		
		return $result;
	}
	
}