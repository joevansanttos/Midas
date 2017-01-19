<?php

// ----------------------------------------------------------------------------------
// Class: Alias
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

class Alias{
	
	/**
	 * Enter description here...
	 *
	 * @var string
	 * @access private
	 */
	var $table;
	
	var $aliases;
	
	var $SQLexpression;
	
	// -------------------------------------------------------------------------------------------
	
	/**
	 * Construktor
	 *
	 * @param string $databaseTable
	 * @param string $aliasTable
	 * @return Alias
	 * @access public
	 */
	function Alias($databaseTable, $aliasTable){
		$this->table = $databaseTable;
		$this->aliases = $aliasTable;
		$this->SQLexpression = Alias::setSQLExpression($databaseTable,$aliasTable);
	}
	
	/**
	 * builds an Alias from a d2rq:alias expression in SQL Syntax.
	 * Using alias syntax: Table AS Alias
	 * The whole String is converted into uppercase
	 *
	 * @param string $alias
	 * @return Alias
	 */
	
	function buildAlias($alias){
		
	//	$alias = strtoupper($alias);
		
		$pos = strpos($alias, 'AS');
		if($pos === false){  
			logging::error("d2rq:alias: \'".$alias."\' wrong syntax. use: \'Table AS Alias\'");
		}
			
		$table = substr($alias,0,$pos);  $table = trim($table);
		$alias = substr($alias,$pos+2);   $alias = trim($alias);
		

		return new Alias($table,$alias);
	}
	
	/**
	 * Enter description here...
	 *
	 * @param string array $aliases
	 * @return string array
	 * @access public
	 */
	function buildAliases($aliases){
		
		$result = array();
		
		
		for(reset($aliases); $alias=key($aliases);next($aliases)):
			$alias = Alias::buildAlias($alias);
			
			$aliasTable = $alias->getAliasTable();

			$result[$aliasTable] = $alias; 		
			
		endfor;	
		
		
		return $result;
		
	}
	
	
	/**
	 * generates SQL Code for an Alias Map
	 *
	 * @param string $databaseTable
	 * @param string $aliasTable
	 * @return string $sql
	 * @access public
	 */
	function SetSQLExpression($databaseTable, $aliasTable){
		return $databaseTable . ' AS ' . $aliasTable;
	}
	
	function getAliasTable(){
		return $this->aliases;
	}
	
	function getDatabaseTable(){
		return $this->table;
	}
	
	function getSQLExpression(){
		return $this->SQLexpression;
	}
	
	
}
?>