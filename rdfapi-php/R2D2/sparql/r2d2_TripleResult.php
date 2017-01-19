<?php
// ----------------------------------------------------------------------------------
// Class: SQLResultSet
// ----------------------------------------------------------------------------------


/**
 * 
 * This class offers methods to access the result set of a SQL query
 * 
 * <BR><BR>History:<UL>
 * <LI>01-06-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package sparql
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);

class r2d2_TripleResult{
	
	/**
	 * map: Columnname <-> internal number
	 *
	 * @var array string
	 * @access protected
	 */
	var $columnNameNumberMap = array();
	
	
	/**
	 * a row
	 *
	 * @var array string
	 * @access protected
	 */
	var $currentRow = array();
	
	/**
	 * contains the database
	 *
	 * @var Database
	 * @access protected
	 */
	var $database;
	
	/**
	 * contains information about a result triple
	 *
	 * @var array
	 */
	var $tripleMaker = array(array()); // [triplePattern][tripleMaker]
	
	/**
	 * a SQL result set
	 *
	 * @var    array()
	 * @access private
	 */
	var $resultSet=null;
	
	var $SQL;
	
	var $numCol = 0;
	
	/**
	 * a list with all result of an SQL resultSet
	 *
	 * @var array string
	 */
	var $resultList=null;
	
	
	
	/**
	 * constructor of class SQLResultSet
	 *
	 * @param Database $database
	 * @return SQLResultSet
	 * @access protected
	 */
	function r2d2_TripleResult($database){
		$this->database = $database;
	}
	

	function setSQL($sql){
		
		$this->SQL = $sql;
	}
	
	function setColumnMap($map){
		//$this->columnNameNumberMap[$number] = $ColumnName;
		$this->columnNameNumberMap = $map;
	}
	
	function getColumnMap(){
		return $this->columnNameNumberMap;
	}
	
	function addTripleMaker($tripleMakers){
		/*if(!$this->tripleMaker[$iTriplePattern])
			 $this->tripleMaker[$iTriplePattern][0] = $tripleMaker;
		else	array_push($this->tripleMaker[$iTriplePattern],$tripleMaker);*/
		
		$this->tripleMaker = $tripleMakers;
		
	}
	
	
	/**
	 * execution of an SQL query on database.
	 * The funtion checks, if query has been executed yet and connects to database if a generated query don´t exist.
	 * After all the resultSet is stored.
	 * @access private
	 *
	 */
	function execSQLQuery($sql){
		
		// check if SQL query has been executed yet
		/*if(array_key_exists($this->SQL,$this->SQLlist))	
			// SQL has been executed -> take saves results/ dont query the database
			$this->resultSet = $this->SQLlist[$this->SQL];
		else{*/
		
			// connect to DB
			$con = $this->database->getConnection();
	    
			$this->resultSet = &$con->execute($sql);
		    if (!$this->resultSet)
      				echo $con->errorMsg();
   		
			// get MetaData
			$this->numCol = $this->resultSet->FieldCount();
			$numRows = $this->resultSet->RecordCount();
			//echo "Number of rows in ResultSet: ".$numRows."<br>";
			
			// save result set in the SQL queries list
			//$this->SQLlist[$this->SQL] = $this->resultSet;
			
	//	}
	}

	function BuildTriples($triplePatterns){
		$cur_retTriples = array();
		$returnList = array(array());
		$row = 0;
		do{  // take every row
			$currentRow = $this->nextRow();
			if($currentRow!=null){

				$tripleCounter = 0;
				foreach($this->tripleMaker as $tripleMaker){ // take every query from stack
					$columnNameNumberMap = $this->columnNameNumberMap[$tripleCounter];

					foreach( $tripleMaker as $cur_maker){
						$triple = $cur_maker->makeTriple($currentRow, array_flip($columnNameNumberMap));
						if ( $triple != null){
							// take spo name from SPARQL
							$SPOnames = $cur_maker->getTriplePattern();
							$subject = $SPOnames->getSubject();
							$pred = $SPOnames->getPredicate();
							$object = $SPOnames->getObject();
							
							$result = array();							
							if (!tools::checkSparqlNodeType($subject))
								$result[$subject] = $triple->getSubject();
							if (!tools::checkSparqlNodeType($pred))
								$result[$pred] = $triple->getPredicate();
							if (!tools::checkSparqlNodeType($object))
								$result[$object] = $triple->getObject();
							
							$returnList[$tripleCounter][$row]= $result;													
						}
					}
					$tripleCounter++;
				}

			}
			$row++;
		}while ($currentRow != null);
		return $returnList;
	}
	
	/**
	 * close current resultSet
	 * @access public
	 *
	 */
	function close(){
		if ( $this->resultSet == null)
			return;
			
		$this->resultSet->close();
		$this->resultSet = null;
		$this->queryHasBeenExecuted = false;
	}
	
	/**
	 * get the current Row from result set as an array to generate a triple
	 *
	 * @return array string
	 * @access protected
	 */
	function nextRow(){

		if($this->resultSet->EOF)
			return null;	

		$result = array();
		$i=0;
		for ( $i;$i<$this->numCol;$i++){
				$result[$i] = $this->resultSet->fields[$i];
		}
		
		
		// move internal cursor no next row
		$this->resultSet->moveNext();
		
		return $result;
	}
	
}