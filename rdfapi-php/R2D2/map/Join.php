<?php

// ----------------------------------------------------------------------------------
// Class: Join
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

class Join{
	
	/**
	 * Enter description here...
	 *
	 * @var string array
	 * @access private
	 */
	
	var $fromColumn = array();
	
	var $toColumn = array();
	
	var $fromTable;
	
	var $toTable;
	
	/**
	 * contains two join columns in both directions
	 * (col1 - col2)
	 * (col2 - col1)
	 *
	 * @var string array
	 */
	var $relations = array();
	
	/**
	 * contains a SQL expression for querying with a join condition
	 *
	 * @var string $SQL Expression
	 */
	var $SQLexpression;
	
	
	// ------------------------------------------------------------------------------------------------------------
	
	function Join(){
		$this->fromTable = null;
		$this->toTable = null;
		$this->fromColumn = array();
		$this->toColumn = array();
		$this->SQLexpression = null;
		$this->relations = array();
	}
	
	
	/**
	 * creates a new Join 
	 *
	 * @param string $join condition
	 * @return Join $join
	 */
	
	function buildJoin ($join){
		
		$join = strtoupper($join);
		
		$join = new Join();
		$this->newcondition($join);
		return $join;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array strings $joins
	 * @return array strings
	 */
	function buildJoins($joins){
				
		$result = array();
		$condition = null;
		for(reset($joins); $condition=key($joins);next($joins)):
		
			$table1 = Join::getColumn($condition,1);
			$table1 = $table1->getTableName();
			$table2 = Join::getColumn($condition,2);
			$table2 = $table2->getTableName();
			
			
			
			// check if join exists for other conditions
			for ($j=0; $j<count($result);$j++){
				$join = $result[$j];
				if($join->containsTable($table1) && $join->containsTable($table2)){
					$join->newCondition($condition);
					$j = count($result);   // end this for turn
					$condition = null;
				}
			}
				
			if ( $condition != null){
				$join = new Join();
				$join->newcondition($condition);
				array_push($result,$join);
			}
			
			
		endfor;	
		
		return $result;
	}
	
	/**
	 * get the column specified in a join condition from one of the tables.
	 * 
	 * Parameter $table have to be 1 or 2. 
	 * 1 is for first table column (before =)
	 * 2 is for seconde table column (after =)
	 *
	 * @param string $join
	 * @param int $table (1 or 2)
	 * @return column
	 */
	function getColumn($join,$table){
		
		$pos = strpos($join, '=');
		if($pos === false){  
			logging::error("d2rq:join: \'".$join."\' wrong syntax. use: \'table1.col1=Table2.col2\'");
		}
		
		$column1 = substr($join,0,$pos);  $column1 = trim($column1);
		$column2 = substr($join,$pos+1);  $column2 = trim($column2);
		
		if ($table === 1)
			return new Column($column1);
		else if ($table === 2)
			return new Column($column2);
		else{
			logging::error("wrong table number specified in \"getColumn\" from class join. use 1 or 2");
			return null;
		}
	}
	
	/**
	 * adds a new condition 
	 *
	 * @param string $condition
	 */
	function newcondition($condition){
		$column1 = $this->getColumn($condition,1);
		$column2 = $this->getColumn($condition,2);
		
		if ($this->fromTable == null){
			$this->fromTable = $column1->getTableName();
			$this->toTable   = $column2->getTableName();
		}
		
		array_push($this->fromColumn,$column1);
		array_push($this->toColumn,$column2);
		
		
		$this->relations[$column1->getQualifiedName()] = $column2->getQualifiedName();
		$this->relations[$column2->getQualifiedName()] = $column1->getQualifiedName();
	
	}
	
	
	function generateSQL(){
		if (null !== $this->SQLexpression)
			return $this->SQLexpression;
		
		$sql = null;
			
		for ($i=0; $i < count($this->fromColumn); $i++){
			if ($i > 0)
				$sql = $sql." AND ";
			
			$column    = $this->fromColumn[$i];
			$relcolumn = $this->getJoinRelation($column->getQualifiedName());
			
			$sql = $sql . $column->getQualifiedName();
			$sql = $sql . "=";
			$sql = $sql . $relcolumn;
		}
		
		$this->SQLexpression = $sql;
		
		return $this->SQLexpression;
		
	}
	
	
	
	function getJoinRelation($column){
		return $this->relations[$column];
	}
	
	/**
	 * checks if a table name exists
	 *
	 * @param string $table
	 * @return boolean
	 */
	function containsTable($table){
		if ( (0 === strcasecmp($table,$this->fromTable)) || (0 === strcasecmp($table,$this->toTable)) )
			return true;
		else return false;
	}
	
	function getFromTable(){
		return $this->fromTable;
	}
	
	function getToTable(){
		return $this->toTable;
	}
	
	function getFromColumn(){
		return $this->fromColumn;
	}
	
	function getToColumn(){
		return $this->toColumn;
	}
	
	
	/**
	 * checks if a column name exists
	 *
	 * @param string $table
	 * @return boolean
	 */
	function containsColumn($column){
		if ( (0 === strcasecmp($column,$this->fromColumn)) || (0 === strcasecmp($column,$this->toColumn)) )
			return true;
		else return false;
	}
	
	
}
?>