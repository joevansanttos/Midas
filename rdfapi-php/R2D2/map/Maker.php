<?php
// ----------------------------------------------------------------------------------
// Class: LiteralObject
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
//require_once(R2D2_INCLUDE_DIR . 'map/NodeMaker.php');

class LiteralMaker {
	
	var $ID;
	
	var $DBSource;
	
	var $RDFDatatype; 
	
	var $language;  //d2rq:lang
	
	var $type = LITERAL;
	
	
	// ------------------------------------------------------------------------------------------------------------
	
	function LiteralMaker($Literal, $Source, $datatype, $language){
		$this->ID = $Literal;
		$this->DBSource = $Source;
		$this->RDFDatatype = $datatype;
		$this->language = $language;
	}
	
	function getID(){
		return $this->ID;
	}
	
	function matchConstraint($constraint){
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Literal $node
	 */
	function couldFit($node){
		if (($node === null) || ($node === ''))	
			return true;
			
		if (!is_a($node,'Literal'))
			return false;
			
		$label =$node->getLabel();
		
		return $this->DBSource->couldFit($label);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Literal $node
	 * @return array string
	 */
	function getColumnValues($node){
		return $this->DBSource->getColumnValues($node->getLabel());
	}
	
	/**
	 * Enter description here...
	 *@return array string
	 */
	function getColumns(){
		return $this->DBSource->getColumns();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array string $row
	 * @param array string $columnNameNumberMap
	 * @return resource
	 */
	function getNode($row, $columnNameNumberMap){
		$value = $this->DBSource->getValue($row,$columnNameNumberMap);
		if($value == null)
			return null;
		return new Literal($value,$this->language);
	}
	
	
	
}

// ----------------------------------------------------------------------------------
// Class: URIObject
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

class URIMaker {
	
	
	var $ID;

	var $DBSource;
	
	var $type = URI;
	
		
	
	function URIMaker($URI, $Source){
		$this->ID = $URI;
		$this->DBSource = $Source;
		
	}
	
	function getID(){
		return $this->ID;
	}
	
	function matchConstraint($constraint){
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Resource $node
	 */
	function couldFit($node){
		if (($node === null) || ($node === ''))	
			return true;
			
		if ( (is_a($node,'Resource')) && ($this->DBSource->couldFit($node->getURI() )))
			return true;
		else return false;
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Resource $node
	 * @return array string
	 */
	function getColumnValues($node){
		if ($node == null) return;
		return ($this->DBSource->getColumnValues($node->getURI()));
	}
	
	/**
	 * Enter description here...
	 *@return array string
	 */
	function getColumns(){
		return $this->DBSource->getColumns();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array string $row
	 * @param array string $columnNameNumberMap
	 * @return resource
	 */
	function getNode($row, $columnNameNumberMap){
		$value = $this->DBSource->getValue($row,$columnNameNumberMap);
		if($value == null)
			return null;
		return new Resource($value);
	}
}

// ----------------------------------------------------------------------------------
// Class: bNodeObject
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

class bNodeMaker {
	
	var $ID;
	
	var $DBSource;
	
	var $type = BNODE;
	
	function bNodeMaker($URI, $Source){
		$this->ID = $URI;
		$this->DBSource = $Source;
		
	}
	
	function getID(){
		return $this->ID;
	}

	function matchConstraint($constraint){
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Blanknode $node
	 */
	function couldFit($node){
		if (($node === null) || ($node === ''))	
			return true;
		
		$sourceFit = $this->DBSource->couldFit($node->getURI());
		return $sourceFit;
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Blanknode $node
	 * @return array string
	 */
	function getColumnValues($node){
		return ($this->DBSource->getColumnValues($node->getID()));
	}
	
	/**
	 * Enter description here...
	 *@return array string
	 */
	function getColumns(){
		return $this->DBSource->getColumns();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param array string $row
	 * @param array string $columnNameNumberMap
	 * @return resource
	 */
	function getNode($row, $columnNameNumberMap){
		$value = $this->DBSource->getValue($row,$columnNameNumberMap);
		if($value === null)
			return null;
		return new BlankNode($value);
	}	
	
	
}

// ----------------------------------------------------------------------------------
// Class: FixedNodeMaker
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

class FixedNodeMaker {
	
	/**
	 * Enter description here...
	 *
	 * @var Resource
	 */
	var $fixedNode;
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 */
	var $ID;
	
	var $type = FIXEDNODE;	
	
	function FixedNodeMaker($fixedNode){
		if ($fixedNode === null) return;
		$this->fixedNode = $fixedNode;
		$this->ID = $fixedNode->getLabel();
	}
	
	function isUriPattern(){
		return false;
	}
	
	
	function getID(){
		return $this->ID;
	}
	
	function matchConstraint($constraint){
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Resource $node
	 */
	function couldFit($node){
		if (($node === null) || ($node === '')) return true;

		//if ($this->fixedNode === $node)	
		if ($this->ID == $node->uri)		
			return true;
		else return false;	
		
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Blanknode $node
	 * @return array string
	 */
	function getColumnValues($node){
		return;
	}
	
	/**
	 * Enter description here...
	 *@return array string
	 */
	function getColumns(){
		return ;
	}
	
	/**
	 * Returns a RDF node from a given database ResultSet row and 
	 * the mapped columnnumber which is set through the mapper
	 *
	 * @param array string $row
	 * @param array string $columnNameNumberMap
	 * @return resource
	 */
	function getNode($row, $columnNameNumberMap){
		return $this->fixedNode;
	}
	
}
?>
