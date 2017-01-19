<?php

// ----------------------------------------------------------------------------------
// Class: R2D2
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

/**
 * defines the D2RQ vocabulary terms
 *
 * <p>History:<br>
 * 07-06-2006:   Initial version of this class.<br>

 * @author Christian Lehmann Lehmann.Christian@gmx.net
 * @version V0.1
 * @access public
 */
 
 //require_once( RDFAPI_INCLUDE_DIR . './..model/Node.php' );
 
define('R2D2Delimiter','@@');
 
class R2D2 {

    /** The D2RQ Namespace    
     * 
     * @var string 
     * @access public 
     * 
     */
    var $uri = "http://www.wiwiss.fu-berlin.de/suhl/bizer/D2RQ/0.1#";
   


    
    
    // Processing Informations
    /** D2RQ Processing Instructions
     * 
     * @var object Node         Node of a Processing Instruction
     * @access public 
     */
     var $queryHandler;
     var $ProcessingInstructions;
       
    // Database
    /** Database stuff
     * @var object Node
     * @access public
     */
    var $Database;
    var $odbcDSN ;
    var $jdbcDSN ;
    var $jdbcDriver ;
    var $username ;
    var $password ;
    var $numericColumn ;
    var $textColumn ;
    var $dateColumn ;
    var $allowDistinct ; // true/false
    var $expressionTranslator ; // className

    // ClassMap
    var $ClassMap ;
    var $uriPattern ;
    var $uriColumn ;
    var $bNodeIdColumns ;
    var $dataStorage ;
    var $containsDuplicates ;
    var $classMap ;
    var $class_ ;
    
    // PropertyBridge
    var $DatatypePropertyBridge ;
    var $ObjectPropertyBridge ;
    var $column ;
    var $join ;
    var $alias ; // jg
    var $pattern ;
    var $belongsToClassMap ;
    var $refersToClassMap ;
    var $datatype ;
    var $lang ;
    var $translateWith ;
    var $valueMaxLength ;
    var $valueContains ;
    var $valueRegex ;
    var $propertyBridge ;
    var $property ;

    // ClassMap and PropertyBridge
    var $condition ;

    // AdditionalProperty
    var $AdditionalProperty ;
    var $additionalProperty ;
    var $ropertyName ;
    var $propertyValue ;

    // TranslationTable
    var $TranslationTable ;
    var $href ;
    var $javaClass ;
    var $translation ;
    var $Translation ;
    var $databaseValue ;
    var $rdfValue ;
    
    
    
    // ------------------------------------------------------------------------------------------------------------
    
    
    
    
        /** Returns the URI for this schema
      *  @return  string the URI for this schema
      *  @access public
      *  
      */           
    function getURI(){ 
    	return $this->uri; 
     }
    
    /** Constructor of the class R2D2
     *  Sets all URI Informations of an Map
     * This URI will be used to find a mapping rule
     * @access public
     */
    function R2D2(){
    	
    	
    	$this->ProcessingInstructions = new Resource ($this->uri."ProcessingInstructions");
    	$this->queryHandler = new Resource ($this->uri."queryHandler");

    	$this->Database = new Resource (($this->uri."Database"));
    	$this->odbcDSN = new Resource ($this->uri."odbcDSN");
    	$this->jdbcDSN = new Resource ($this->uri."jdbcDSN");
    	$this->jdbcDriver = new Resource ($this->uri."jdbcDriver");
    	$this->username = new Resource ($this->uri."username");
    	$this->password = new Resource ($this->uri."password");
    	$this->numericColumn = new Resource ($this->uri."numericColumn");
    	$this->textColumn = new Resource ($this->uri."textColumn");
    	$this->dateColumn = new Resource ($this->uri."dateColumn");
    	$this->allowDistinct = new Resource ($this->uri."allowDistinct"); // true/false
    	$this->expressionTranslator = new Resource ($this->uri."expressionTranslator"); // className

    	// ClassMap
    	$this->ClassMap = new Resource ($this->uri."ClassMap");
    	$this->uriPattern = new Resource ($this->uri."uriPattern");
    	$this->uriColumn = new Resource ($this->uri."uriColumn");
    	$this->bNodeIdColumns = new Resource ($this->uri."bNodeIdColumns");
    	$this->dataStorage = new Resource ($this->uri."dataStorage");
    	$this->containsDuplicates = new Resource ($this->uri."containsDuplicates");
    	$this->classMap = new Resource ($this->uri."classMap");
    	$this->class_ = new Resource ($this->uri."class");

    	// PropertyBridge
    	$this->DatatypePropertyBridge = new Resource ($this->uri."DatatypePropertyBridge");
    	$this->ObjectPropertyBridge = new Resource ($this->uri."ObjectPropertyBridge");
    	$this->column = new Resource ($this->uri."column");
    	$this->join = new Resource ($this->uri."join");
    	$this->alias = new Resource ($this->uri."alias"); // jg
    	$this->pattern = new Resource ($this->uri."pattern");
    	$this->belongsToClassMap = new Resource ($this->uri."belongsToClassMap");
    	$this->refersToClassMap = new Resource ($this->uri."refersToClassMap");
    	$this->datatype = new Resource ($this->uri."datatype");
    	$this->lang = new Resource ($this->uri."lang");
    	$this->translateWith = new Resource ($this->uri."translateWith");
    	$this->valueMaxLength = new Resource ($this->uri."valueMaxLength");
    	$this->valueContains = new Resource ($this->uri."valueContains");
    	$this->valueRegex = new Resource ($this->uri."valueRegex");
    	$this->propertyBridge = new Resource ($this->uri."propertyBridge");
    	$this->property = new Resource ($this->uri."property");

    	// ClassMap and PropertyBridge
    	$this->condition = new Resource ($this->uri."condition");

    	// AdditionalProperty
    	$this->AdditionalProperty = new Resource ($this->uri."AdditionalProperty");
    	$this->additionalProperty = new Resource ($this->uri."additionalProperty");
    	$this->propertyName = new Resource ($this->uri."propertyName");
    	$this->propertyValue = new Resource ($this->uri."propertyValue");

    	// TranslationTable
    	$this->TranslationTable = new Resource ($this->uri."TranslationTable");
    	$this->href = new Resource ($this->uri."href");
    	$this->javaClass = new Resource ($this->uri."javaClass");
    	$this->translation = new Resource ($this->uri."translation");
    	$this->Translation = new Resource ($this->uri."Translation");
    	$this->databaseValue = new Resource ($this->uri."databaseValue");
    	$this->rdfValue = new Resource ($this->uri."rdfValue");
   
    }
}

 
?>