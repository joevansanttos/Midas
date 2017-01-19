<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * This is an demo of the RDF to Database Mapping Language R2D2.
 * It loads a map from a file $baseURI and find all triples.
 * Afterall the found triples are stored in a file as xml/rdf and n3 notation.	
 *
 * History:
 * 07-07-2006                : 
 *
 *
 * TO DO:
 * add the in a mapfile defined namespaces in the result file
 * up to this time RAP uses generated namespace prefixes in n1 .. nX syntax
 * this prefixes have to be replaces manually
 *
 * up to now the find method returns a string array
 * in next step a iterator-find method will be implemented
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */


// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");



// path and Filename of the map
//$baseURI="WP-d2rq.n3";
//$baseURI="ISWC-d2rq.n3";
$baseURI="wp-foaf-test.n3";



// creates a new R2D2 model which contains a data structure with 
// all mapping definitions taken from a map
$R2D2Model = new R2D2Model($baseURI);
// enable this to show debug information
$R2D2Model->enableDebug();



$result = array();

$sparqlQuery ="SELECT ?date WHERE { <http://wp_posts.org/user#user1> <http://purl.org/dc/elements/1.1/date> ?date . }";
$sparqlQuery ="SELECT ?y WHERE {<http://www.conference.org/conf02004/paper#Paper1> <http://annotation.semanticweb.org/iswc/iswc.daml#secondaryTopic> ?y .}";
$sparqlQuery = "SELECT ?resource ?value WHERE { ?resource <http://annotation.semanticweb.org/iswc/iswc.daml#date> ?value }";

$sparqlQuery = "SELECT ?property ?hasValue ?isValueOf
WHERE {
  { <http://www-uk.hpl.hp.com/people#andy_seaborne> ?property ?hasValue }
  UNION
  { ?isValueOf ?property <http://www-uk.hpl.hp.com/people#andy_seaborne> }
}
ORDER BY (!BOUND(?hasValue)) ?property ?hasValue ?isValueOf";



// -----------------------------------------------------------------------
// IT WORKS
// -----------------------------------------------------------------------
$sparqlQuery = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?name ?mail WHERE {
 ?x foaf:name ?name .
 ?x foaf:mbox ?mail .
} ORDER BY ?name";

$sparqlQuery = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?mail WHERE {
 ?x foaf:name \"Christian Lehmann\" .
 ?x foaf:mbox ?mail .
} ORDER BY ?name";

$sparqlQuery = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?s ?p ?o WHERE {
?x ?p ?o .  

} ORDER BY ?x";

$sparqlQuery = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?name ?s ?p ?o WHERE {
?s foaf:name ?name.
?x ?p ?o .  
} ORDER BY ?x";
// -----------------------------------------------------------------------



$sparqlQuery =
	"PREFIX foaf: <http://xmlns.com/foaf/0.1/>	
	SELECT ?source ?uri ?superclass 
	WHERE {  
	{ ?uri foaf:name ?name } UNION { ?uri foaf:mbox ?mail } 
	OPTIONAL { ?uri rdfs:subClassOf ?superclass } } } ";

$sparqlQuery =
	"PREFIX foaf: <http://xmlns.com/foaf/0.1/>	
	SELECT ?uri ?name ?mail
	WHERE { { ?uri foaf:name ?name. ?uri foaf:mbox ?mail} 
	  UNION { {?uri foaf:mbox ?mail.} UNION {?uri foaf:mbox ?mail}}} } ";

	
	
$sparqlQuery =
	"PREFIX foaf: <http://xmlns.com/foaf/0.1/>	
	SELECT ?uri ?name ?mail
	WHERE { { ?uri foaf:name ?name. ?uri foaf:mbox ?mail } UNION { ?uri foaf:mbox ?mail. }} } ";

$sparqlQuery="
 SELECT ?name ?mail WHERE
  { ?x foaf:name ?name. 
  FILTER (?name = \"Lehmann\").
  ?x foaf:mail ?mail.}";




//$result = $R2D2Model->find(null,null,null);


$sparqlQuery = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?name ?mail WHERE {
 ?x foaf:name ?name .
 ?x foaf:mbox ?mail .
} ORDER BY ?name";
echo $sparqlQuery."\n";

/*
print("<p>\nRAP SPARQL-Engine");
$start2 = setStart();  // start
$result = $R2D2Model->sparqlQuery($sparqlQuery);
$end2 = setEnd($start2); // end creating the model
SparqlEngine::writeQueryResultAsHtmlTable($result);
print("runtime old Engine: ".$end2." sec. <br>\n"); 
*/


print("<p>\nR2D2 SPARQL2SQL Rewriter");
$start1 = setStart();  // start
$result = $R2D2Model->sparqlQuery1($sparqlQuery);
$end1 = setEnd($start1); // end creating the model
r2d2_SparqlEngine::writeQueryResultAsHtmlTable($result);
print("runtime new Engine: ".$end1." sec. <br>\n"); 





/*
$dbstore = new DbStore('mysql','localhost','wordpress','root','root');
if ($dbstore->modelExists('foaftest')){		
    $dbmodel = $dbstore->getModel('foaftest');

    //$r = $dbmodel->find(null , new Resource('http://xmlns.com/foaf/0.1/name'),null);
    //$r->writeAsHtmlTable();
   
    $result = $dbmodel->sparqlQuery($sparqlQuery);
    SparqlEngine::writeQueryResultAsHtmlTable($result);     	
}
*/


/**
 * sets the starttime
 * @return string starttime
 */
function setStart(){
	
	$time=microtime();
	$tmpTime=explode(" ",$time);
	$time=$tmpTime[0]+$tmpTime[1];
	
	return $time;
}

/**
 * return the runtime from starttime to now
 *
 * @param string $starttime
 * @return string $runtime
 */
function setEnd($starttime){
	$endtime=microtime();
	$tmpTime=explode(" ",$endtime);
	$endtime=$tmpTime[0]+$tmpTime[1]; // Timestamp + Nanosec
	$time=$endtime-$starttime;
	$time=substr($time,0,8); // // 6 decimal places
	
	return $time;
}


?>
