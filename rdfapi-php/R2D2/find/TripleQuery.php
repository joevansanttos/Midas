<?php


// ----------------------------------------------------------------------------------
// Class: 
// ----------------------------------------------------------------------------------


/**
 * 
 * This class determines the database, classes and propertyBridges may be used for an
 * input triple. Through the input triple elements this class finds the correct propertybridge
 * and can after all set up the correct SQL query.
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


class TripleQuery{
	
	var $propertyBridge;
	
	var $joins = array();

	var $columnValues = array();
	
	var $selectColumns = array();
	
	var $table = array();
	
	var $replacesColumns = array();
	
	var $subjectColumns = array();
	
	var $objectColumns = array();
	
	var $subjectMaker;
	var $predicateMaker;
	var $objectMaker;
	
	var $triplePattern;
	
	var $offset;
	var $limit;
	
	
	/**
	 * constructor of a tripleQuery.
	 * 
	 *
	 * @param PropertyBridge $PropertyBridge
	 * @param Resource $Subject
	 * @param Resource $Predicate
	 * @param Resource $Object
	 * @return TripleQuery
	 * @access public
	 */
	function TripleQuery($PropertyBridge, $Subject, $Predicate, $Object, $Pattern = null, $offset =null, $limit = null){
		$this->propertyBridge = $PropertyBridge;
		
		$this->triplePattern = $Pattern;
		$this->offset = $offset;
		$this->limit = $limit;
		
		$subjectMaker = $PropertyBridge->getSubject();
		$objectMaker= $PropertyBridge->getObject();
		$predicateMaker = $PropertyBridge->getPredicate();
		
		$this->subjectColumns = $subjectMaker->getColumns();	
		$this->objectColumns = $objectMaker->getColumns();
		
		// Check if S,P,O are concrete of wildcard nodes (null)
		if ( $this->isConcrete($Subject)){
			$columnValue = $subjectMaker->getColumnValues($Subject);
			$column      = $subjectMaker->getColumns();

			// if S is concrete the FROM column will be add to SQL query
			if ($columnValue) array_push($this->columnValues, $columnValue);
			// if S is concrete the SELECT column will be add to SQL query
			if ($column) array_push($this->selectColumns,$column);
			
			$this->subjectMaker = new FixedNodeMaker($Subject);
		}
		else{
			$column = $subjectMaker->getColumns();
			if ($column) array_push($this->selectColumns,$column);
			$this->subjectMaker = $subjectMaker;
		}
		
		if ( $this->isConcrete($Predicate)){
			$columnValue = $predicateMaker->getColumnValues($Predicate);
			// if P is concrete the SELECT column will be add to SQL query
			if ($columnValue) array_push($this->columnValues,$columnValue);
			
			$this->predicateMaker = new FixedNodeMaker($Predicate);
		}
		else{
			$column = $predicateMaker->getColumns();
			if ($column) array_push($this->selectColumns,$column);
			$this->predicateMaker = $predicateMaker;
		}
		
		if ( $this->isConcrete($Object)){
			$columnValue = $objectMaker->getColumnValues($Object);
			// if O is concrete the SELECT column will be add to SQL query
			if ($columnValue) array_push($this->columnValues,$columnValue);
			
			$this->objectMaker = new FixedNodeMaker($Object);
		}
		else{
			$column = $objectMaker->getColumns();
			if ($column) array_push($this->selectColumns,$column);
			$this->objectMaker = $objectMaker;				
		}
		
		// get join conditions
		$this->joins = $PropertyBridge->getJoins();
	
		$this->removeOptionalJoins();
		
		if($this->selectColumns != null){
			$this->table = reset($this->selectColumns);
			$this->table = reset($this->table);
			$this->table = $this->table->getTableName();
		}
		else {
			$this->table = reset($this->columnValues);
			if($this->table){
				$this->table = reset($this->table);
				$this->table = $this->table->getTableName();	
			}
		}
			
	}
	
	/**
	 * checks if a node is concrete (not null or emty)
	 * return true if node is concrete
	 * 
	 * @param Resource $node
	 * @return boolean
	 * @access private
	 */
	function isConcrete($node){
		if ($node == null) return false;
		if ( (0 != strcmp($node->getLabel(),"")) ||  (0 != strcmp($node->getLabel(),null)))
			return true;
		else return false;
	}
	

	function getNodeMaker($i){
		switch($i){
			case 0: return $this->subjectMaker; break;
			case 1: return $this->predicateMaker; break;
			case 2: return $this->objectMaker;break;
			default: return null;break;
		}
	}
	
	
	function getPropertyBridge(){
		return $this->propertyBridge;
	}
	
	function getConditions(){
		return $this->propertyBridge->getConditions();
	}
	
	function getJoins(){ 
		return $this->joins;	
	}
	
	function getDatabase(){
		return $this->propertyBridge->getDatabase();
	}
	
	function getSelectColumns(){
		return $this->selectColumns;
	}
	
	function getColumnValues(){
		return $this->columnValues;
	}
	
	function containDuplicates(){
		return $this->propertyBridge->containDuplicates();
	}
	
	function getTable(){
		return $this->table;
	}
	
	function getReplacedColumns(){
		return $this->replacesColumns;
	}
	
	function getPredicateMakerID(){
		return $this->predicateMaker->getID();
	}
	
	function getSubjectColumns(){
		return $this->subjectColumns;
	}
	
	/**
	 * checks if another query may be combined with  this into one sql statement.
	 * Two queries can be combined if they access to the same database and have same join and where clauses.
	 * If they contain no joins they have to access the same columns in database.
	 *
	 * @param TripleQuery $query
	 * @return boolean  true if two queries can be combined
	 * @access public
	 */
	
	function isCombinable($query){
		
		if ($this->getDatabase() !== $query->getDatabase())
		return false;
		
		
		if ($this->getJoins() !== $query->getJoins())
		return false;
		
		if ($this->getConditions() !== $query->getConditions())
		return false;
		
		if ($this->getColumnValues() !== $query->getColumnValues())
		return false;
		
		if ($this->propertyBridge->getContainDuplicates() || $query->propertyBridge->getContainDuplicates())
		return false;
		
		if ( (null == $this->getJoins()) && ($this->getTable() !== $query->getTable()))
		return false;
				
		return true;
	
	}
	
	
	/**
	 * creates a triple from a database result row
	 *
	 * @param array string $row   a database result row
	 * @param array string $columnNameNumberMap   map from column names to integer row array
	 * @return object Statement
	 */
	function makeTriple($row, $columnNameNumberMap){
		
		$s = $this->subjectMaker->getNode($row, $columnNameNumberMap);
		$p = $this->predicateMaker->getNode($row, $columnNameNumberMap);
		$o = $this->objectMaker->getNode($row, $columnNameNumberMap);
		
		if ((null == $s) || (null == $p) ||(null == $o))
			return null;
				
		return new Statement($s,$p,$o);
	}
	
	
	/**
	 * TO DO
	 * Not supported yet
	 *
	 */
	function removeOptionalJoins(){
		if (null != $this->propertyBridge->getConditions())
			return;
		
		
			
			
	}
	
	function getAllJoinTables(){
			
		$result = array();	
		$it = $this->joins;
		$i = 0;
				
		for(reset($it); $current=current($it);next($it)):
			array_push($result, $current->getFromTable());
			array_push($result, $current->getToTable());
				
		endfor;
		
		return $result;
		
	}
	
	function getSingleJoinReferTable($table){
		$result = null;
		$it = $this->joins;
		
		for(reset($it); $current=current($it);next($it)):
			if (!$it->containsTable($table))
				continue;
		
			if($result != null)
				return null;

			$result = $it;
		endfor;
		return $result;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $table
	 * @param Join $join
	 * @return boolean
	 */
	function isOptionalTable($table,$join){
		// TO DO
		return false;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Join $join
	 * @param string $tableName
	 */
	function eliminateColumns($join, $tableName){
		// TO DO
	}
	
	function getTriplePattern(){
		return $this->triplePattern;
	}
	
}

?>