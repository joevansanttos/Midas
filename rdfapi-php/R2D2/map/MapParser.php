<?php
// ----------------------------------------------------------------------------------
// Class: MapParser.php
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


class MapParser{
	
	
	var $R2D2Model;    // the map
	var $R2D2voc;      // holds the vocabulary terms of the D2RQ vocabulary
	
    /**
     * @var string
     * @access private
     * 
     */
	var $Databases = array();  //stores all mapped Databases 
	var $classMaps = array();  // mapped class Maps
	var $propertyBridges = array(); 
	var $translationTables = array();
	var $uniqueNode = array();

	var $ConditionMap = array();
	var $DatabaseMap = array();   //mapped to a DB
	var $columnResMaker = array();
	var $patternResMaker = array();

	var $processingInstructions = array();
	

	// ------------------------------------------------------------------------------------------------------------
	
	/**
	 *  Constructor of a  new Map Parser 
	 * @access public
	 *
	 */
	function MapParser($R2D2Model){
		$this->R2D2Model = $R2D2Model;
		$this->R2D2voc = new R2D2();
	}
	
	/**
	 * parse the whole Map Model
	 */
	function parseAll() {
		
		//echo "Parsing Map<br>";
		$this->parseProcessingInstructions();
		$this->parseDatabases();
		$this->parseClassMaps();
		$this->parsePropertyBridges();
		$this->parseAdditionalProperties();
		$this->parseRDFTypePropertyBridges();
		
	
	}
	
	/** Returns all Non-RDF-Databases
	 * 
	 * @return string array $databases
	 * @access public
	 * 
	 */
	function getDatabases() {
		return $this->Databases;
	}
	
	/** 
	 * @access public
	 * @return array string
	 */
	function getClassMaps() {
		return $this->classMaps();
	}
	
	/** 
	 * @access public
	 * @return array string
	 */
	function getPropertyBridges() {
		return $this->propertyBridges;
	}
	
	/** 
	 * @access public
	 * @return array string
	 */
	function getProcessingInstructions() {
	    return $this->processingInstructions;
	}
	
	/**
	 * parses all Information about one Database defined in a Map
	 * @access public
	 *
	 */
		
	function parseDatabases(){
		$res=$this->R2D2Model->find(null,$GLOBALS['RDF_type'], $this->R2D2voc->Database);
		
		$triples = $res->triples;
		
		if(count($triples) == null) logging::error("no Database defined");
				
		for (reset($triples); $database = current ($triples); next($triples)):
			$this->StoreDBconfig($database->getSubject());
			//echo "Database: ".$database->getLabel()."<br>";
		endfor;
				
	}
	
	/**
	 * stores all Information about a database in a Datastructure
	 *
	 * @param Object Resource $db
	 */
	function StoreDBconfig($db){
		
		$odbcDSN    =  $this->findOneLiteral($db,$this->R2D2voc->odbcDSN);
		$jdbcDSN    =  $this->findOneLiteral($db,$this->R2D2voc->jdbcDSN);
		$jdbcDriver =  $this->findOneLiteral($db,$this->R2D2voc->jdbcDriver);
		$username   =  $this->findOneLiteral($db,$this->R2D2voc->username);		
		$password   =  $this->findOneLiteral($db,$this->R2D2voc->password);
		$allowDistinct =  $this->findOneLiteral($db,$this->R2D2voc->allowDistinct);
		$expressionTranslator =  $this->findOneLiteral($db,$this->R2D2voc->expressionTranslator);
	
		$ColumnTypes = array();
		$TextColumnTypes    = array();
		$NumericColumnTypes = array();
		$DateColumnTypes    = array();
		
		$NumericColumnTypes = $this->findIteratorLiteral($db,$this->R2D2voc->numericColumn);
		$TextColumnTypes    = $this->findIteratorLiteral($db,$this->R2D2voc->textColumn);
		$DateColumnTypes    = $this->findIteratorLiteral($db,$this->R2D2voc->dateColumn);
		
		if($NumericColumnTypes){
			$DBrows = array_keys($NumericColumnTypes);
			for(reset($DBrows); $current=current($DBrows);next($DBrows)):
				$ColumnTypes[$current] = DB_numericColumnType;
			endfor;	
		}
		
		if($TextColumnTypes){
			$DBrows = array_keys($TextColumnTypes);
			for(reset($DBrows); $current=current($DBrows);next($DBrows)):
				$ColumnTypes[$current] = DB_textColumnType;
			endfor;	
		}

		if($DateColumnTypes){
			$DBrows = array_keys($DateColumnTypes);
			for(reset($DBrows); $current=current($DBrows);next($DBrows)):
				$ColumnTypes[$current] = DB_dateColumnType;
			endfor;	
		}
		
		if (($jdbcDSN != null && $jdbcDriver == null || $jdbcDSN == null && $jdbcDriver != null)) 
			logging::error("d2rq:jdbcDSN and d2rq:jdbcDriver must be used together <br>\n");
		
		$database = new Database($odbcDSN, $jdbcDSN, $jdbcDriver, $username, $password, $ColumnTypes);	
		
		if ($allowDistinct != null){
	    	if ( 0 == strcasecmp($allowDistinct,"true"))
				$database->setAllowDistinct(TRUE);
			else if( 0 == strcasecmp($allowDistinct,"false"))
				$database->setAllowDistinct(FALSE);
			else 
				Logging::error("d2rq:allowDistinct value must be true or false!");
		}
		if ($expressionTranslator != null)
			$database->setExpressionTranslator($expressionTranslator);
		
		// Store all DB- information in an array
		$this->Databases[$db->getLabel()]=$database;			

		
	}
	
	
	
		
	/**
	 * finds all ClassMaps in a map and stores them
	 *
	 */
	function parseClassMaps(){
		$res=$this->R2D2Model->find(null,$this->R2D2voc->dataStorage, null);
		
		$triples = $res->triples;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			
			$dbname       = $triple->getObject();
			$ClassMapName = $triple->getSubject();
			
			$db = $this->Databases[$dbname->getLabel()];
			if($db == null){
				Logging::error("Unknwon d2rq:dataStorage for ClassMap".$ClassMapName->getLabel());
				return;
			}
			
		    $this->StoreClassMap($ClassMapName, $db);
		   // echo "ClassMap: ".$ClassMapName->getLabel()."<br>\n";
		endfor;
		
		$res=$this->R2D2Model->find(null,$GLOBALS['RDF_type'], $this->R2D2voc->ClassMap);
		
		$triples = $res->triples;
		
		for (reset($triples); $triple = current ($triples); next($triples)):

			$ClassMapname = $triple->getSubject();			
			
			if( $this->classMaps[$ClassMapname->getLabel()] === null){
				Logging::error("Missing d2rq:dataStorage for ClassMap".$ClassMapname->getLabel()."<br>\n");
				return;
			}
		endfor;			
		
	}

	
	/**
	 * Stores a classmap
	 *
	 * @param Resource $ClassMap
	 * @param  Database $db
	 * @access private
	 */
	function StoreClassMap($ClassMapNode, $db){
     	foreach ($this->classMaps as $name => $ClassMap)
     	{
         	if ($name === $ClassMapNode->getLabel())
         		return;
     	}		
		
		$pattern     = $this->findOneLiteral($ClassMapNode, $this->R2D2voc->uriPattern);
		$column      = $this->findOneLiteral($ClassMapNode, $this->R2D2voc->uriColumn);
		$bNodeColumns = $this->findOneLiteral($ClassMapNode, $this->R2D2voc->bNodeIdColumns);
		
		$DBSource = null;
		
		if ($pattern != null){
			$DBSource = new Pattern($pattern);
		}
		else if ($column != null){
			if ( $DBSource != null)
				logging::error("Cannot combine d2rq:uriPattern and d2rq:uriColumn on ".$ClassMapNode->getLabel());
			$DBSource = new Column($column);
		}
		else if ($bNodeColumns != null){
			if ( $DBSource != null)
				logging::error("Cannot combine d2rq:bNodeColumns and d2rq:uriColumn/d2rq:uriPattern on ".$ClassMapNode->getLabel());
			$DBSource = new bNodeIdentifier($bNodeColumns, $ClassMapNode);
			
		}
		else{
			Logging::error("ClassMap ".$ClassMapNode->getLabel()." needs a d2rq:uriColumn, d2rq:uriPattern or d2rq:bNodeIDColumns.");
			return;
		}
		
		
		$translateWith = $this->findOneLiteral($ClassMapNode,$this->R2D2voc->translateWith);
		if ($translateWith != null){
			$table = getTranslationTable($translateWith);
			 if ($table == null)
			 		Logging::error("Unknown d2rq:translateWith for ClassMap: ".$ClassMapNode->getLabel());

		}
		
		if ($bNodeColumns != null)
			 $resource = new bNodeMaker ($ClassMapNode->getLabel(), $DBSource);
		else $resource = new URIMaker  ($ClassMapNode->getLabel(), $DBSource);
		
		$this->assertHasColumnTypes($resource,$db);
		$this->classMaps[$ClassMapNode->getLabel()] = $resource;  // put in array
		$this->DatabaseMap[(string)$resource->ID] = $db;
		
		
		if($pattern!==null)
			$this->patternResMaker[(string)$resource->ID] = $resource;
		if($column!==null)
			$this->columnResMaker[(string)$resource->ID] = $resource;
			

			
		
		// find condition for classMap
		$conditions = $this->findIteratorLiteral($ClassMapNode,$this->R2D2voc->condition);
		if($conditions!=null)
		$this->ConditionMap[(string)$resource->ID] = array_keys($conditions);
		else
		$this->ConditionMap[(string)$resource->ID] = null;
			
		// Check d2rq:containsDuplicates in ClassMap  
		$duplicates = $this->findOneLiteral($ClassMapNode,$this->R2D2voc->containsDuplicates);
		if (0 !== strcasecmp($duplicates,"true"))
			$this->uniqueNode[$resource->ID] = $resource;
		else if ($duplicates != null) 
			logging::error("Illegal value '".duplicates."' for d2rq:containsDuplicates on ".$ClassMapNode->getLabel());
			
		
	}
	
	
	/**
	 * Enter description h
	 *
	 * @param Resource $tablenode
	 * @return unkno
	 * @access public
	 */
	function getTranslationTable($TableNode){
		
		//  if translation table exists return it
		foreach ($this->translationTables as $name => $content)
     	{
         	if ($name === $TableNode->getLabel())
         		return $this->translationTables[$TableNode->getLabel()];
     	}		
     	
		if (array_key_exists($TableNode->getLabel(), $this->translationTables))
			return $this->translationTables[$TableNode->getLabel()];
		
		
		$translationTable = new TranslationTable();
		
		$href = $this->findOneLiteral($TableNode,$this->R2D2voc->href);
		if($href !== null){
			$csv = new CSVParser($href);
			$translationTable->addAll($csv->parseURI());
		}
		
		$className = $this->findOneLiteral($TableNode,$this->R2D2voc->javaClass);
		if($className !== null){
			$translationTable->setTranslatorClass($className, $TableNode->getURI());
		}
		
		$res=$this->R2D2Model->find($TableNode,$this->R2D2voc->translation, null);
		$triples = $res->triples;
		if ( ($href === NULL) && ($className === NULL) && ($triples === NULL)){
			logging::warning("TranslationTable ".$TableNode->getLabel()."contains no translations! <br>\n");
		}
		if ( ($href === NULL) && (($className === NULL) || ($triples !== NULL)) ){
			logging::warning("Can´t combine d2rq:javaClass with d2rq:translation on d2rq:href on "
			.$TableNode->getLabel()."<br>\n");
		}
		
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$translation = $triple->getObject();
			$dbValue = $this->findOneLiteral($translation ,$this->R2D2voc->databaseValue);
			$rdfValue = $this->findOneLiteral($translation ,$this->R2D2voc->rdfValue);
			$translationTable->addTranslation($dbValue,$rdfValue);
		
		endfor;
		
		$this->translationTables[$TableNode->getLabel()]=$translationTable;
		
		return $translationTable;
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function parsePropertyBridges(){
		$res=$this->R2D2Model->find(null,$this->R2D2voc->belongsToClassMap, null);

		$triples = $res->triples;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
		
			$propClassMap = $triple->getObject();
			$propertyBridge = $triple->getSubject();
			
			$classMap = $this->classMaps[$propClassMap->getLabel()];				
			if ($classMap === null){
				logging::error("PropertyBridge ".$propertyBridge->getLabel().": d2rq:belongsToClassMap ".$propClassMap->getLabel()."
				is no ClassMap. <br>\n");
			}
			
			$this->StorePropertyBrigde($propertyBridge, $classMap);
			
			//echo "PropertyBridge: ".$propertyBridge->getLabel()." to Class Map: ".$classMap->ID."<br>";
		 
		endfor;
		
		
		
		$res=$this->R2D2Model->find(null,$GLOBALS['RDF_type'], $this->R2D2voc->DatatypePropertyBridge);
		$triples = $res->triples;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$bridge = $triple->getSubject();
			if ( !array_key_exists($bridge->getLabel(), $this->propertyBridges) )
				logging::warning("PropertyBridge ".$bridge->getLabel()." has no d2rq:belongsToClassMap. <br>\n");
		endfor;
		
		$res=$this->R2D2Model->find(null,$GLOBALS['RDF_type'], $this->R2D2voc->ObjectPropertyBridge);
		$triples = $res->triples;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$bridge = $triple->getSubject();
			if ( !array_key_exists($bridge->getLabel(), $this->propertyBridges) )
				logging::warning("PropertyBridge ".$bridge->getLabel()." has no d2rq:belongsToClassMap. <br>\n");
		endfor;
			
	}
	
	/**
	 * stores one property Bridge
	 *
	 * @param Resource $PropertyBridgeNode
	 * @param NodeMaker $ClassMap
	 */
	function StorePropertyBrigde($PropertyBridgeNode,$ClassMap){
		foreach ($this->propertyBridges as $name => $content)
     	{
         	if ($name == $PropertyBridgeNode->getLabel()){
         		Logging::error("Multiple d2rq:belongsToClassMap in ".$PropertyBridgeNode->getLabel());
         		return;
         	}
     	}
		
		/* searching for d2rq:property" for this property Bridge */
		$property = $this->findPropertyForBridge($PropertyBridgeNode);
		
		$DBsource = null;
		
		$column = $this->findOneLiteral($PropertyBridgeNode,$this->R2D2voc->column);
		if($column!= null){		
			//create new		
			$DBsource = new Column($column);
		}
		
		$pattern = $this->findOneLiteral($PropertyBridgeNode,$this->R2D2voc->pattern);
		if ( $pattern != null){
			if($DBsource != null){
				Logging::error("Cannot combin d2rq:column and d2rq:pattern on Property Bridge ".$PropertyBridgeNode->getLabel()."\n < /br>");
			}
	 		$DBsource = new Pattern($pattern);
		}
		
		$regEx = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->valueRegex);
		if ( $regEx != null){
	 		//$DBsource = new RegEx($DBSource, $regEx);
		}
		$valuecontains = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->valueContains);
		if ( $valuecontains != null){
			//$DBsource = new ContainsRestriction($DBSource,$valuecontains);
		}
		
		$maxlen = $this->findOneLiteral($PropertyBridgeNode,$this->R2D2voc->valueMaxLength);
		if ( $maxlen != null){
			
			//$DBsource = new MaxLength($DBSource,(int)$maxlen);
		}
		
		$translate = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->translateWith);
		if ( $translate != null){
			
		}
		

		
		if( TRUE == $this->R2D2Model->contains( new Statement($PropertyBridgeNode,$GLOBALS['RDF_type'],$this->R2D2voc->DatatypePropertyBridge))){
		
			if ( $DBsource == null){
				logging::error("Property Bridge \"".$PropertyBridgeNode->getLabel()."\" needs d2rq:column or d2rq:pattern!");
			}
		
			$datatype = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->datatype);
			$language = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->lang);
			
			if($datatype !== null){
				// to do: check if datatype exists
				
			}
			$object = new LiteralMaker($PropertyBridgeNode->getLabel(), $DBsource, $datatype, $language);
		}
		else if( TRUE == $this->R2D2Model->contains(new Statement($PropertyBridgeNode,$GLOBALS['RDF_type'], $this->R2D2voc->ObjectPropertyBridge))){
		
			$refersTo = $this->findOneLiteral($PropertyBridgeNode,$this->R2D2voc->refersToClassMap);
						
			if($refersTo == null){
					if ( $DBsource == null){
						logging::error("Property Bridge \"".$PropertyBridgeNode->getLabel()."\" needs d2rq:column or d2rq:pattern!");
					}
			
				$object = new URIMaker($PropertyBridgeNode->getLabel(), $DBsource);
			
				if($pattern != null)
					$this->patternMap[(string)$object->ID] = $object;
					//array_push($this->patternMap, $object);
				
				if($column != null)
					$this->columnMap[(string)$object->ID] = $object;	
					//array_push($this->columnMap, $object);
			}
			else{
				$object = $this->classMaps[$refersTo];
				if(null == $object)
					logging::error("PropertyBridge: ".$PropertyBridgeNode->getLabel()." d2rq:refersToClassMap ".$refersTo." is no valid d2rq:ClassMap. Class Map dont exists.<br>\n");;
				if($this->getDatabase($object) != $this->getDatabase($ClassMap))
					logging::error("d2rq:dataStorage for ".$PropertyBridgeNode->getLabel()." don´t match!<br>\n");
			}
		}
		else{
			logging::error("PropertyBridge: ".$PropertyBridgeNode->getLabel()." have to be d2rq:DatatypePropertyBridge or d2rq:ObjectPropertyBridge. <br>\n");
		}
		
		// store aliases
		$aliases = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->alias);
		if ($aliases != null)
			$aliases = Alias::buildAliases($aliases);
		
		// store joins
		$joins = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->join);
		if ($joins != null)
			$joins = Join::buildJoins($joins);
		
		// store conditions
		$conditions = $this->findIteratorLiteral($PropertyBridgeNode,$this->R2D2voc->condition);
		if($conditions != null)
			$conditions = array_keys($conditions);
			
		// generating a property bridge
		$bridge = $this->newPropertyBridge($PropertyBridgeNode, $ClassMap, new FixedNodeMaker($property), $object, $joins, $aliases, $conditions);
		

		
	}
	
	/**
	 * creates a new PropertyBridge 
	 *
	 * @param Node $PropertyNode
	 * @param NodeMaker $subjects
	 * @param NodeMaker $predicates
	 * @param NodeMaker $objects
	 * @param array string $joins
	 * @param array string $aliases
	 * @access private
	 */
	function newPropertyBridge($PropertyNode, $subjects, $predicates, $objects, $joins, $aliases, $conditions){

			
		$PropertyBridge = new PropertyBridge($PropertyNode, $subjects, $predicates, $objects, 
		                                     $this->getDatabase($subjects),$joins,$aliases, $conditions);
        
		$subjUnique = in_array($subjects->ID,array_keys($this->uniqueNode)); 	 
		$predUnique = in_array($predicates->ID,array_keys($this->uniqueNode));
		$objUnique = in_array($objects->ID,array_keys($this->uniqueNode));                                     
		                             
		
		if ($subjUnique && $predUnique && $objUnique == true)
			$allUnique = true;
		else $allUnique = false;
			
		if ($subjUnique || $predUnique || $objUnique == true)
			$someUnique = true;
		else $someUnique = false;
		
		if ( ($allUnique && (count($joins)<=1)) || ($someUnique && ($joins == null)) )
			$containsDuplicates = false;
		else $containsDuplicates = true;
				
		
		$PropertyBridge->setContainDuplicates($containsDuplicates);
		
		if(array_key_exists((string)$subjects->ID,$this->ConditionMap)){
			$subjCond =$this->ConditionMap[(string)$subjects->ID];
			$PropertyBridge->addWhereCond($subjCond);
		}
		if(array_key_exists((string)$predicates->fixedNode->uri,$this->ConditionMap)){
			$predCond =$this->ConditionMap[(string)$predicates->fixedNode->uri];
			$PropertyBridge->addWhereCond($predCond);
		}
		if(array_key_exists((string)$objects->ID,$this->ConditionMap)){
			$objCond = $this->ConditionMap[(string)$objects->ID];
			$PropertyBridge->addWhereCond($objCond);
		}
		
		if ($joins != null){
			for(reset($joins); $current=key($joins);next($joins)):
				$db = $this->getDatabase($subjects);
				$db->searchColumnTypes($current);	
			endfor;			
		}
		
		$URIMatchPolicy = new URIMatchPolicy();
		
		// checks if a give subject and object matches to Columns or Patterns of a Property Bridge
		// if the subject or object matches, a URIPolicy-flag is set
		
		if ( in_array($subjects->ID,array_keys($this->columnResMaker)))
			$URIMatchPolicy->setSubjectBasedOnURIColumn(true);
		
		
		if ( in_array($subjects->ID,array_keys($this->patternResMaker)))
			$URIMatchPolicy->setSubjectBasedOnURIColumn(true);
			
		if ( in_array($objects->ID,array_keys($this->columnResMaker)))
			$URIMatchPolicy->setObjectBasedOnURIColumn(true);
		
		
		if ( in_array($objects->ID,array_keys($this->patternResMaker)))
			$URIMatchPolicy->setObjectBasedOnURIColumn(true);
		
			
		$PropertyBridge->setCorrectURI($URIMatchPolicy);
		
		
		$this->propertyBridges[$PropertyNode->getLabel()] = $PropertyBridge;
		return $PropertyBridge;
		
	}

	
	/**
	 * finds a property for a property Bridge
	 *
	 * @param  Resource $PropertyBridgeNode
	 * @return Resource $property
	 * @access public
	 */
	function findPropertyForBridge($PropertyBridgeNode){
		$property = null;
		// Finds Property for a Property Bridge
		$res=$this->R2D2Model->find(null,$this->R2D2voc->propertyBridge, $PropertyBridgeNode);		
		$triples = $res->triples;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$property = $triple->getSubject();
			if($it->hasNext()){
				Logging::warning("Ignoring multiple d2rq:propertyBridges for d2rq:PropertyBridge ".$PropertyBridgeNode->getLabel()."\n< /br>");
				return null;
			}
		endfor;
		
		
		$res=$this->R2D2Model->find($PropertyBridgeNode,$this->R2D2voc->property,null);
		$triples = $res->triples;
		
		if($triple == null && count($triples)==null){
			Logging::error("Missing d2rq:property for d2rq:PropertyBridge ".$PropertyBridgeNode->getLabel()."\n< /br>");
			return null;
		}
		if($triple != null && count($triples)>0){
			Logging::warning("Ignoring multiple d2rq:property for d2rq:PropertyBridge ".$PropertyBridgeNode->getLabel()."\n< /br>");
			return null;
		}
		if($triple != null){
			return $property;
		}
		
		$property = $triples[0];
		$property = $property->getObject();
		
		if(count($triples)>1){
			Logging::warning("Ignoring multiple d2rq:property statements for d2rq:PropertyBridge ".$PropertyBridgeNode->getLabel()."\n< /br>");
			return null;
		}
		return $property;
		
	}

		


	/**
	 * Enter description here...
	 *
	 */
	function parseAdditionalProperties(){
		$res=$this->R2D2Model->find(null, $this->R2D2voc->additionalProperty, null);
		$triples=$res->triples;
		
		$triple = null;
		$continue = true;
		
		for (reset($triples); $triple = current ($triples); next($triples), $continue != false):
			$ClassMap = $triple->getObject();
			$additionalProp = $triple->getSubject();
			
			$belongstoclassMap = $this->classMaps[$additionalProp->getLabel()];				
			if ($belongstoclassMap == null){
				logging::warning("Ignoring d2rq:AdditionalProperty on  "
				   .$additionalProp->getLabel()." as they are allowed on d2rq:ClassMaps");
				$continue = false;
			}
			else{
			    
				 $property = $this->findOneObject($ClassMap,$this->R2D2voc->propertyName);
				 $value = $this->findOneObject($ClassMap,$this->R2D2voc->propertyValue);
				
				 $this->newPropertyBridge(
				 	$ClassMap,
				 	$belongstoclassMap,
				 	new FixedNodeMaker($property),
				 	new FixedNodeMaker($value), 
				 	array(),
				 	array(),
				 	array());
			}
			
		endfor;
		
	}
	
	

	/**
	 * Enter description here...
	 *
	 * @access private
	 */
	function parseRDFTypePropertyBridges(){
		$res=$this->R2D2Model->find(null, $this->R2D2voc->classMap, null);
		$triples = $res->triples;
		
		$triple = null;
		$ClassMap = null;
		$rdfsClass = null;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$ClassMap = $triple->getObject();
			$rdfsClass = $triple->getSubject();
			
			$this->StoreRDFTypePropertyBridge($ClassMap, $rdfsClass);
			//echo "PropertyBridge: ".$rdfsClass->getLabel()." to Class Map: ".$classMap->ID."<br>";
		endfor;
		
		$res=$this->R2D2Model->find(null, $this->R2D2voc->class_, null);
		$triples = $res->triples;
		$triple = null;
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$ClassMap = $triple->getObject();
			$rdfsClass = $triple->getSubject();
			
			$this->StoreRDFTypePropertyBridge($rdfsClass, $ClassMap);
						//echo "PropertyBridge: ".$rdfsClass->getLabel()." to Class Map: ".$ClassMap->getLabel()."<br>";
		endfor;
		
	}
	
	/**
	 * adds a RDF Type Property Bridge (Mapping from a RDF class to Database Table). 
	 *
	 * @param  Resource $ClassMap
	 * @param  Resource $rdfCslass
	 * @access private
	 */
	function StoreRDFTypePropertyBridge($ClassMap, $rdfCslass){
		
		$belongstoclassMap = $this->classMaps[$ClassMap->getLabel()];	
		if ($belongstoclassMap == null){		
			logging::error("ClassMap: ".$ClassMap->getLabel()." referenced from ".$rdfCslass-getLabel()." is no d2rq:ClassMap");		
			return;
		}
		
		$this->newPropertyBridge(
			new BlankNode($ClassMap->getURI()),
			$belongstoclassMap,
			new FixedNodeMaker($GLOBALS['RDF_type']),
			new FixedNodeMaker($rdfCslass),
			array(),array(),array());
		
	
	}
	
	/**
	 * returns the mapped Database for a resource
	 *
	 * @param array string $resource
	 * @return Database
	 * @access private
	 */
	function getDatabase($resource){
		$index = (string)$resource->ID;
		return  $this->DatabaseMap[$index];		
	}
	

	/**
	 * Enter description here...
	 *
	 */
	function parseProcessingInstructions(){
		$res=$this->R2D2Model->find(null,$GLOBALS['RDF_type'], $this->R2D2voc->ProcessingInstructions);
		
		$triples = $res->triples;
		
		for (reset($triples); $instruction = current ($triples); next($triples)):
		
			$instruction = $instruction->getSubject();
			
			$result = $this->findIteratorLiteral($instruction,null);
			array_push($this->processingInstructions ,$result);
			
		endfor;
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @param NodeMaker $node
	 * @param Database $db
	 */
	function assertHasColumnTypes($node,$db){
		$columns = $node->getColumns();
		for(reset($columns); $current=current($columns);next($columns)):
				$db->assertHasType($current);
		endfor;	
		
	}

	
	
	/**
	 * returns the literal for given subject and predicate
	 *
	 * @param Resource $subj
	 * @param Resource $pred
	 * @return string $obj
	 */	
	function findOneObject($subj, $pred){
		//echo "subj:".$sub ."+pred: ".$pred; echo "<br>";
		$res = $this->R2D2Model->find($subj,$pred,null);
		$triples = $res->triples;
		
		if(count($triples)==null){
			return null;
		}
			
		$obj = $triples[0]->getObject();
					 
		if(count($triples)>1){
			logging::error("multiple ".$pred->getLabel(). "for " .$subj->getLabel());
			 // error multiple pred on subj
			 return null;
		}
		return $obj;	
	}
	
	
	function findOneLiteralOrURI($subj,$pred){
		$object = $this->findOneObject($subj,$pred);
		
		if ($object == null)
			return null;
		
		if(is_a($object,"Literal"))
			return $object->getLabel();
		else if(is_a($object,"Resource"))
			return $object->getURI();
		else logging::error($pred->getLabel()." for ".$subj->getLabel()." must be a literal or URI!");
	}
	
	/**
	 * Enter description here...
	 *
	 * @param Resource $subj
	 * @param Resource $pred
	 * @return Literal $obj
	 */
	function findOneLiteral($subj,$pred){
		$obj = $this->findOneObject($subj,$pred);
		if ($obj == null) 
			return null;
		
		return $obj->getLabel();
	}
	
	
		
	/**
	 * returns the literal for given subject and predicate
	 *
	 * @param Object Resource $subj
	 * @param Object Resource $pred
	 * @return string $obj_array
	 */	
	function findIteratorLiteral($subj, $pred){
		//echo "subj:".$sub ."+pred: ".$pred; echo "<br>";
		$res = $this->R2D2Model->find($subj,$pred,null);
		$triples = $res->triples;
		
		if(count($triples)==null){
			return null;
		}
		
		$obj_array = array(); // the return value
		
		for (reset($triples); $triple = current ($triples); next($triples)):
			$obj = $triple->getObject();
			$obj = $obj->getLabel();
			$obj_array[$obj]= $pred->getLabel();
			
		endfor;
		
		return $obj_array;	
	}
	

		
	
}
 
 
 
?>
