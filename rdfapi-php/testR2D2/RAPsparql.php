<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * RAP demo: testing sparql queries on a RDF-model generated with R2D2
 *
 * History:
 * 07-07-2006                : 
 *
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>SPARQL queries within R2D2</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");


// path and Filename of the RDF-file
$baseURI="MappedRDF.n3";



// creates a new R2D2 model which contains a data structure with 
// all mapping definitions taken from a map
$model = new MemModel();
// enable this to show debug information
$model->load($baseURI);

$result = array();

 $sparqlQuery = '
PREFIX rdf <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
SELECT ?fullName
WHERE { ?x rdf:type ?fullName }';
 
$sparqlQuery = "
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
SELECT DISTINCT ?name ?mail WHERE {
 ?x foaf:name ?name .
 ?x foaf:mbox ?mail .
} ORDER BY ?name";

 
$result = $model->sparqlQuery($sparqlQuery);
SparqlEngine::writeQueryResultAsHtmlTable($result);
/*
 foreach($result as $line){
  $value = $line['?fullName'];
    if($value != "")
      echo $value->toString()."<br>";
    else
      echo "undbound<br>";
}

*/
			  
echo "all done .... exit"

?>
