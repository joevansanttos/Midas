<?php



class r2d2_TripleGenerator{
		
	var $context;
	
	var $combiner;
	
	//var $SQLMaker = array();
	
	var $triple;
	
	var $subject;
	
	var $predicate;
	
	var $object;
	
	var $SQL;
	
	var $resultIt;
	
	
	function r2d2_TripleGenerator($triple){
		
		$this->combiner = new QueryCombiner();
		$this->context  = new QueryContext();
		$this->triple = $triple;
		
		$this->subject = new r2d2_TripleNode($triple->getSubject());
		$this->predicate = new r2d2_TripleNode($triple->getPredicate());
		$this->object = new r2d2_TripleNode($triple->getObject());	
	}
	
	
	function AddCombiner($tripleQuery){
		$this->combiner->add($tripleQuery);
	}
	
	function getQueryContext(){
		return $this->context;
	}
	
		
	function setResultIterator(){
		 $this->SQL = $this->combiner->getSPARQL2SQL();
		 $this->resultIt = $this->combiner->getSPARQLResultIterator($this->SQL);		 		
	}
	
	function getResultIterator(){
		return $this->resultIt;
	}
	
	/*function addSQL($SQLMaker){
		array_push($this->SQLMaker,$SQLMaker);
	}*/
	
	function getSubject(){
		return $this->subject;
	}
	
	function getPredicate(){
		return $this->predicate;
	}
	
	function getObject(){
		return $this->object;
	}
	
	function getR2D2Mapping(){
		return $this->SQL;
	}
	
	function getCompatibleQuery(){
		return $this->combiner->getCompatibleQuery();
	}
	
	function getSQL(){
		return $this->SQL;
	}
	
	function getSQLFrom(){
		return $this->SQL();
	}
	
	function getDatabase(){
		return $this->SQL[0]->getDatabase();
	}
	
	function getTriple(){
		return $this->triple;
	}
	
	function getResultIt(){
		return $this->resultIt;
	}
	
}


?>