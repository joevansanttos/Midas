<?php



class r2d2_SQLGenerator{
	
	var $SELECT = array();
	
	var $FROM = array();
	
	var $WHERE = array();
	
	var $isDistinct = false;
	
	var $OrderBy = false;
	
	var $Constraint = false;
	
	var $OPTIONAL = false;
	
	var $UNION = false;
	
	var $tableMap = array(array());
	
	var $tripleQuery = array();
	
	var $parser;
	
	
	function r2d2_SQLGenerator(){
		
	}
	
	function addSELECT($term){
		array_push($this->SELECT,$term);
	}
	
	function addFROM($term){
		array_push($this->FROM,$term);
	}
	
	function addWHERE($term){
		array_push($this->WHERE,$term);
	}
	
	function setDistinct($distinct = TRUE){
		$this->isDistinct = $distinct;
	}
	
	function setUnion($union){
		$this->UNION = $union;
	}
	
	function getUnion(){
		return $this->UNION;
	}
	
	function setOptional($optional){
		$this->OPTIONAL = $optional;
	}
	
	function getOptional(){
		return $this->OPTIONAL;
	}
	
	function setContraint($constraint){
		$this->Constraint = $constraint;

		foreach($constraint as $cur_constraint){
			$expression = $cur_constraint->getExpression();

			$this->parser = new r2d2_SparqlParser();
			$expression = $this->parser->uncomment($expression);
			$this->Constraint = $expression;
			$this->parser->tokenize($expression);

			$where = '';
			do{
				switch(strtolower(current($this->parser->tokens))){
					case "regex":
						$this->parseRegex();
						break;					
					case "<":
						$this->parseComparing("<");												
						break;
					case ">":
						$this->parseComparing(">");												
						break;
					case "=":
						$this->parseComparing("=");		
						break;
				}
			}while(next($this->parser->tokens));
		}
		

		
			
	}
	
	function getConstraint(){
		return $this->Constraint;
	}
	
	function addTableMap($patternnr,$table,$alias){
		$this->tableMap[$patternnr][$alias] = $table;
	}
	
	function getTableMap($table, $patternnr,$aliasnr = null){
		if ($aliasnr == null)
			return array_search($table,$this->tableMap[$patternnr]);
		else {
			
			$i=0;
			foreach ($this->tableMap[$patternnr] as $alias => $table) {
				if($i==$aliasnr)
					return $alias;
				$i++;	
			}
		}
	}
	
	function addTripleQuery($tripleQuery){
		array_push($this->tripleQuery,$tripleQuery);
	}
	
	function getSQLFROM(){
		return $this->FROM;
	}
	
	
	function buildSQL($SQLQueries){
		
		// checking combination of UNION / FILTER / Contraint
		
		$sql='';
		
		foreach($SQLQueries as $id => $query){	
									
				$sql .= " SELECT ";

				if ($query->isDistinct)
				$sql .= "DISTINCT ";


				if (0 == count($query->SELECT))
				$sql .= "1";
				else
				$sql .= join(", ",$query->SELECT);

				$sql .= " \nFROM ";

				// adding left-joins

				// adding referred tables
				$sql .= join(", ",$query->FROM);

				if($query->WHERE){
					$sql .= " \nWHERE ";

					// adding triple pattern clauses and filters
					$sql .= join(" \nAND ",$query->WHERE);
				}
				// get equi-joins
			
			
			// get order by code
			//if ($query->OrderBy)
			//$sql .= $query->OrderBy;

			// get limit / offset code


			//$sql .= ";";

		}
		
		return $sql;
		
	}
	
	
	function parseRegex(){
		$this->_fastForward();
		if(current($this->tokens) === '('){
			$this->_fastForward();
			$first = current($this->tokens);
			if($first === '?'){
				
			}
			
			
		}else{
			$msg = current($this->tokens);
			$msg = preg_replace('/</', '&lt;', $msg);
			echo new r2d2_SparqlParserException("IRI expected ",null,key($this->tokens));
		}
	}
	
	function parseComparing($comparator){
		$string ='';
		$this->parser->_rewind();
		
		
	}
	
}

?>