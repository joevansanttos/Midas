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
class LiteralObject{
	
	var $ID;
	
	var $DBSource;
	
	var $RDFDatatype; 
	
	var $language;  //d2rq:lang
	
	
	// ------------------------------------------------------------------------------------------------------------
	
	function LiteralObject($Literal, $Source, $datatype, $language){
		$this->ID = $Literal;
		$this->DBSource = $Source;
		$this->RDFDatatype = $datatype;
		$this->language = $language;
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

class URIObject{
	
	
	var $ID;

	var $DBSource;
	
		
	function URIObject($URI, $Source){
		$this->ID = $URI;
		$this->DBSource = $Source;
		
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

class bNodeObject{
	
	var $ID;
	
	var $DBSource;
	
		function bNodeObject($URI, $Source){
		$this->ID = $URI;
		$this->DBSource = $Source;
		
	}
	
}

?>
