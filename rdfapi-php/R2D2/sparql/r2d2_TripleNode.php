<?php



class r2d2_TripleNode{
	
	var $node;
	
	var $isSet = false;
	
	var $isResultVar = false;
	
	
	function r2d2_TripleNode($node){
		$this->node = $node;
	}
	
	function getTripleNode(){
		return $this->node;
	}
	
	function setIsResultVar(){
		$this->isResultVar = true;
	}
	
	function getIsResultVar(){
		return $this->isResultVar;	
	}
	
	function NodeIsSet($status = true){
		$this->isSet = $status;
	}
	
	function getIsSet(){
		return $this->isSet;
	}
	
	
	
	function getNodeName(){
		
		if (is_a($this->node,'String'))  return $this->node;
		
		elseif (is_a($this->node,'Resource') || 
		    is_a($this->node,'Literal') ||
		    is_a($this->node,'BlankNode')) return $this->node->getLabel(); 	
		
		else return $this->node;
		
		
	}
	
	
	
}

?>