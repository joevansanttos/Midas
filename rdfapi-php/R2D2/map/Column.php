<?php

// ----------------------------------------------------------------------------------
// Class: Column
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

//require_once(R2D2_INCLUDE_DIR . 'map/DBSource.php');
class Column {
	
	
	/**
	 * the current qualified Name "table.column", table name and column name
	 *
	 * @var string
	 * @access private
	 */
	var $qualifiedName;
	var $tableName;
	var $columnName;
	
	var $type = DBSOURCE_COLUMN;
	
	
	
	/**
	 * Constructor to construct a new Column from a complete Columnname
	 *
	 * @param string $fullName
	 * @return column
	 * @access public
	 */
	
	function Column($fullName){
		$pos = strpos($fullName,".");
		if ( $pos === FALSE){
			Logging::error("\"".$fullName."\" is not in \"table.column\" notation\n < /br>");
		}
		
		$this->qualifiedName = $fullName;
		$this->tableName = substr($fullName,0, $pos);
		$this->columnName = substr($fullName, $pos+1);
		
	}
	
	
	function column_table_col($table,$column){
		$this->qualifiedName = $table.".".$column;
		$this->tableName = $table;
		$this->columnName = $column;
	}
	
	/**
	 * creates table.column with parameters table and column
	 *
	 * @param string $table
	 * @param string $column
	 * @access public
	 * @return unknown
	 */
	function addTableColumn($table,$column){
		return $table.".".$column;	
	}
	
	//function addTablePrefix ($prefix){	
	//	}
	
	/**
	 * returns the current column name
	 *
	 * @return string column name
	 */
	function getColumnName(){
		return $this->columnName;
	}
	
	/**
	 * returns the current qualified column name "table.column"
	 *
	 * @return string column name
	 * @access public
	 */
	function getQualifiedName(){
		return $this->qualifiedName;
	}
	
	/**
	 * returns the current table name
	 *
	 * @return string table name
	 * @access public
	 */
	function getTableName(){
		return $this->tableName;
	}
	
	/**
	 * returns the value (or intern number) of a column in a database row
	 *
	 * @param string $value
	 * @return array string 
	 * @access public
	 */
	function getColumnValues($value){
		$returnvalue = array();
		$returnvalue[$value] = $this;
		
		return $returnvalue;
	}
	
	/**
	 * check if a other column is equal to current column.
	 * Two columns are fully equal if the have the same qualified Name.
	 *
	 * @param unknown_type $column
	 * @return unknown
	 */
	function equals ($column){
		// check if $column is a column object
		if (!(is_a($column, column))){
			return FALSE;
		}
					
		if (strcmp($column->getQualifiedName(), $this->qualifiedName) === 0)
		return TRUE;
		else 
		return FALSE;
	}	
	
	/**
	 * Enter description here...
	 *
	 * @param string $anonID
	 * @return boolean
	 * @access public
	 */
	function couldFit($value){
		return true;
	}
	
	/**
	 * returns the array of all defined columns for current class Map
	 *
	 * @return string array $columns
	 * @access public
	 */
	function getColumns(){
		$result = array($this);
		return $result;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param array string $row
	 * @param array string $columnNameNumberMap
	 * @return string
	 * @access public
	 */
	function getValue($row,$columnNameNumberMap){
		$index = $columnNameNumberMap[$this->qualifiedName];
		$index-=1; // Index begins with 0; columnNameNumberMap begins with 1 --> MapNumber -1<b></b>
		return $row[$index];
	}
}

?>