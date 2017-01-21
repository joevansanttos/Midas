<?php

// ----------------------------------------------------------------------------------
// Class: TranslationTable
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

class TranslationTable{
	
	
	/**
	 *
	 *
	 * @var array string
	 * @access private
	 */
	var $RDF2DB = array();
	var $DB2RDF = array();
	
	var $DBSource;
	
	//var $Translator;
	
	
	
	/**
	 * Constructor 
	 *
	 * @param string $fullName
	 * @return column
	 * @access public
	 */
	function TranslationTable(){
		
	}
	
	/**
	 * counts the number of mappings
	 *
	 * @return int  number of mappings
	 * @access public
	 */
	
	function size(){
		return count($this->DB2RDF);
	}
	
	/**
	 * adds a new translation mapping
	 *
	 * @param string $dbValue  - value on db side (from db column)
	 * @param string $rdfValue - value on rdf side (URI or string)
	 * @access public
	 */
	function addTranslation($dbValue, $rdfValue){
		$this->DB2RDF[$dbValue] = $rdfValue;
		$this->RDF2DB[$rdfValue] = $dbValue;
	}
	
	function addTranslatorClass($className, $resource){
		
	}
	
	function setTranslator($translator){
	
	}
	
	function toDBValue ( $rdfValue ){
		return $this->RDF2DB[$rdfValue];
	}
	
	function toRDFValue ( $dbValue){
		return $this->DB2RDF[$dbValue];
	}
	
	function addAll($translationMap){
		$value = null;
		$i=0; $len = count($translationMap);
		while ($value = current($translationMap)) {
			$key = key($translationMap);
			$value = $translationMap($key);
			TranslationTable::addTranslation($key,$value);
			
			next($array);
		}
   		
       
   }
   
   function setTranslatorClass($className, $resource){
   	
   }
   	
	function TranslatingValueSource($DBSource,$translator){
		$this->DBSource = $DBSource;
		$this->Translator = $translator;
		
	}
	
	function couldFit($value){
		$dbvalue = $this->toDBValue($value);
		
		if ( ($dbvalue != null) && ($this->couldFit($dbvalue) == true) )
			return true;
		else return false;
 	}
	
	function getColumns(){
		return $this->DBSource->getColumns();
	}
	
	function getColumnValues($value){
		$value = $this->toDBValue($value);
		return $this->DBSource->getColumnValues($value);
	}
	

	
	
	
}
	
?>