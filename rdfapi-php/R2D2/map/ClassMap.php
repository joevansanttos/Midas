<?php

// ----------------------------------------------------------------------------------
// Class: ClassMap
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

class ClassMap{
	

	/**
	 * An identifier of a class map
	 * @var Resource $ID
	 * @access private
	 */
   var $ID;
   
   /**
    * Enter description here...
    *
    * @var array $propertyBridges
    * @access private
    */
   var $propertyBridges;
   
   /**
    * Enter description here...
    *
    * @var Resource
    * @access private
    */
   var $uriPattern;
   var $uriColumn;
   
   /**
    * Enter description here...
    *
    * @var array
    * @access private
    */
   var $bNodeIDColumns = array();
   
   /**
    * Enter description here...
    *
    * @var Resource
    * @access private
    */
   var $database;
   
   // ------------------------------------------------------------------------------------------------------------
   
   
   
   /**
    * Constructor of the class ClassMap
    * holds all information about a ClassMap
    *
    * @param Resource $ID
    * @param Resource $pattern
    * @param Resource $column
    * @param array $bNodeIDColumns
    * @param Resource $database
    * @access public
    */
   function ClassMap($ID, $pattern, $column, $bNodeIDColumns,$database){	
   	
   	$this->ID             = $ID;
   	$this->uriPattern     = $pattern;
   	$this->uriColumn      = $column;
   	$this->bNodeIDColumns = $bNodeIDColumns;
   	$this->database       = $database;
   	
   	$this->propertyBridges = array();
   }
	
   
   function getID(){
   		return $this->ID;
   }
   
   function getUriPattern(){
   		return $this->uriPattern;
   }
   
   function getUriColumn(){
   		return $this->uriColumn;
   }
   
   function getDatabase(){
   	return $this->database;
   }
   
   function getBNodeIDColumns(){
   	return $this->bNodeIDColumns;
   }
   
   function addPropertyBridge($bridge){
   	 array_push($this->propertyBridges, $bridge);
   }
   
   function getPropertyBridges(){
   		return $this->propertyBridges;
   }
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
?>