<?php
// ----------------------------------------------------------------------------------
// class: queryContext
// ----------------------------------------------------------------------------------

/**
 * set the context of a sql query for executing a find query.
 *
 * History:
 * 07-06-2006                : 
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 * @version V0.1
 * @see de....R2D2Model
 * 
 * @package find
 * @access public
 */

class QueryContext{
	
	var $uriPatternMatched = false;
	
	/**
	 * Enter description here...
	 *
	 * @param boolean $matched
	 */
	function setURIPatternMatched($matched){
		$this->uriPatternMatched = $matched;	
	}
	
	function isURIPatternMatched(){
		return $this->uriPatternMatched;
	}
	
}

?>