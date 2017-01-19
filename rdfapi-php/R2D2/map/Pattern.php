<?php

// ----------------------------------------------------------------------------------
// Class: Pattern
// ----------------------------------------------------------------------------------


/**
 * 
 *  * String patterns are used to use one or more database columns take generate URI´s from a primary key.
 * a pattern usually is defined as  URI@@table.column@@
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

class Pattern {
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access public
	 */
	var $Pattern;
	
	//var $Columns = array(3);
	var $Columns = array();
	
	var $FirstPart;
	var $PatternParts = array();
	var $LiteralParts = array();
	//var $PatternParts = array(3);
	//var $LiteralParts = array(3);
	
	var $type = DBSOURCE_PATTERN;
	
	
	// ------------------------------------------------------------------------------------------------------------
	
	
	
	/**
	 * Constructor of Class Pattern.
	 *
	 * @param string $pattern
	 * @access public
	 */
	
	function Pattern($pattern){
		$this->Pattern = $pattern;
		Pattern::analyse();
		//$this->ColumnSet = new 
		
		
	}
	
	/**
	 * takes a pattern string an split it into several parts like table column, literal parts
	 * @access public
	 * 
	 */
	
	function analyse(){

		$startpos = strpos($this->Pattern, R2D2Delimiter);
		if($startpos === false){  // no delimiter found -> no column
			//$startpos = strlen($this->Pattern);
			Logging::error("Illegal pattern - no column defined in d2rq:uriPattern: '".$this->Pattern."'\n<p>
			Use: d2rq:uriPattern \"pattern@@table.column@@\"");
		}
		
		// get first literal part
		$this->FirstPart = substr($this->Pattern,0,$startpos);
		
		while($startpos < strlen($this->Pattern)){
			$startpos = $startpos + strlen(R2D2Delimiter);
			
			$endpos = strpos($this->Pattern,R2D2Delimiter, $startpos);
			if($endpos === false)
				Logging::error("Illegal pattern: '".$this->Pattern."'");
			
			// take columnname between two delimiter
			$columnlen = $endpos - $startpos;
			$columnname = substr($this->Pattern,$startpos,$columnlen); 
			$columnname = trim($columnname);
			
			// add a new column
			array_push($this->Columns, new column($columnname));
			
			// find end of text
			$startpos = $startpos + $columnlen + strlen(R2D2Delimiter); //set endpos behind delimiter
			
		    $endpos = strpos($this->Pattern, R2D2Delimiter, $startpos);
		    if ($endpos === false){ // no delimiter --> set endpos behind last delimiter, set startpos at end of string
		    	$endpos = $startpos;
		    	$startpos = strlen($this->Pattern);
		    }
			$literalpart = substr($this->Pattern, $startpos, $endpos-$startpos);
			if($literalpart !== false){
				array_push($this->LiteralParts, $literalpart);
				$startpos = $endpos;
			}
			
		}
		
		
		
		
	}
	
    
    /**
     * Enter description here...
     *
     * @param string $value
     * @return array string
     * @access public
     */

    function getColumnValues($value){
    	
    	$resultarray = array();
    	
    	if (($value === null) || (0 === count($this->Columns)))
    		return $resultarray;
    		
    	$i = 0;
    	$startpos = strlen($this->FirstPart);
    	while ( $i < (count($this->Columns)-1)){
    		if (!$this->LiteralParts) $literal = null;
    		else                      $literal = $this->LiteralParts[$i];
    		$endpos = strpos($value, $literal, $fieldpos);
    		if ($endpos === false){
    			return array();
    		}
    		$resultarray[substr($value,$startpos,$endpos)] = $this->Columns[$i];
    		$startpos = $endpos + strlen($literal);
    		$i++;
    	}
    	if(!$this->LiteralParts) $lastLiteral = null;
    	else                   	 $lastLiteral = $this->LiteralParts[$i];
    	
    	$endsWith = false;
    	
    	if( false == tools::endsWith($value, $lastLiteral))
    		return array();
    	
    		
    	// store array with key: searchstring, value: column
    	// substr takes the value from startpos(length of firstLiteral) to begin of lastLiteral
    	$resultarray[substr($value,$startpos,(strlen($value)-strlen($lastLiteral)))] = $this->Columns[$i];

    	return $resultarray;
    		
  
    }
    
				
				
	/**
	 * Enter description here...
	 *
	 * @param string $value
	 * @return boolean
	 * @access public
	 */
	function couldFit($value){
		if($value === null)
		 	return false;
		 
		 if(count($this->Columns) === 0){
		 	if(0 === strcmp($value,$this->FirstPart))
		 		return true;
		 	else return false;
		 }
		 
		
		 $len = strlen($this->FirstPart);
		 if(0 !== strncmp($this->FirstPart,$value,$len))
		 	return false;
		 
		 $i=0;
		 while($i< (count($this->Columns)-1) ){
		 	if (!$this->LiteralParts)   $literal = null;
		 	else             		 	$literal = $this->LiteralParts[$i];
		 	$len = strpos($value,$this->LiteralParts,$len);
		 	if(!$len)
		 		return false;
		 	
		 	$len+=strlen($literal);
		 	$i++;
		 }
		 if($this->LiteralParts){		 
		 	if (tools::endsWith($value,$this->LiteralParts[$i]))
		 		return true;
		 	else return false;
		 }
		 else return true;
		 
		 	
	}
	
	/**
	 * returns the array of all defined columns for current class Map
	 *
	 * @return string array $columns
	 * @access public
	 */
	function getColumns(){
		return $this->Columns;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string $anonID
	 * @return string
	 * @access public
	 */

	function getValue($row,$columnNameNumberMap){
		$i = 0;
		$result = $this->FirstPart;
		while($i < count($this->Columns)){
			$column = $this->Columns[$i];
			$index = $columnNameNumberMap[$column->getQualifiedName()];
			$index-=1; // Index begins with 0; columnNameNumberMap begins with 1 --> MapNumber -1
			if ( $index === null)
				logging::error("Illegal pattern: \'".$this->Pattern,"\'");
			if($row[$index] === null)
				return null;
			
			$result.=$row[$index];
			if( ($this->LiteralParts && array_key_exists($i,$this->LiteralParts))){
					if($this->LiteralParts[$i] !== null)
				 		$result.=$this->LiteralParts[$i];
			}
				
			$i++;
		}
		return $result;
	}
				

}
?>