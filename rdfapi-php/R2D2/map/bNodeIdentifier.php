<?php
// ----------------------------------------------------------------------------------
// Class: bNodeIdentifier
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
class bNodeIdentifier  {
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access public
	 */
	var $classMap;
	
	/**
	 * all columns defined for this class map through a blank node identifier
	 *
	 * @var string array
	 * @access public
	 */
	var $Columns = array();
	
	var $type = DBSOURCE_BNODEID;
	
	// ------------------------------------------------------------------------------------------------------------
	
	
	/**
	 * Constructor of an Blank Node Identifier
	 *
	 * @param string $bNode
	 * @param Resource $classMap
	 * @access public
	 */
	function bNodeIdentifier($bNode, $classMap){
		
		$this->classMap = $classMap;
		
		
		$token = strtok($bNode,",");
		while ($token !== false){
			array_push($this->Columns, new Column($token));
			$token = strtok(",");
		}
		
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param string $anonID
	 * @return boolean
	 * @access public
	 */
	function couldFit($anonID){
		$pos = false;
		$pos = strpos($anonID,R2D2Delimiter);
		if($pos === FALSE)
			return false;
		
		$bnode = substr($anonID,0,$pos);
		if (0 === strcmp($this->classMap,$bnode))
		return true;
		else return false;
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
	 * @return array string
	 * @access public
	 */
	function getColumnValues($anonID){
		$token = strtok($anonID,R2D2Delimiter);
		
		$result = array(3);
		for(reset($this->Columns); $column=current($this->Columns);next($this->Columns)):
			$value = strtok(R2D2Delimiter);
			$result[$column] = $value;
		endfor;	
		
		return $result;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array string $row
	 * @param array string $columnNameNumberMap
	 * @return string
	 * @access public
	 */
	function getValue($row,$columnNameNumberMap){
		$result = $this->classMap->getLabel();
		
		for(reset($this->Columns); $column=current($this->Columns);next($this->Columns)):
			$index = (int)$columnNameNumberMap[$column->getQualifiedName()];	
			$index-=1; // Index begins with 0; columnNameNumberMap begins with 1 --> MapNumber -1
			if($row[$index] === null) return null;
			
			$result.= R2D2Delimiter;
			$result.= $row[$index];
		
		endfor;	
		
		return (string)$result;
		
	}
}

?>