<?php

// ----------------------------------------------------------------------------------
// Class: R2D2ResultIterator
// ----------------------------------------------------------------------------------


/**
 * 
 * Iterator over a result set of an find(spo) query.
 * This version still contains no iterator, but a GetAllTriples method.
 * So you are not able to move through a triple result set, but can get all found
 * triples and walk through a array manually. 
 * GetAllTriples is much more faster, than a iterator function!
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
 * @todo implement next, current, hasNext methods
 * @access	public
 * 
 * 
 * 
 * 
 */

class R2D2ResultIterator{
	
	/**
	 * contains all prepared SQL queries
	 *
	 * @var array string
	 */
	var $tripleResultSets;
	
	/**
	 * contains a copy of $triple ResultSets as a stack
	 *
	 * @var array string
	 */
	var $tripleResultSetsStack;
	

	/**
	 * all done?
	 *
	 * @var boolean
	 */
	var $finished;
	
	/**
	 * triple is prefetched?
	 *
	 * @var boolean
	 */
	var $prefetched;
	
	/**
	 * a prefetched Triple
	 *
	 * @var Statement
	 */
	var $prefetchedTriple;
	
	
	/**
	 * contains the result Set of a SQL query
	 *
	 * @var TripleResultSet
	 */
	var $prefetchedTripleResultSet;
	
	
	
	// --------------------------------------------------------------------------
	
	/**
	 * constructor of R2D2ResultIterator
	 *
	 * @return R2D2ResultIterator
	 * @access public
	 */
	function R2D2ResultIterator(){
		$this->finished = true;
		$this->tripleResultSets = array();
	}
	
	/**
	 * adds a new ResultSet to ResultSet List
	 *
	 * @param TripleResultSet object $resultSet
	 * @access public
	 */
	function addTripleResultSet($resultSet){
		array_push($this->tripleResultSets,$resultSet);
		$this->tripleResultSetsStack = $this->tripleResultSets;
		
		$this->finished = false;
	}
	
	function getTripleResultSet(){
		return $this->tripleResultSets;
	}
	
	
	/**
	 * Test if there is a next result in list
	 * !! TO DO !! Doesn t work yet
	 *
	 * @return boolean
	 * @access public
	 */
	function hasNext(){
		return true;
		//if(!$this->finished && !$this->prefetched) $this->NextQuery();
		//return !$this->finished;
	}
	
	/**
	 * Takes the next Triple from TripleResultSet - List
	 *
	 * @return Statement $triple
	 * @access public
	 */
	function next(){	
		if(!$this->finished && !$this->prefetched) $this->NextQuery();
		$this->prefetched = false;
		
		return $this->prefetchedTriple;  // return the next triple
	}
	
	
	/**
	 * moves to next triple in result set
	 * * !! TO DO !! Doesn t work yet
	 *
	 * @access private
	 */
	function NextQuery(){
	  if(!$this->finished){
		if($this->prefetchedTripleResultSet == null)
			$this->prefetchedTripleResultSet = array_pop($this->tripleResultSetsStack);
			
		if($this->prefetchedTripleResultSet != null){
			if( $this->prefetchedTripleResultSet->hasNext()){
					$this->prefetchedTriple = $this->prefetchedTripleResultSet->next();
					$this->prefetched = true;
			}
		}
		else {
			// close current prefetched Triple
			$this->prefetchedTripleResultSet->close();
			
			// search for next Triple
			$this->prefetchedTripleResultSet = array_pop($this->tripleResultSetsStack);
			if($this->prefetchedTripleResultSet != null){
				$this->NextQuery();
			}
			else $this->close(); // no Triple Result Sets
		}
	  } else $$this->close();
		
	}
	
	/**
	 * creates all triples from a given search pattern on all possible PropertyBridges and ClassMaps
	 *
	 * @return array string Statements
	 */
	function GetAllTriples(){
	
	  $resultList = array();
		
	  if(!$this->finished){	
	  	do{	
			// copy the current prepared query Information from one PropertyBridge from the ResultSet stack
			$this->prefetchedTripleResultSet = array_pop($this->tripleResultSetsStack);
			
			if($this->prefetchedTripleResultSet != null){
				
				// get a list of all triples generated from a SQL query
				$curResultSetTriplesList = $this->prefetchedTripleResultSet->GetAllTriplesFromResultSet();
				// copy result triples into return list
				$resultList = array_merge($resultList,$curResultSetTriplesList);
			}
		}while ($this->prefetchedTripleResultSet != null);
		$this->close(); // all triples created
		$this->finished = true;
		return $resultList;
	  } else {
	  	$this->close();
	  	return;
	  }
		
	}
	
	/**
	 * closes the current tripleResultSet and set free all resources
	 * @access public
	 *
	 */
	function close(){
		if ($this->finished == false){
			$resultset = $this->tripleResultSets;
			for(reset($resultset); $current=current($resultset);next($resultset)):
				$current->close();
			endfor;	
			
		}
		$this->finished = true;
	}

}


?>