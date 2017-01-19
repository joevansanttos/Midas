<?php
// ---------------------------------------------
// class: Query
// ---------------------------------------------

/**
* The Class Query represents a SPARQL query.
*
*
* <BR><BR>History:<UL>
* <LI>08.09.2005: Initial version</LI>
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 0.9.3
*
* @package sparql
*/

Class r2d2_Query extends Object {

	/**
	* @var string The BASE part of the SPARQL query.
	*/
	var $base;

	/**
	* @var array Array that vontains used prefixes and namespaces.
	*/
	var $prefixes = array();

	/**
	* @var array List of result variables.
	*/
	var $resultVars = array();

	/**
	* The result form of the query.
	*/
	var $resultForm;

	/**
	* Contains the result part of the SPARQL query.
	*/
	var $resultPart;

	/**
	* @var array Contains the FROM part of the SPARQL query.
	*/
	var $fromPart = array();

	/**
	* @var array Contains the FROM NAMED part of the SPARQL query.
	*/
	var $fromNamedPart = array();

	/**
	* @var array Optional solution modifier of the query.
	*/
	var $solutionModifier = array();

	/**
	* @var int Blanknode counter.
	*/
	var $bnodeCounter;

	/**
	* @var int GraphPattern counter.
	*/
	var $graphPatternCounter;

	/**
	* @var array List of all vars used in the query.
	*/
	var $usedVars;

	/**
	* If the query type is CONSTRUCT this variable contains the
	* CONSTRUCT graph pattern.
	*/
	var $constructPattern;

	/**
	* @var boolean TRUE if the query is empty FALSE if not.
	*/
	var $isEmpty;


	/**
	* Constructor
	*/
	function r2d2_Query(){
		$this->resultForm = false;
		$this->solutionModifier['order by'] = 0;
		$this->solutionModifier['limit']    = 0;
		$this->solutionModifier['offset']   = 0;
		$this->bnodeCounter = 0;
		$this->graphPatternCounter = 0;

	}

	/*
	* Returns the BASE part of the query.
	*
	* @return String
	*/
	function getBase(){
		return $this->base;
	}

	/*
	* Returns the prefix map of the query.
	*
	* @return Array
	*/
	function getPrefixes(){
		return $this->prefixes;
	}

	/*
	* Returns a list containing the result vars.
	*
	* @return Array
	*/
	function getResultVars(){
		return $this->resultVars;
	}

	/*
	* Returns a list containing the result vars.
	*
	* @return Array
	*/
	function getResultForm(){
		return $this->resultForm;
	}
	/*
	* Returns a list containing the graph patterns of the query.
	*
	* @return Array
	*/
	function &getResultPart(){
		return $this->resultPart;
	}

	/*
	* Returns the FROM clause of the query.
	*
	* @return String
	*/
	function getFromPart(){
		return $this->fromPart;
	}

	/*
	* Returns the FROM NAMED clause of the query.
	*
	* @return Array
	*/
	function getFromNamedPart(){
		return $this->fromNamedPart;
	}

	/**
	* Returns an unused Bnode label.
	*
	* @return String
	*/
	function getBlanknodeLabel(){
		return "_:bN".$this->bnodeCounter++;
	}


	/**
	* Sets the base part.
	*
	* @param String $base
	* @return void
	*/
	function setBase($base){
		$this->base = $base;
	}


	/**
	* Adds a prefix to the list of prefixes.
	*
	* @param  String $prefix
	* @param  String $label
	* @return void
	*/
	function addPrefix($prefix, $label){
		$this->prefixes[$prefix]= $label;
	}

	/**
	* Adds a variable to the list of result variables.
	*
	* @param  String $var
	* @return void
	*/
	function addVariable($var){
		$this->resultVars[]= $var;
	}


	/**
	* Sets the result form.
	*
	* @param  String $form
	* @return void
	*/
	function setResultForm($form){
		$this->resultForm = $form;
	}

	/**
	* Adds a graph pattern to the result part.
	*
	* @param  GraphPattern $pattern
	* @return void
	*/
	function addGraphPattern(&$pattern){
		$pattern->setId($this->graphPatternCounter);
		$this->resultPart[] = $pattern;
		$this->graphPatternCounter++;
	}

	/**
	* Adds a construct graph pattern to the query.
	*
	* @param  GraphPattern $pattern
	* @return void
	*/
	function addConstructGraphPattern(&$pattern){
		$this->constructPattern = $pattern;
	}


	/**
	* Adds a graphuri to the from part.
	*
	* @param  String $graphURI
	* @return void
	*/
	function addFrom($graphURI){
		$this->fromPart = $graphURI;
	}

	/**
	* Adds a graphuri to the from named part.
	*
	* @param  String $graphURI
	* @return void
	*/
	function addFromNamed($graphURI){
		$this->fromNamedPart[] = $graphURI;
	}

	/**
	* Sets a solution modifier.
	*
	* @param  String $name
	* @param  Value  $value
	* @return void
	*/
	function setSolutionModifier($name, $value){
		$this->solutionModifier[$name] = $value;
	}


	/**
	* Generates a new GraphPattern. If it is a CONSTRUCT graph pattern
	* $constr has to set to TRUE FALSE if not.
	*
	* @param  boolean $constr
	* @return GraphPattern
	*/
	function &getNewPattern($constr = false){
		$pattern = & new r2d2_GraphPattern();
		if($constr)
		$this->addConstructGraphPattern($pattern);
		else
		$this->addGraphPattern($pattern);
		return $pattern;
	}

	/**
	* Adds a new variable to the variable list.
	*
	* @param  String $var
	* @return void
	*/
	function addVar($var){
		$this->usedVars[$var]=true;
	}

	/**
	* Returns a list with all used variables.
	*
	* @return Array
	*/
	function getAllVars(){
		return array_keys($this->usedVars);
	}
	
	/**
	* Gets the solution modifiers of the query.
	* $solutionModifier['order by'] = value
	*                  ['limit']    = vlaue
	*                  ['offset']   = value
	*
	*
	* @return Array
	*/
	function getSolutionModifier(){
		return $this->solutionModifier;
	}


	/**
	* Returns the constcutGraphPattern of the query if there is one.
	*
	* @return GraphPattern
	*/
	function getConstructPattern(){
		return $this->constructPattern;
	}

}
// end class: Query.php

?>
