<?php
// ----------------------------------------------------------------------------------
// Class: TripleResultSet
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
 * @package find
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

class TripleResultSet extends SQLResultSet {	
	
	var $tripleMakers = array();
	
	var $tripleMakersStack;
	
	var $rsForward = false;
	
	var $cachedTriples;
	
	var $columnNameNumberMap = array();
	
	
	/**
	 * Enter description here...
	 *
	 * @param string $SQL
	 * @param array string $ColumnNameNumberMap
	 * @param Database $database
	 * @return TripleResultSet
	 */
	function TripleResultSet($SQL,$ColumnNameNumberMap,$database){
		SQLResultSet::SQLResultSet($SQL,$ColumnNameNumberMap,$database);
	}
	
	/**
	 * adds a new TripleMaker to generate a triple from a SQL ResultSet
	 *
	 * @param unknown_type $tripleMaker
	 */
	function addTripleMaker($tripleMaker){
		array_push($this->tripleMakers,$tripleMaker);
	}
	
	function getTripleMaker(){
		return $this->tripleMakers;
	}
	
	
	/**
	 * checks if there are any TripleMakers for generating a Triple
	 * @return boolean
	 */
	function hasTripleMakers(){
		if( count ($this->tripleMakers > 0))
		return 	true;
		else return false;
	}
	
	/**
	 * checks if there are more Triple genereted
	 */
	function hasNext(){
		if ($this->cachedTriples == null)
			$this->cachedTriples = $this->next();
		
		if ($this->cachedTriples != null)
			return true;
		else 
			return false;
	}
	
	/**
	 * Generates a Triple from a given SQL ResultSet
	 *
	 * @return Statement
	 */
	function next(){
		if (!$this->hasTripleMakers())
			return null;
		if ($this->cachedTriples != null){
			$triple = $this->cachedTriples;
			$this->cachedTriples = null;
			return $triple;
		}
		
		if (!$this->queryHasBeenExecuted){
			$this->execSQLQuery();
			$this->queryHasBeenExecuted  = true;
		}
		
		if( $this->rsForward && $this->tripleMakersStack != null)
			$this->rsForward = false;
		
		if (!$this->rsForward){
			// get current row entry
			$this->currentRow = $this->nextRow();
			if($this->currentRow == null)
				return null;
			
			$this->tripleMakersStack = $this->tripleMakers;
			$this->rsForward = true;
		}
		
		// generating triple from resultset
		$tripleMaker = array_pop($this->tripleMakersStack);
		$triple = $tripleMaker->makeTriple($this->currentRow, $this->columnNameNumberMap);
		if ( $triple != null)
			return $triple;  
		else return $this->next();
		
		
	}
	
	/**
	 * execute a SQL query and generate triples from every row
	 *
	 * @return array string
	 * @access private
	 */
	function GetAllTriplesFromResultSet(){
		
		$returnList = array();
		
		 
		$this->execSQLQuery();
		
		do{
			$currentRow = $this->nextRow();
			if($currentRow!=null){
				
				for($i=0;$i<count($this->tripleMakers);$i++){
						$triple = $this->tripleMakers[$i]->makeTriple($currentRow, $this->columnNameNumberMap);
						if ( $triple != null)
							array_push($returnList,$triple);
				}
			}
		}while ($currentRow != null);
		return $returnList;
	}

	function getColumnNameNumberMap(){
		return $this->columnNameNumberMap;
	}
}