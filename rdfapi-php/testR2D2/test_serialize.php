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
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Test generating SQL</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");

echo "entering R2D2 </br>";

// path and Filename of the map
//$baseURI="genericMap.n3";
//$baseURI="ISWC-d2rq.n3";
//$baseURI="WP-d2rq.n3";
$baseURI="wp-foaf-test.n3";
$baseURI="wordpress_map.n3";


// creates a new R2D2 model which contains a data structure with 
// all mapping definitions taken from a map
$R2D2Model = new R2D2Model($baseURI);
// enable this to show debug information
//$R2D2Model->enableDebug();

// read all ClassMaps
//$s = null;
//$p = $RDF_type;
//$o = null;

// read All
$s = null;
$p = null;
$o = null;


$result = array();

// the new find method - result is an array with all found statements
$result = $R2D2Model->find($s,$p,$o);

//generate a new RAP MemModel to output as RDF file
$memModel = new MemModel();


// adding new Namespaces used in Map
$memModel->addNamespace('foaf','http://xmlns.com/foaf/0.1/');
$memModel->addNamespace('sioc','http://rdfs.org/sioc/ns#');
$memModel->addNamespace('atom','http://www.ietf.org/rfc/rfc4287.txt#');

// save every single statement in the memModel
for (reset($result); $triple = current ($result); next($result)):	
	// add Statement to memModel
	$memModel->add($triple);
endfor;

$date = $today = date('h-i-s,j-m-y'); 

// generate a name for output files
$testfileN3 = "MappedRDF-".$baseURI."-".$date.".n3";
$testfileRDF = "MappedRDF-".$baseURI."-".$date.".rdf";

// saves the output file
$rdf = $memModel->saveAs($testfileN3,"n3");
$rdf = $memModel->saveAs($testfileRDF,"rdf");
			  
echo "all done .... exit"

?>
</body>
</html>
