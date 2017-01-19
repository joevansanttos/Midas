<?php
// ----------------------------------------------------------------------------------
// Class: PropertyBridge
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
class PropertyBridge{
	
	/**
	 * ID of a Propterty Bridge with the name of the Bridge
	 *
	 * @var Resource
	 * @access private
	 */
	var $ID;
	
	/**
	var $Subject;
	
	var $Predicate;
	
	var $Object;
	
	/**
	 * contains the database to which the property bridge belongs to
	 *
	 * @var database $Database
	 * @access private
	 */
	var $Database;
	
	/**
	 * some defined aliases for the bridge
	 *
	 * @var string array
	 * @access private
	 */
	var $aliasNames = array();
	
	/**
	 * the defined joins for this bridge
	 *
	 * @var string array
	 * @access private
	 */
	var $Joins = array();
	
	/**
	 * all defined conditions
	 *
	 * @var string array()
	 * @access public
	 */
	var $Conditions = array();
	
	/**
	 * true if property bridge may contain duplicates,
	 * false if property bridge have no duplicates
	 *
	 * @var boolean $duplicates
	 */
	var $duplicates = false;
	
	/**
	 * @var UriMatchPolicy
	 * @access private
	 */
	
	/**
	 * Enter description here...
	 *
	 * @var URIMatchPolicy
	 */
	var $uriMatchPolicy ;
	
	// ------------------------------------------------------------------------------------------------------------
	
	
	/**
	 * Construktor of class PropertyBridge
	 *
	 * @param Resource $PropertyNode
	 * @param NodeMaker $Subject
	 * @param NodeMaker $Predicate
	 * @param NodeMaker $Object
	 * @param database $Database
	 * @param array $joins
	 * @param array $aliases
	 * @param array string $conditions
	 * @return PropertyBridge
	 */
	function PropertyBridge($PropertyNode, $ClassMap, $Predicate, $Object, $Database, $joins, $aliases, $conditions){
		$this->ID = $PropertyNode;
		$this->Subject = $ClassMap;
		$this->Predicate = $Predicate;
		$this->Object = $Object;
		$this->Database = $Database;
		if($joins != null) $this->Joins = $joins;
		$this->aliasNames = $aliases;
		$this->uriMatchPolicy = new URIMatchPolicy();
		if ($conditions != null) $this->Conditions = $conditions;
		else $this->Conditions = array();
	}
	
	/**
	 * adds a SQL WHERE condition for generating the SQL query
	 *
	 * @param string array() $conditions
	 */
	function addWhereCond($conditions){
		if($conditions != null){
			for(reset($conditions); $current=current($conditions);next($conditions)):
				array_push($this->Conditions, $current);
			endfor;
				
		}
	}
	
	function getAliasNames(){
		return $this->aliasNames;
	}
	
	function getConditions(){
		return $this->Conditions;
	}
	
	function getJoins(){ 
		return $this->Joins;	
	}
	
	function getDatabase(){
		return $this->Database;
	}
	
	function getSubject(){
		return $this->Subject;
	}
	
	function getID(){
		return $this->ID;
	}
	
	function getObject(){
		return $this->Object;
	}
	
	
	function getPredicate(){
		return $this->Predicate;
	}
	
	
	function getContainDuplicates(){
		return $this->duplicates;
	}
	
	
	function setContainDuplicates($status){
		if (($status == true) || ($status == false))
			return $this->duplicates = $status;
		else
			logging::error("wrong parameter to function \'setContainDuplicates\' 
			in class \'PropertyBridge\'. use: true or false");
	}
	
	/**
	 * set the mode for searching a correct PropertyBridge from a given SQL expression
	 *
	 * @param URIMatchPolicy $mode
	 */
	function setCorrectURI($mode){
		$this->uriMatchPolicy = $mode;
	}
	
	
	
	/**
	 * checks a given triple if a answer could be given without having quering the database
	 *
	 * @param Resource $s
	 * @param Resource $p
	 * @param Resource $o
	 * @param QueryContext $context
	 * @return boolean
	 */
	function searchCorrectBridge($s,$p,$o,$context){
		if( (!$this->Subject->couldFit($s)) ||
		    (!$this->Predicate->couldFit($p)) ||
		    (!$this->Object->couldFit($o)))
		    return  false;
		
		if (($s === null) || ($s === '')){
			if(!$this->uriMatchPolicy->couldFitSubjectInContext($context))
				return false;
			$this->uriMatchPolicy->updateContextAfterSubjectMatch($context);
		}
		
		if (($o === null) || ($o === '')){
			if(!$this->uriMatchPolicy->couldFitObjectInContext($context))
				return false;
			$this->uriMatchPolicy->updateContextAfterObjectMatch($context);
		}
		
		
		return true;
	}
	
	
}
?>