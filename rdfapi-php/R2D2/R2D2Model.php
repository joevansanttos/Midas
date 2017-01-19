<?php
// ----------------------------------------------------------------------------------
// class: R2D2Model.php
// ----------------------------------------------------------------------------------

/* offers an R2D2 read-only RAP model backed by a non-RDF database.
 * This class provides methods for quering an R2D2-Modell 
 *
 * History:
 * 07-07-2006                : 
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 * @version V0.1
 * @see de....R2D2Model
 * 
 * @package R2D2
 * @access public
 */

define('PACKAGE_MAP','map/MAP.php');
define('PACKAGE_FIND','find/FIND.php');
define('PACKAGE_R2D2_SPARQL','sparql/r2d2_SPARQL.php');
define('PACKAGE_TOOLS','tools/tools.php');
include_once ( R2D2_INCLUDE_DIR . "R2D2DbModel.php");
include_once ( R2D2_INCLUDE_DIR . "createMap/generateR2D2MAP.php");
include_once ( R2D2_INCLUDE_DIR . PACKAGE_MAP);
include_once ( R2D2_INCLUDE_DIR . PACKAGE_FIND);
include_once ( R2D2_INCLUDE_DIR . PACKAGE_R2D2_SPARQL);
include_once ( R2D2_INCLUDE_DIR . PACKAGE_TOOLS);
include_once ( R2D2_INCLUDE_DIR . "logging/Logging.php");

include(RDFAPI_INCLUDE_DIR . "vocabulary/RDF.php");


// NODE TYPE
define('URI',1);
define('LITERAL',2);
define('BNODE',3);
define('FIXEDNODE',4);


// VALUE SOURCE TYPE
define('DBSOURCE_COLUMN',1);
define('DBSOURCE_PATTERN',2);
define('DBSOURCE_BNODEID',3);
define('DBSOURCE_REGEX',4);

define("DB_noColumnType",0);
define("DB_numericColumnType",1);
define("DB_textColumnType",2);
define("DB_dateColumnType",3);




 
 class R2D2Model extends Model{	
 	
/**
 	 * Name of the R2D2 Model
 	 *
 	 * @var string
 	 */
 	var $baseURI;

 	/**
	* Triples of the MemModel
	* @var		array
	* @access	private
	*/
	var $triples = array();
 	
 	/**
 	 * Enter description here...
 	 *
 	 * @var array of strings
 	 * @access private
 	 */
 	var $propertyBridges = array();
 	
 	/**
 	 * Enter description here...
 	 *
 	 * @var array of strings
 	 * @access private
 	 */
 	var $processingInstructions = array();	
 	
 	var $parsedNamespaces = array();
 	
 	var $cachedSQLQueries = array();
 	
 	var $log;
 
 	
 	
 	// ------------------------------------------------------------------------------------------------------------
     
     /** 
	 * Create a non-RDF database-based model. The model is created
	 * from a D2RQ map that must be provided in xml/rdf of N3 notation.
	 * @param string $mapURL  URL of the D2RQ map to be used for this model
	 * @access public
	 */     
	function R2D2Model($mapURL) {
		
		$this->baseURI = $mapURL;
		
		$this->log = new logging();

		// Create a new memory model
		$R2D2Model = new MemModel();

		// Load and parse document into memory
		$R2D2Model->load($mapURL);
	
		// store a Map into internal data structures to be accessible
		$this->MapInit($R2D2Model);
		
	}
	
	
	
	/**
	 * Enter description here...
	 *
	 * @param memModel $R2D2Model
	 * @access private
	 */
	function MapInit($R2D2Model){
		
		$parser = new MapParser($R2D2Model);
		$parser->parseAll();
		
		$this->propertyBridges = $parser->getPropertyBridges();
		$this->processingInstructions = $parser->getProcessingInstructions();
	}
	
	function getPropertyBridges(){
		return $this->propertyBridges;
	}
	

	


		
	/**
     * General method to search for triples.
	 * NULL input for any parameter will match anything.
	 * Example:  $result = $m->find( NULL, NULL, $node );
	 * Finds all triples with $node as object.
	 * Returns an empty array if nothing is found.
	 *
	 *
	 * @param Resource $subject
	 * @param Resource $predicate
	 * @param Resource $object
	 * @access public
	 * @throws	PhpError
	 * @return array string
	 */
	function find ($subject=null,$predicate=null,$object=null){
			
		if (
		(!is_a($subject, 'Resource') && $subject != NULL) ||
		(!is_a($predicate, 'Resource') && $predicate != NULL) ||
		(!is_a($object, 'Node') && $object != NULL)
		) {
			$errmsg = RDFAPI_ERROR . '(class: R2D2Model; method: find): Parameters must be subclasses of Node or NULL';
			trigger_error($errmsg, E_USER_ERROR);
		}
		
		$subName = tools::checkNodeType($subject);
		$predName = tools::checkNodeType($predicate);
		$objName = tools::checkNodeType($object);
		
		
		$this->log->debug("-------------------------------------------------------------<br>\n");
		$this->log->debug("                Find(SPO) Query Pattern                      <br>\n");
		$this->log->debug("-------------------------------------------------------------<br>\n");
		$this->log->debug("Subject  : ".$subName."<br>\n");
		$this->log->debug("Predicate: ".$predName."<br>\n");
		$this->log->debug("Object   : ".$objName."<br>\n");
		$this->log->debug("-------------------------------------------------------------<br>\n");
		
		
		$combiner = new QueryCombiner();
		$context = new QueryContext();
		
		$it = $this->propertyBridges;
		for(reset($it); $bridge=current($it);next($it)):
		
			if(!$bridge->searchCorrectBridge($subject,$predicate,$object,$context)){
				continue;
			}				
			$ID = $bridge->getID();
			$this->log->debug( "-------------------------------------------------------------<br>\n");
			$this->log->debug( "Using Property Bridge: ".$ID->getLabel()."<br>\n");
				
			// adding all possible queries to $combiner
			$tripleQuery = new TripleQuery($bridge, $subject, $predicate, $object);
			$combiner->add($tripleQuery);
			
		endfor;	

	
		// create resultIterator for every possible query
		$resultIt = $combiner->getResultIterator();
		
		// init executed SQL queries
		$tripleResSet = $resultIt->tripleResultSets;
		for(reset($tripleResSet); $it=current($tripleResSet);next($tripleResSet)):
				$this->cachedSQLQueries[$it->SQL] = '';
				$this->log->debug("SQL statement execute: \"".$it->SQL."\"<br><br>\n");
		endfor;
		
		
		$result = array();
		
		$result = $resultIt->GetAllTriples();
		
		$this->triples = $result;	
		
		return $result;
		
	
	}

			
	/**
     * Search for triples and returns only triples with the defined offset and limi
	 * NULL input for any parameter will match anything.
	 * Example:  $result = $m->find( NULL, NULL, $node,0,false );
	 * Finds all triples with $node as object.
	 * Returns an empty array if nothing is found.
	 *
	 * @param Resource $subject
	 * @param Resource $predicate
	 * @param Resource $object
	 * @access public
	 * @throws	PhpError
	 * @return array string
	 */
	function findOffsetLimit ($subject=null,$predicate=null,$object=null, $offset=0,$limit=false){
			
		if (
		(!is_a($subject, 'Resource') && $subject != NULL) ||
		(!is_a($predicate, 'Resource') && $predicate != NULL) ||
		(!is_a($object, 'Node') && $object != NULL)
		) {
			$errmsg = RDFAPI_ERROR . '(class: R2D2Model; method: find): Parameters must be subclasses of Node or NULL';
			trigger_error($errmsg, E_USER_ERROR);
		}
		
		$subName = tools::checkNodeType($subject);
		$predName = tools::checkNodeType($predicate);
		$objName = tools::checkNodeType($object);
		
		
		$this->log->debug("-------------------------------------------------------------<br>\n");
		$this->log->debug("                Find(SPO) Query Pattern                      <br>\n");
		$this->log->debug("-------------------------------------------------------------<br>\n");
		$this->log->debug("Subject  : ".$subName."<br>\n");
		$this->log->debug("Predicate: ".$predName."<br>\n");
		$this->log->debug("Object   : ".$objName."<br>\n");
		$this->log->debug("-------------------------------------------------------------<br>\n");
		
		
		$combiner = new QueryCombiner();
		$context = new QueryContext();
		
		$it = $this->propertyBridges;
		for(reset($it); $bridge=current($it);next($it)):
		
			if(!$bridge->searchCorrectBridge($subject,$predicate,$object,$context)){
				continue;
			}				
			$ID = $bridge->getID();
			$this->log->debug( "-------------------------------------------------------------<br>\n");
			$this->log->debug( "Using Property Bridge: ".$ID->getLabel()."<br>\n");
				
			// adding all possible queries to $combiner
			$tripleQuery = new TripleQuery($bridge, $subject, $predicate, $object,null,$offset,$limit);
			$combiner->add($tripleQuery);
			
		endfor;	

	
		// create resultIterator for every possible query
		$resultIt = $combiner->getResultIterator();
		
		// init executed SQL queries
		$tripleResSet = $resultIt->tripleResultSets;
		for(reset($tripleResSet); $it=current($tripleResSet);next($tripleResSet)):
				$this->cachedSQLQueries[$it->SQL] = '';
				$this->log->debug("SQL statement execute: \"".$it->SQL."\"<br><br>\n");
		endfor;
		
		
		$result = array();
		
		$result = $resultIt->GetAllTriples();
		
		$this->triples = $result;	
		
		return $result;
		
	
	}
	
	/**
	* Set a base URI for the R2D2Model.
	* Affects creating of new resources and serialization syntax.
	*
	* @param	string	$uri
	* @throws  SqlError
	* @access	public
	*/
	function setBaseURI($uri) {
		$this->baseURI = $uri;
	}
	
	
	/**
	* Add a new triple to this R2D2Model.
	*
	* @param	object Statement	&$statement
	* @throws	PhpError
	* @throws  SqlError
	* @access	public
	*/
	function add(&$statement) {
		$this->log->warning( "can´t add in R2D2-Model. model supports only mapping");
	}
 	function _addStatementFromAnotherModel($statement, &$blankNodes_tmp) {
 		$this->log->warning( "can´t add in R2D2-Model. model supports only mapping");
 	}
 	function addWithoutDuplicates(&$statement) {
		$this->log->warning( "can´t add in R2D2-Model. model supports only mapping");
	}
	function remove ($statement){
		$this->log->warning( "can´t remove in R2D2-Model.model supports only mapping");
	}
	
		/**
	* Dumps of the R2D2Model including all triples.
	*
	* @access	public
	* @return	string
	*/
	function toStringIncludingTriples() {
		$dump = $this->toString() . chr(13);
		foreach($this->triples as $value) {
			$dump .= $value->toString() . chr(13);
		}
		return $dump;
	}
	
	
	/**
	* Writes the RDF serialization of the R2D2Model as HTML.
	*
	* @access	public
	*/
	function writeAsHtml() {
		
		$memModel = $this->StoreAsMemModel();
		if($memModel){	
			require_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
			$ser = new RdfSerializer();
			$rdf =& $ser->serialize($memModel);
			$rdf = htmlspecialchars($rdf, ENT_QUOTES);
			$rdf = str_replace(' ', '&nbsp;', $rdf);
			$rdf = nl2br($rdf);
			echo $rdf;
		}	
 	}
 	
 	/**
	* Writes the RDF serialization of the R2D2Model as HTML table.
	*
	* @access	public
	*/
	function writeAsHtmlTable() {
		$memModel = $this->StoreAsMemModel();
		if($memModel){	
			// Import Package Utility
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);
			RDFUtil::writeHTMLTable($memModel);
		}
	}
	/**
	* Writes the RDF serialization of the R2D2Model as HTML table.
	*
	* @access	public
	* @return	string
	*/
	function writeRdfToString() {
		$memModel = $this->StoreAsMemModel();
		if($memModel){
			// Import Package Syntax
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
			$ser = new RdfSerializer();
			$rdf =& $ser->serialize($memModel);
			return $rdf;
		}
	}
	
	
	/**
	* Saves the RDF,N3 or N-Triple serialization of the R2D2Model to a file.
	* You can decide to which format the model should be serialized by using a
	* corresponding suffix-string as $type parameter. If no $type parameter
	* is placed this method will serialize the model to XML/RDF format.
	* Returns FALSE if the R2D2Model couldn't be saved to the file.
	*
	* @access	public
	* @param 	string 	$filename
	* @param 	string 	$type
	* @throw   PhpError
	* @return	boolean
	*/
	function saveAs($filename, $type ='rdf') {

		$memModel = $this->StoreAsMemModel();
		if($memModel){

		// get suffix and create a corresponding serializer
		if ($type=='rdf') {
			// Import Package Syntax
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_RDF);
			$ser=new RdfSerializer();
		}elseif ($type=='nt') {
			// Import Package Syntax
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
			$ser=new NTripleSerializer();
		}elseif ($type=='n3') {
			// Import Package Syntax
			include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SYNTAX_N3);
			$ser=new N3Serializer();
		}else {
			echo 'Serializer type not properly defined. Use the strings "rdf","n3" or "nt".';
			return false;
		};

		return $ser->saveAs($memModel, $filename);
		}
		else 	return null;
	}
	
	
	/**
	* Tests if the R2D2Model contains the given triple.
	* TRUE if the triple belongs to the R2D2Model;
	* FALSE otherwise.
	*
	* @param	object Statement	&$statement
	* @return	boolean
	* @access	public
	*/
	function contains(&$statement) {

		$memModel = $this->StoreAsMemModel();
		if($memModel){
			foreach($this->triples as $value) {
				if ($value->equals($statement))
						return TRUE; 
			}
			return false;
		}
	}
	
	
	
	/**
	* Method to search for triples using Perl-style regular expressions.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->find_regex( NULL, NULL, $regex );
	* Finds all triples where the label of the object node matches the regular expression.
	* Returns null if nothing is found.
	*
	* @param	string	$subject_regex
	* @param	string	$predicate_regex
	* @param	string	$object_regex
	* @return	object MemModel
	* @access	public
	*/
	function findRegex($subject_regex, $predicate_regex, $object_regex) {
		//if($subject_regex == NULL && $predicate_regex == NULL && $object_regex == NULL)
		//	return $this;
	}
	

	
 /**
 * Searches for triples using find() and tracks forward blank nodes
 * until the final objects in the retrieved subgraphs are all named resources.
 * The method calls itself recursivly until the result is complete.
 * NULL input for subject, predicate or object will match anything.
 * Inputparameters are ignored for recursivly found statements.
 * Returns a new MemModel or adds (without checking for duplicates)
 * the found statements to a given MemModel.
 * Returns an empty MemModel, if nothing is found.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * WARNING: This method can be slow with large models.
 * NOTE:    Blank nodes are not renamed, they keep the same nodeIDs
 *          as in the queried model!
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @author   Christian Lehmann
 * @param    object Node     $subject
 * @param    object Node     $predicate
 * @param    object Node     $object
 * @param    object MemModel $object
 * @return   object MemModel
 * @access   public
 * @throws   PhpError
 */

 function findForward($subject, $predicate, $object, $newModel = NULL){
	if (!is_a($newModel, "MemModel"))
     {
         $newModel = New MemModel;
     }

     
     //$it    = $model->findAsIterator($subject, $predicate, $object);
     $R2D2Model = new R2D2Model($this->baseURI);
     $result = $R2D2Model->find($subject,$predicate,$object);
     
     for (reset($result); $triple = current ($result); next($result)):	
		// add Statement to memModel
		$newModel->add($triple);
		$o = $triple->getObject();
		if(is_a($o,'BlankNode'))
			$R2D2Model->findForward($o,NULL,NULL,&$newModel);
	endfor;
     

/*     while ($it->hasNext())
     {
         $statement = $it->next();
         $newModel->add($statement);
         if (is_a($statement->object(),'BlankNode'))
         {
             $model->findForward($statement->object(), NULL, NULL,&$newModel);
         }
     }*/
     return $newModel;
	}
	
	 function & getMemModelByRDQL($queryString, $closure = FALSE){
	 	echo "Operation not supported in R2D2Model";
	 }
	 
 /**
 * Alias for RDFUtil::visualiseGraph(&$model, $format, $short_prefix)
 *
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 * Note: See RDFUtil for further Information.
 * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
 *
 * @author   Anton Köstlbacher <anton1@koestlbacher.de>
 * @param    string  $format
 * @param    boolean $short_prefix
 * @return   string, binary
 * @access   public
 * @throws   PhpError
 */

 function visualize($format = "dot", $short_prefix = TRUE)
 {
 	$memModel = $this->StoreAsMemModel();
 	if ($memModel)
     	return RDFUtil::visualizeGraph($memModel, $format, $short_prefix);
 }
	

	function sparqlQuery($query,$resultform = false){
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_SPARQL);
 		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_DATASET); 	
 		$dataset = new DatasetMem();
 		$dataset->setDefaultGraph($this);
 		$parser = new SparqlParser();
 		$q = $parser->parse($query);
 	
 		$engine = new SparqlEngine();
 		return $engine->queryModel($dataset,$q,$resultform);
 	}
 	
	/**
	* Performs a SPARQL query against a R2D2 model. The model is converted to
 	* an RDF Dataset. The result can be retrived in SPARQL Query Results XML Format or
 	* as an array containing the variables an their bindings.
 	*
 	* @param  String $query      the sparql query string
 	* @param  String $resultform the result form ('xml' for SPARQL Query Results XML Format)
 	* @return String/array       
 	*/
	function sparqlQuery1($query,$resultform = false){
		include_once(R2D2_INCLUDE_DIR.PACKAGE_R2D2_SPARQL);
 		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_DATASET); 	
 		$dataset = new DatasetMem();
 		$dataset->setDefaultGraph($this);
 		$parser = new r2d2_SparqlParser();
 		$q = $parser->parse($query);
 	
 		$engine = new r2d2_SparqlEngine();
 		return $engine->queryModel($dataset,$q,$resultform);
 	}
	

	
	/**
	* Checks if MemModel is empty
	*
	* @return	boolean
	* @access	public
	*/
	function isEmpty() {
		if (count($this->triples) == 0) {
			return TRUE;
		} else {
			return FALSE;
		};
	}
	
	
	/**
	 * Return the number of triples in a R2D2 model.
	 * To get the size of a R2D2 model a find operation have to be
	 * executed.
	 *
	 * @return int size
	 * @access public
	 */
	function size(){
		return count($this->triples);
	}

	/**
	* Short Dump of the R2D2Model.
	*
	* @access	public
	* @return	string
	*/
	function toString() {
		return 'R2D2Model[baseURI=' . $this->getBaseURI() . ';  size=' . $this->size() . ']';
	}


	
	/**
	 * stores all triples stored in $this->triples from a find-operation as a new memModel
	 * if no triples -> return null
	 *
	 * @return MemModel 
	 * @access public
	 */
	function getMemModel(){

		$memModel = new MemModel();
		
		if( $this->triples != null){
 		
	 		// create a new memModel and fill it with triples from find-operation
 			 for (reset($$this->triples); $triple = current ($this->triples); next($this->triples)):	
				// add Statement to memModel
				$memModel->add($triple);
			endfor;
			return $memModel;
	 	}
 		else{ // get the whole model, because no find operation has been set
 			
			$result = $this->find(null,null,null);
			if($result){
				for (reset($result); $triple = current ($result); next($result)):
					$memModel->add($triple);
				endfor;
				return $memModel;
			}
			else return $memModel;
 		}
	}
	
	/**
	 * stores all triples stored in $this->triples from a find-operation as a new DbModel
	 * if no triples -> return null
	 *
	 * @return DbModel 
	 * @access public
	 */
	function getDbModel($db){
		$memModel = $this->StoreAsMemModel();
 		if ($memModel){
 			
 			$rdf_database = new DbStore($db->ADODriver,$db->ip,$db->dbName, $db->username,$db->password);
 			
 			$rdf_database->putModel($memModel);
 			
 			$rdf_database->close();
 			
 		}
	}
	
	/**
	 * stores a given Map configuraton in the RAP Database
	 *
	 * @param $string map the Map path 
	 * @param $string $modelName  the name of the model in database
	 * @param DBInfo $db information about the database connection
	 * @access public
	 */
	function StoreMapAsDbModel($map,$modelName,$db){
		$memModel = new MemModel();
		// Load and parse document
		$memModel->load($map);
 		if ($memModel){
 			$rdf_database = new DbStore($db->getDBSystem(),$db->getHost(),$db->getDBname(), $db->getUser(),$db->getPWD());

 			
 			if (!$rdf_database->modelExists($modelName))
				$rdf_database->putModel($memModel,$modelName);	
 		}
 		$rdf_database->close();
	}
	
	
	
	
		/**
	* Returns a FindIterator for traversing the R2D2Model.
	* @access	public
	* @return	object	FindIterator
	*/
	function & findAsIterator($sub=null,$pred=null,$obj=null) {
		// Import Package Utility
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

		return new FindIterator($this,$sub,$pred,$obj);
	}
	
	/**
	* Returns a FindIterator for traversing the R2D2Model.
	* @access	public
	* @return	object	FindIterator
	*/
	function & iterFind($sub=null,$pred=null,$obj=null) {
		// Import Package Utility
		include_once(RDFAPI_INCLUDE_DIR.PACKAGE_UTILITY);

		return new IterFind($this,$sub,$pred,$obj);
	}
	


	/**
	* Searches for triples and returns the first matching statement.
	* NULL input for any parameter will match anything.
	* Example:  $result = $m->findFirstMatchingStatement( NULL, NULL, $node );
	* Returns the first statement of the MemModel where the object equals $node.
	* Returns an NULL if nothing is found.
	* You can define an offset to search for. Default = 0
	*
	* @param	object Node	$subject
	* @param	object Node	$predicate
	* @param	object Node	$object
	* @param	integer	$offset
	* @return	object Statement
	* @access	public
	*/
	function findFirstMatchingStatement($subject, $predicate, $object, $offset = 0) {

		if ($offset == 0){
			$result = $this->find($subject,$predicate,$object);
		}
		else{
			$result = $this->triples;
		}
		
		if(array_key_exists($offset,$result))
				return $result[$offset];
		else return NULL;
	}
	
	/**
	* Returns the models namespaces.
	*
	* @access   public
	* @return   Array
	*/
	function getParsedNamespaces(){
		if(count($this->parsedNamespaces)!=0){
			return $this->parsedNamespaces;
		}else{
			return false;
		}
	}
	
		/**
	* Adds a namespace and prefix to the model.
	*
	* @access   public
	* @param    String 
	* @param    String
	*/
	function addNamespace($prefix, $nmsp){
		$this->parsedNamespaces[$nmsp]=$prefix;
	}
	
	/**
	* removes a single namespace from the model
	*
	* @access   public
	* @param    String $nmsp
	*/
	function removeNamespace($nmsp){
		if(isset($this->parsedNamespaces[$nmsp])){
			unset($this->parsedNamespaces[$nmsp]);
			return true;
		}else{
			return false;
		}
	}
	
	/**
	 * Enables D2RQ debug messages.
	 * @access public
	 */
	function enableDebug() {
		$this->log->setDebug(TRUE);
	}
	
	/**
	 * Checks if Debug mode is set
	 * @return boolean 
	 * @access public
	 */
	function isDebug(){
		return $this->log->getDebug();
	}


	
}
	

  
 
?>
