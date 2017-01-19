<?php


// ----------------------------------------------------------------------------------
// Class: SQLMaker
// ----------------------------------------------------------------------------------


/**
 * This class stores all information of a SQL query and generates a SQL query through 
 * method getSQLStatement. Wil be uses by class TripleResultSets.
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

define ("escapePattern", "([\\\\']");
define ("escapeReplacement", "\\\\$1");
	
	
class SQLMaker{
	
	
	/**
	 * contains the database object
	 *
	 * @var database object
	 */
	var $database;
	
	
	var $sqlSelect = array();
	var $sqlFrom = array();
	var $sqlWhere = array();
	
	var $columnNameNumberMap = array(); // Maps column names into internal column numbers in result set
	
	var $selectColumnCount = 0;
	
	var $eliminateDuplicates = false;
	
	var $aliasMap = array();
	var $referedTables = array();
	
	var $offset = 0;
	var $limit = false;
	
	/**
	 * Constructor
	 *
	 * @param object database $database
	 * @return SQLMaker
	 * @access public
	 */
	function SQLMaker ($database){
		$this->database = $database;
	}
	
	/**
	 * returns the database for this sql query
	 *
	 * @return object database
	 * @access public
	 */
	function getDatabase(){
		return $this->database;
	}
	
	/**
	 * generates a SQL query 
	 *
	 * @return string SQLStatement
	 * @access public
	 */
	function getSQLStatement(){
		$sql = "SELECT ";
		
		if($this->eliminateDuplicates)
			$sql .= "DISTINCT ";
		
		$len = count($this->sqlSelect);
		if ($len == 0)
			$sql .= "1";
		else{
			$i=0;
			for(reset($this->sqlSelect); $current=current($this->sqlSelect);next($this->sqlSelect)):
				if($i>0) $sql.=", ";
				$sql.=$current;
				$i++;
			endfor;		
		}
		
		$sql.= " FROM ";
		
		$sql.= $this->sqlFromExpression($this->referedTables, $this->aliasMap);

		if ($this->sqlWhere != null){
			$sql.=" WHERE ";
			$i=0;
			for(reset($this->sqlWhere); $current=current($this->sqlWhere);next($this->sqlWhere)):
				if($i>0)
					$sql.=" AND ";	
				
				$sql.=$current;
				
				$i++;
			endfor;	
		}
		
		if($this->limit)
			$sql .= " LIMIT ". $this->limit;
		
		if ($this->offset != 0)
			$sql .= " OFFSET " .$this->offset;
			
		//$sql .=";";
		
		return (string) $sql;

	}
	
	/**
	 * Sets a column a SQL query points on
	 *
	 * @param Column $column
	 * @access private
	 */
	function referColumn($column){
		$tablename = $column->getTableName();
		$this->referTable($tablename);
	}

	/**
	 * Sets all columns a SQL query points on 
	 * uses method referColumn
	 *
	 * @param array string $columns
	 * @access private
	 */
	function referColumns($columns){
		for(reset($columns); $current=current($columns);next($columns)):
				$this->referColumn($current);			
		endfor;	
	}
	/**
	 * Sets the table a SQL table points on
	 * @param string table name
	 *
	 */
	function referTable($tablename){
		if(array_search($tablename, $this->referedTables) === false)
			array_push($this->referedTables,$tablename);
	}
	
	
	
	/**
	 * adds a join condition
	 *
	 * @param array string $join
	 * @access public
	 */
	function addJoins($joins){
		if($joins != null){
			for(reset($joins); $current=current($joins);next($joins)):
				$expression = $current->generateSQL();		
				if(false === array_search($expression,$this->sqlWhere)){
					array_push($this->sqlWhere,$expression);
					$this->referTable($current->getFromTable());
					$this->referTable($current->getToTable());
				}
			endfor;	
		}
	}
	
	
	/**
	 * adds a condition
	 *
	 * @param array string $conditions
	 * @access public
	 */
	function addConditions($conditions){
		if($conditions != null){
			for(reset($conditions); $current=current($conditions);next($conditions)):
				if(array_search($current,$this->sqlWhere) === false)
				array_push($this->sqlWhere,$current);
			endfor;	
		}
	}
	
	/**
	 * adds a alias map to sql object
	 *
	 * @param array string $aliasmap
	 * @public
	 */
	function addAliasMap($aliasmap){
		$this->aliasMap = $aliasmap;

	}
	
	
	/**
	 * adds a Column which will be add after SELECT
	 *
	 * @param Column $column
	 * @access public
	 */
	function addSelectColumn($column){
		$qname = $column->getQualifiedName();
		$table = $column->getTableName();
		$alias = false;
		
		if(array_search($qname,$this->sqlSelect) === false){
			
			array_push($this->sqlSelect,$qname);

			$this->selectColumnCount++;

			$this->columnNameNumberMap[$qname] = $this->selectColumnCount;
			
			$this->referColumn($column);
			
		}
	}
	
	/**
	 * adds all Columns which will be added after SELECT
	 *
	 * @param array string $columns
	 * @acces public
	 */
	function addSelectColumns($columns){
		// walk array with select columns
		for(reset($columns); $current=current($columns);next($columns)):
			// read current select
			for(reset($current); $entry=current($current);next($current)):
				$this->addSelectColumn($entry);			
			endfor;
		endfor;	
	}
	
	/**
	 * adds a column value to be query 
	 *
	 * @param Column $column
	 * @param string $value
	 * @access public
	 */
	function addColumnValue($column,$value){
		$columntype = $this->columnType($column);
		$correctlyQuotedColumnValue = $this->getQuotedColumnValue($value, $columntype);
		
		$sqlWhere = $column->getQualifiedName()."=".$correctlyQuotedColumnValue;
		
		if(!in_array($sqlWhere,$this->sqlWhere)){
			array_push($this->sqlWhere,$sqlWhere);
			$this->referColumn($column);
		}
	}
	
	/**
	 * adds all column values to be query 
	 *
	 * @param array string $ColToValues
	 * @access public
	 */
	function addColumnValues($ColToValues){
		// current ColumnMap
		for(reset($ColToValues); $curColumn=current($ColToValues);next($ColToValues)):
			// current Column and value
			for(reset($curColumn); $column=current($curColumn),$value=key($curColumn);next($curColumn)):
				$this->addColumnValue($column,$value);							
			endfor;	
		endfor;	
	}
	
	/**
	 * get type of a column
	 *
	 * @param Column $column
	 * @return int
	 * @access public
	 */
	function columnType($column){
		$databaseColumn = $column->getQualifiedName($this->aliasMap);
		
		return $this->database->getquColumnType($databaseColumn);
	}
	
	/**
	 * access columns by old pre-renaming names
	 *
	 * @param array string $renamedColumns
	 */
	function addColumnRenames($renamedColumns){
		if($renamedColumns!=null){
			for(reset($renamedColumns); $current=current($renamedColumns);next($renamedColumns)):
				
				$column = $current;
				$key =  key($column);
				$oldName = $key->getQualifiedName();
				$newName = $colum[$key];
				$newName = $newName->getQualifiedName();
		
				$this->columnNameNumberMap[$oldName] = $this->columnNameNumberMap[$newName];
			endfor;	
		}
	}
	
	function addLimit($limit){
		$this->limit = $limit;
	}
	
	function addOffset($offset){
		$this->offset = $offset;
	}
	
	
	
	/**
	 * sets a SELECT DISTINCT to eliminate duplicates if true
	 *
	 * @param boolean $distinct
	 */
	function setEliminateDuplicates($eliminate){
		$this->eliminateDuplicates = $eliminate;
	}
	
	function getColumnNameNumberMap(){
		return $this->columnNameNumberMap;
	}
	
	/**
	 * gets the formated datatype of a column
	 *
	 * @param string $value
	 * @param int $ColumnType
	 * @return string
	 * @access private
	 */
	function getQuotedColumnValue($value,$ColumnType){
		if($ColumnType == DB_numericColumnType){			
			$value = str_replace(' ','',$value);			
			return "'".$value."'";
		}
		else if ($ColumnType == DB_dateColumnType){
			return "'".$value."'";
		}
		else 
			return (string)"'".$this->escape($value)."'";
	}
	
	/**
	 * formating escape sequences
	 *
	 * @param string $string
	 * @return string
	 * @access public
	 */
	function escape($string){
		$ret =  str_replace(escapePattern, escapeReplacement, $string);
		return (string)$ret;
	}
	
	/**
	 * generates the SELECT FROM part of a sql query
	 *
	 * @param array string $referedTables
	 * @param array string $aliasMap
	 * @return string
	 * @access private
	 */
	function sqlFromExpression($referedTables,$aliasMap){
		
		$sql = null;
		$tableName = null;		
		
		$tableNames = array();
		
		for(reset($referedTables); $current=current($referedTables);next($referedTables)):
			
				
			$tableName = $current; 
			if($aliasMap != null){
				for(reset($aliasMap); $alias=current($aliasMap);next($aliasMap)):
					$aliasTable = $alias->getAliasTable();
									
					if ($aliasTable === strtoupper($tableName))
						$tableName = $alias->getSQLExpression();					
				endfor;
			}
			array_push($tableNames,$tableName);
						
		endfor;
		
		
		$i=0;
		$tablesAdded = array();
		foreach($tableNames as $tableName){
			if(!in_array(strtolower($tableName),$tablesAdded)){
				if($i>0)	
					$sql .= " , ";
				$sql .= $tableName;				
				array_push($tablesAdded,strtolower($tableName));
			}
					
			$i++;
		}
		
		return (string)$sql;
		
	}
	
	
	/**
	 * SPARQL
	 *
	 * @return unknown
	 */
	function getSQLSelect(){
		return $this->sqlSelect;
	}
	
	/**
	 * SPARQL
	 *
	 * @return unknown
	 */
	
	function getSQLFrom(){
		
		$table = array();
		$tableName = null;
		$referedTables = $this->referedTables;
		$aliasMap = $this->aliasMap;
		
		for(reset($referedTables); $current=current($referedTables);next($referedTables)):
				
			$tableName = $current; 
			if($aliasMap != null){
				for(reset($aliasMap); $alias=current($aliasMap);next($aliasMap)):
					$aliasTable = $alias->getAliasTable();
									
					if ($aliasTable === $tableName)
						$tableName = $alias->getSQLExpression();					
				endfor;
			}
						
			array_push($table,$tableName);
		endfor;	
		
		return $table;

	}
	function getSQLWhere(){
		return $this->sqlWhere;
	}
	
}

?>