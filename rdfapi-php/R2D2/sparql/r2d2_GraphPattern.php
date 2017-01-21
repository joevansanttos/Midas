<?php
// ---------------------------------------------
// class: GraphPattern
// ---------------------------------------------

/**
* A graph pattern which consists of triple patterns, optional
* or union graph patterns and filters.
*
* <BR><BR>History:<UL>
* <LI>08.09.2005: Initial version</LI>
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 0.9.3
*
* @package sparql
*/

Class r2d2_GraphPattern extends Object{

	/**
	* Graphname. 0 if its in the default graph.
	*/
	var $graphname;

	/**
	* @var array The TriplePattern.
	*/
	var $triplePattern;

	/**
	* @var array A List of Constraints.
	*/
	var $constraint = array();

	/**
	* @var int Pointer to optional patterns.
	*/
	var $optional;

	/**
	* @var int Pointer to union patterns.
	*/
	var $union;

	/**
	* @var boolean TRUE if the pattern is open- FALSE if closed.
	*/
	var $open;

	/**
	* @var boolean TRUE if the GraphPattern is a construct pattern.
	*/
	var $isConstructPattern;


	/**
	* @var int The GraphPatterns id.
	*/
	var $patternId;


	/**
	* Constructor
	* @access public
	*/
	function r2d2_GraphPattern(){
		$this->open               = true ;
		$this->isConstructPattern = false;
		$this->constraint         = false;
		$this->triplePattern      = false;
	}

	/**
	* Returns the graphname.
	*
	* @return String
    * @access public
	*/
	function getGraphname(){
		return $this->graphname;
	}

	/**
	* Returns the triple pattern of the graph pattern.
	*
	* @return Array
	* @access public
	*/
	function getTriplePattern(){
		return $this->triplePattern;
	}

	/**
	* Returns a constraint if there is one false if not.
	*
	* @access public
	* @return Constraint
	*/
	function getConstraint(){
		return $this->constraint;
	}

	/**
	* Returns a pointer to an optional graph pattern.
	*
	* @return integer
	* @access public
	*/
	function getOptional(){
		return $this->optional;
	}

	/**
	* Returns a pointer to a union graph pattern.
	*
	* @return integer
	* @access public
	*/
	function getUnion(){
		return $this->union;
	}

	/**
	* Sets the graphname.
	*
	* @param  String $name
	* @access public
	* @return void
	*/
	function setGraphname($name){
		$this->graphname = $name;
	}
	/**
	* Adds a List of QueryTriples to the GraphPattern.
	*
	* @param  array $trpP
	* @access public
	* @return void
	*/
	function addTriplePattern(&$trpP){
		$this->triplePattern = $trpP;
	}

	/**
	* Adds a Constraint to the GraphPattern.
	*
	* @param  Constraint $cons
	* @access public
	* @return void
	*/
	function addConstraint(&$cons){
		$this->constraint[] = $cons;
	}
	/**
	* Adds a pointer to an optional graphPattern.
	*
	* @param  integer $pattern
	* @return void
	*/
	function addOptional(&$pattern){
		$this->optional = &$pattern;
	}

	/**
	* Adds a pointer to a union graphPattern.
	*
	* @param  integer $pattern
	* @return void
	*/
	function addUnion(&$pattern){
		$this->union = &$pattern;
	}


	/**
	* Sets the GraphPatterns Id.
	*
	* @param  integer $id
	* @return void
	*/
	function setId(&$id){
		$this->patternId = $id;
	}

	/**
	* Returns the GraphPatterns id.
	*
	* @return integer
	*/
	function getId(){
		return $this->patternId;
	}

}
// end class: GraphPattern.php
?>
