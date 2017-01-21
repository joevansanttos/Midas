<?php
// ----------------------------------------------------------------------------------
// Class: tools.php
// ----------------------------------------------------------------------------------


/**
 * 
 * Some tool functions
 * 
 * <BR><BR>History:<UL>
 * <LI>01-06-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package find
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */


class tools{
	
/**
 * return true if $str ends with $sub
 *
 * @param string $str
 * @param string $sub
 * @return boolean
 */

	function endsWith( $str, $sub ) {
		$test1 = substr( $str, strlen( $str )) - strlen($sub);		
		
   		return ( substr( $str, strlen( $str ) - strlen( $sub ) ) == $sub );
	}
	
	
	/**
	 * checks if a node is literal, resource or blanknode 
	 *
	 * @param unknown_type $node
	 * @return unknown
	 */
	function checkNodeType($node){
	
		if (is_a($node,'Resource') || 
		    is_a($node,'Literal') ||
		    is_a($node,'BlankNode')) return $node->getLabel(); 
		if ($node==null) return "empty";
		
	}
	
	/**
	 * checks if a node in a sparql-query is literal, resource or blanknode 
	 *
	 * @param TripleNode $node
	 * @return string
	 */
	function checkSparqlNodeType($TripleNode){
		
		if(is_a($TripleNode,"r2d2_TripleNode"))
			$node = $TripleNode->getTripleNode();
		else $node = $TripleNode;
	
		if (is_a($node,'Resource') || 
		    is_a($node,'Literal') ||
		    is_a($node,'BlankNode')) return $TripleNode; 
		if ($node==null) return null;
		
		if ($node[0] === '?')
			return null;
		
	}
	
	
	 	/**
 	 * takes the whole url from a database and searches for the host adress without the 
 	 * database name and jdbc/odbc part
 	 *
 	 * @param string $url
 	 * @return string
 	 * @access private
 	 */
 	function cutDbNameFromURL($url){
 		$pos1 = strpos($url,"//");
 		$pos2 = strrpos($url,"/");
 		
 		if($pos1 && $pos2)		
 			$url = substr($url,$pos1+2,$pos2-$pos1-2);
 			
 		return $url;
 	}
 	
 	 	/**
 	 * Takes the whole url of a database und searches for the database name
 	 *
 	 * @param string $url
 	 * @return string
 	 * @access private
 	 */
 	function searchDBName($url){
 		//jdbc:mysql://127.0.0.1:3306/dbname
 		$pos = strrpos($url,"/");
 		if($pos);
 		$dbName = substr($url,$pos+1);
 		return $dbName;
 	}
 	
 	 	/**
 	 * checks the name of the database driver (jdbc or odbc) for type and returns the type to connect
 	 * to the database
 	 *
 	 * @param string $driver
 	 * @return string
 	 * @access private
 	 */
 	function prepareDriver($driver){
 	
		if (false != stristr ($driver, 'MsAccess')){
   			$driver = "MsAccess"; return $driver; }
   		else if (false != stristr ($driver, 'MySQL')){
   			$driver = "MySQL"; return $driver;}
   		else if (false != stristr ($driver, 'MSSQL')){
   			$driver = "MSSQL"; return $driver;}
   		else {
   			echo "error: Database driver ".$driver." not suppored. Only MSAccess, MySQL and MSSQL ars supported!";
   			exit;
   		}
 	}
	
}
	
?>