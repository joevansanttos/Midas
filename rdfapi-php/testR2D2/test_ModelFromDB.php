<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * This is an demo of the RDF to Database Mapping Language R2D2.
 * It loads a map from a database and find all triples.
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
	<title>Test storing R2D2Model in database </title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");


// path and Filename of the map
//$baseURI="WP-d2rq.n3";
//$baseURI="genericMap.n3";
//$baseURI="ISWC-d2rq.n3";
//$modelName ='ISWC';

$baseURI="genericMap.n3";
$modelName="WPGENERIC.n3";


$baseURI="MappedRDF.n3";
$modelName="foaf-test";


// Set the DB information
$dbDriver  = "mysql";
$host = "localhost";
$db ="wordpress";
$user = "root";
$password = "root";
$db = new DBinfo($dbDriver,$host,$db,$user,$password);




// uncomment this to store map model 'modelName' from URI 'baseURI'
R2D2Model::StoreMapAsDbModel($baseURI,$modelName,$db);

//Startzeit
$time1=microtime();
$tmpTime=explode(" ",$time1);
$time1=$tmpTime[0]+$tmpTime[1];


// creates a new R2D2 model which contains a data structure with 
// all mapping definitions taken from a map
$R2D2DbModel = new R2D2DbModel($modelName, $db);

$result = $R2D2DbModel->find(null,null,null);
/*$sparqlQuery = "SELECT DISTINCT ?class WHERE { [] a ?class } ORDER BY ?class";
$result = $R2D2DbModel->sparqlQuery($sparqlQuery);
SparqlEngine::writeQueryResultAsHtmlTable($result);
*/

$time2=microtime();
$tmpTime=explode(" ",$time2);
$time2=$tmpTime[0]+$tmpTime[1]; // Timestamp + Nanosec
$time=$time2-$time1; // 
$time=substr($time,0,8); // 6 decimal places
print("runtime R2D2 for reading a Map: $time sec.<br>\n"); 




?>
</body>
</html>
