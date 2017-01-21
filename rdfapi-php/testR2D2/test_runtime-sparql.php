<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * Testing the performance of R2D2
 *
 * History:
 * 07-06-2006                : creating Modell
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
	<title>Testing performance between R2D2 and RAP internal database</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");

echo "entering R2D2 </br>";

//$baseURI="WP-d2rq.n3";
$baseURI="genericMap.n3";
$modelName="WPGENERIC.n3";



$sparqlQuery = "SELECT DISTINCT ?class ?property ?res WHERE { ?class ?property ?res }";
//$sparqlQuery = "SELECT DISTINCT ?class WHERE { [] a ?class } ORDER BY ?class";
$result = array();


// -----------------------------------------------------------------------
// SPARQL TEST
// -----------------------------------------------------------------------
print ("\nSPARQL TEST\n");

print ("SPARQLquery: ".$sparqlQuery."\n");

// -----------------------------------------------------------------------
// Testing sparq method from file map
// -----------------------------------------------------------------------
$start = setStart();  // start

$R2D2Model = new R2D2Model($baseURI);  // creating new virtual model

$parsetime = setEnd($start); // end creating the model

$findstart =  setStart();   // starting the find operation

$result = $R2D2Model->sparqlQuery($sparqlQuery);  //search all triples

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("SPARQL:");
print("runtime R2D2 for reading a Map from file: $parsetime sec.<br>\n"); 
print("runtime R2D2(find all triples): $findtime sec. <br>\n"); 
print("runtime R2D2(create model + find): $alltime sec. <br>\n"); 
print("triples found through R2D2: ".count($result)."<br>\n\n"); 


// -----------------------------------------------------------------------
// Testing sparq method for RAP STORE
// -----------------------------------------------------------------------


$start = setStart();

$rdf_database = new DbStore();


##  Load a DbModel 
## -----------------
$dbModel = $rdf_database->getModel($model);

$parsetime = setEnd($start);

$findstart = setStart();

$result = $dbModel->sparqlQuery($sparqlQuery);

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("SPARQL:");
print("runtime RAP for getting the model: $parsetime sec.<br>\n"); 
print("runtime RAP(find all triples): $findtime sec. <br>\n"); 
print("runtime RAP(get model + find): $alltime sec. <br>\n"); 
print("triples found through RAP: ".count($result)."<br>\n\n"); 


// -----------------------------------------------------------------------
// Testing sparql method from db map
// -----------------------------------------------------------------------


$start = setStart();

$R2D2DBModel = new R2D2DbModel($modelURI);

$parsetime = setEnd($start);

$findtime = setStart();

$result = $R2D2DBModel->sparqlQuery($sparqlQuery);

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("SPARQL:");
print("runtime R2D2 for reading a Map from db: $parsetime sec.<br>\n"); 
print("runtime R2D2(find all triples): $findtime sec. <br>\n"); 
print("runtime R2D2(get model + find): $alltime sec. <br>\n"); 
print("triples found through R2D2: ".count($result)."<br>\n\n"); 









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
</body>
</html>
