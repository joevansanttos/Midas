<?php
// ---------------------------------------------
// Class: QueryTriple
// ---------------------------------------------

/**
* Represents a query triple.
*
* <BR><BR>History:<UL>
* <LI>08.09.2005: Initial version</LI>
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 0.9.3
*
* @package sparql
*/
Class r2d2_QueryTriple extends Object{

	/**
	* The QueryTriples Subject.
	*/
	var $subject;

	/**
	* The QueryTriples Predicate.
	*/
	var $predicate;

	/**
	* The QueryTriples Object.
	*/
	var $object;


	/**
	* Constructor
	*/
	function r2d2_QueryTriple($sub,$pred,$ob){
		$this->subject   = $sub;
		$this->predicate = $pred;
		$this->object    = $ob;
	}

	/**
	* Returns the Triples Subject.
	*
	* @return Node
	*/
	function getSubject(){
		return $this->subject;
	}

	/**
	* Returns the Triples Predicate.
	*
	* @return Node
	*/
	function getPredicate(){
		return $this->predicate;
	}

	/**
	* Returns the Triples Object.
	*
	* @return Node
	*/
	function getObject(){
		return $this->object;
	}

}

// end class: QueryTriple.php
?>
