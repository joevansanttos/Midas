<?php

// ----------------------------------------------------------------------------------
// Class: QueryCombiner
// ----------------------------------------------------------------------------------


/**
 * 
 * The class QueryCombiner combines multiple queries into a single SQL query, if it is possible.
 * Through method add a Triple Query may be add
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
 */

class QueryCombiner{
	
	var $compatibleQueries = array();
	
	/**
	 * adds a new SQL query object to the list of compatible Queries
	 *
	 * @param TripleQuery $newQuery
	 * @access public
	 */
	function add($newQuery){
		
		$it = $this->compatibleQueries;
		
		$i=0;
		for ($i; $i < count($this->compatibleQueries); $i++){
				$current = $this->compatibleQueries[$i];
				if($newQuery->isCombinable(reset($current))){
					array_push($this->compatibleQueries[$i],$newQuery);
					return;
				}
		}
		
		$list = array();
		array_push($list,$newQuery);
			
		array_push($this->compatibleQueries,$list);
	}
	
	/**
	 * Takes every compatible SQL query and builds a Triple Result Set.
	 * The Triple Result Set contains information of a SQL query.
	 * @return R2D2ResultIterator
	 * @access public
	 */
	function getResultIterator(){
		$result = new R2D2ResultIterator();
		$it = $this->compatibleQueries;
		for(reset($it); $querylist=current($it);next($it)):
			$tripleResultSet = $this->getTripleResultSet($querylist);
			$result->addTripleResultSet($tripleResultSet);
		endfor;	
		
		return $result;
	}
	
	
	/**
	 *  extract all information from a query object for generating a SQL query
	 *  and returns a SQLMaker object
	 *
	 * @param array string $queries
	 * @return SQLMaker object
	 * @access private
	 */
	function getSQL($queries){
	
		$querylen = count ($queries);
		
		
		$result = new SQLMaker($queries[0]->getDatabase());
	
		$propertyBridge = $queries[0]->getPropertyBridge();
		$result->addAliasMap($propertyBridge->getAliasNames());
		$result->addJoins($queries[0]->getJoins());
		$result->addColumnValues($queries[0]->getColumnValues());
		$result->addConditions($queries[0]->getConditions());
		$result->addSelectColumns($queries[0]->getSelectColumns());
		$result->addColumnRenames($queries[0]->getReplacedColumns());
		
		$result->addLimit($queries[0]->limit);
		$result->addOffset($queries[0]->offset);
		
		$i=1;
		for ($i;$i<$querylen;$i++){
			$result->addSelectColumns($queries[$i]->getSelectColumns());
			$result->addColumnRenames($queries[$i]->getReplacedColumns());
		}
			
		return $result;
	}
	
	/**
	 * Generates a TriplesResultSet object
	 *
	 * @param array list $queries
	 * @return TripleResultSet
	 * @access private
	 */
	function getTripleResultSet($queries){
		
		$sql = $this->getSQL($queries);
		$it = $queries;
		$i = 0;
		
		$result = null;
		for(reset($it); $query=current($it);next($it)):
			
			if($i==0){
						
				$result = new TripleResultSet($sql->getSQLStatement(),$sql->getColumnNameNumberMap(),$query->getDatabase());
				
				$result->addTripleMaker($query);
			}
			else{
					$result->addTripleMaker($query);	
			}
		
			$i++;
		endfor;	
		return $result;
	}
	
	/**
	 * Generates a TriplesResultSet object
	 *
	 * @param array list $queries
	 * @return TripleResultSet
	 * @access private
	 */
	function getSPARQLTripleResultSet($queries, $sql){
		
		$it = $queries;
		$i = 0;
		
		$result = null;
		for(reset($it); $query=current($it);next($it)):
			
			if($i==0){
						
				$result = new TripleResultSet($sql->getSQLStatement(),$sql->getColumnNameNumberMap(),$query->getDatabase());
				
				$result->addTripleMaker($query);
			}
			else{
					$result->addTripleMaker($query);	
			}
		
			$i++;
		endfor;	
		return $result;
	}
	
	
	function getSPARQLResultIterator($sqlMaker){
		
		$result = new R2D2ResultIterator();
		$it = $this->compatibleQueries;
		$i=0;
		for(reset($it); $querylist=current($it);next($it)):
			$tripleResultSet = $this->getSPARQLTripleResultSet($querylist,$sqlMaker[$i]);
			$result->addTripleResultSet($tripleResultSet);
			$i++;
		endfor;	
		
		return $result;
	}
	
		
	/**
	 * returns all parts to generating a SQL query
	 *
	 * @param Array $queries
	 * @return SQLMaker
	 * @access private
	 */
	function getSPARQL2SQL(){
		
		$SQLResult = array();
		$it = $this->compatibleQueries;
		
		$i=0;
		for(reset($it); $querylist=current($it);next($it)):
			$sqlMaker = $this->getSQL($querylist);
			array_push($SQLResult,$sqlMaker);		
				
		endfor;	
		
		//return $sqlMaker;
		return $SQLResult;
		
	}
	
	function getCompatibleQuery(){
		return $this->compatibleQueries;
	}
	
	

}

?>