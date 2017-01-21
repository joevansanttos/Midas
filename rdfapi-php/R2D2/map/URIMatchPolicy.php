<?php
// ----------------------------------------------------------------------------------
// Class: URIMatchPolicy.php
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




class URIMatchPolicy{
	var $isSubjectBasedOnURIPattern = false;
	var $isSubjectBasedOnURIColumn = false;
	var $isObjectBasedOnURIPattern = false;
	var $isObjectBasedOnURIColumn = false;

	/**
	 * Enter description here...
	 *
	 * @param boolean $isObjectBasedOnURIColumn
	 */
	function setObjectBasedOnURIColumn($isObjectBasedOnURIColumn) {
		$this->isObjectBasedOnURIColumn = $isObjectBasedOnURIColumn;
	}

	function setObjectBasedOnURIPattern($isObjectBasedOnURIPattern) {
		$this->isObjectBasedOnURIPattern = $isObjectBasedOnURIPattern;
	}

	function setSubjectBasedOnURIColumn($isSubjectBasedOnURIColumn) {
		$this->isSubjectBasedOnURIColumn = $isSubjectBasedOnURIColumn;
	}

	function setSubjectBasedOnURIPattern($isSubjectBasedOnURIPattern) {
		$this->isSubjectBasedOnURIPattern = $isSubjectBasedOnURIPattern;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param QueryContext $context
	 * @return boolean
	 */
	function couldFitSubjectInContext($context) {
		return ((!$this->isSubjectBasedOnURIColumn) || (!$context->isURIPatternMatched()));
	}
	
	/**
	 * Enter description here...
	 *
	 * @param QueryContext $context
	 */
	function updateContextAfterSubjectMatch($context) {
		if ($this->isSubjectBasedOnURIPattern) {
			$context->setURIPatternMatched(true);
		}
	}
	
	/**
	 * Enter description here...
	 *
	 * @param QueryContext $context
	 * @return boolean
	 */
	function couldFitObjectInContext($context) {
		return !$this->isObjectBasedOnURIColumn || !$context->isURIPatternMatched();
	}
	
	/**
	 * Enter description here...
	 *
	 * @param QueryContext $context
	 */
	function updateContextAfterObjectMatch($context) {
		if ($this->isObjectBasedOnURIPattern) {
			$context->setURIPatternMatched(true);
		}
	}

	/**
	 * Enter description here...
	 *
	 * @return int
	 */
	function getEvaluationPriority() {
		$result = 0;
		if ($this->isObjectBasedOnURIColumn) {
			$result -= 1;
		}
		if ($this->isSubjectBasedOnURIColumn) {
			$result -= 1;
		}
		if ($this->isObjectBasedOnURIPattern) {
			$result += 2;
		}
		if ($this->isSubjectBasedOnURIPattern) {
			$result += 2;
		}
		return $result;
	}
}
?>