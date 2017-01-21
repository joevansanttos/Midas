<?php



class r2d2_combiner{
	
	
	var $SQLCombinations = array();
	
	var $TripleCombinations = array();
	
	
	function r2d2_combiner($triples){
		$patterns = array(array());
		$i=0;
		foreach($triples as $pattern){			
			$queries = $pattern->getCompatibleQuery();
			$SQL = $pattern->getSQL();
			$j=0;
			foreach ($queries as $query){
				$this->TripleCombinations[$i][$j] = $query;			
				$j++;
			}
			$j=0;
			foreach ($SQL as $cur_SQL){
				$this->SQLCombinations[$i][$j] = $cur_SQL;			
				$j++;
			}
			$i++;
		}
	}
	
	/**
	 * stores all combinated triples as:
	 * $triples[$possibleQuery][TriplePatternNo][tripleInformations]
	 *
	 * @return array
	 */
	function combineTriples(){
		//return $this->makeCombination($this->TripleCombinations, array(),0);
		$TripleStatment = array();
		foreach($this->TripleCombinations as $cur_triplePattern){
			foreach($cur_triplePattern as $cur_TripleMaker){
				array_push($TripleStatment,$cur_TripleMaker);
			}
		}
		return $TripleStatment;
	}
	
	/**
	 * stores all combinated SQL informations as:
	 * $triples[$possibleQuery][TriplePatternNo][SQLInformations]
	 *
	 * @return array
	 */
	function combineSQL(){
		//return $this->makeCombination($this->SQLCombinations, array(),0);
		
		$sqlStatment = array();
		foreach($this->SQLCombinations as $cur_triplePattern){
			foreach($cur_triplePattern as $cur_SQLMaker){
				array_push($sqlStatment,$cur_SQLMaker);
			}
		}
		return $sqlStatment;
	}
	
	
	/**
	* merge every possible query of an triple pattern with every
 	* possible query from each other triple pattern
 	*
 	* @param unknown_type $dim_array
 	* @param unknown_type $cur_string
 	* @return unknown
 	* @access private
 	*/
	function makeCombination($dim_array, $cur_res,$i)
	{		
		
		$new_array = $dim_array;
		$cur_array = array_shift($new_array);

		$ret = array();
		$num = count($new_array);
		
		
		foreach($cur_array as $val){
			$cur_val = array();
			array_push($cur_val,$val);
			$ret_array = array_merge($cur_res,$cur_val);

			
			if($num){
				$ret = array_merge($ret, $this->makeCombination($new_array, $ret_array,0));	
			}
			else
				$ret[] = $ret_array;			
		}

		return $ret;

	}
	
	
}

?>