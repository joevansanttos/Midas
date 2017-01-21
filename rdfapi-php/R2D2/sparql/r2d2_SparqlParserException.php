<?
// ---------------------------------------------
// class: SparqlParserExecption
// ---------------------------------------------
/**
* A SPARQL Parser Execption for better errorhandling.
*
* <BR><BR>History:<UL>
* <LI>08.09.2005: Initial version</LI>
*
* @author   Tobias Gauss <tobias.gauss@web.de>
* @version	 0.9.3
*
* @package sparql
*/
Class r2d2_SparqlParserException{

	var $tokenPointer;

	function __construct($message, $code = 0, $pointer){

		$this->tokenPointer = $pointer;
		$this->__construct($message, $code);
		return $message." on ".$pointer;
	}

	/**
	* Returns a pointer to the token which caused the exception.
	* @return int
	*/
	function getPointer(){
		return $this->tokenPointer;
	}

}
?>