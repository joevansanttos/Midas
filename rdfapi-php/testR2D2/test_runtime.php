<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * performance Tests
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


$baseURI="wordpress_map.n3";

$r2d2ModelName = 'WP_MAP_V2-0-4-v1';

$modelName="WORDPRESS2";
$model = "WORDPRESS2";

$s = null;
$p = null;
$o = null;

$s = null;
$p = new Resource('http://www.w3.org/2005/Atom#title');
$o = null;




$s = null;
$p = new Resource('http://www.w3.org/2005/Atom#title');
$o = new Literal('comment-42');

$s = null;
$p = null;
$o = new Literal('2003-11-17 12:19:09');

$s = new Resource('tag:localhost/wordpress/27-10-06/docelm#user1');
$p = null;
$o = null;

$s = null;
$p = new Resource('http://www.w3.org/2005/Atom#title');
$o = null;

$sparqlQuery = "SELECT DISTINCT ?p ?o WHERE {<tag:localhost/wordpress/27-10-06/docelm#user1> ?p ?o }";

$result = array();


// -----------------------------------------------------------------------
// FIND TEST
// -----------------------------------------------------------------------
print("\nFIND TEST\n");

// --------------------------------------------------------------------------------
// and now testing the RAP DB-Store


// create new DBModel from found triples and store it in RAP 


	// Connect to MySQL database with user defined connection settings
	$rdf_database = new DbStore("mysql","localhost", "wordpress" , "root", "root" );
	// check if map model exists
	/*if (!$rdf_database->modelExists($model)){
		// Create a map file for wordpress database schema
		
		$memModel = new MemModel();
		for (reset($result); $triple = current ($result); next($result)):	
			// add Statement to memModel
			$memModel->add($triple);
		endfor;
		if ($memModel)
		$rdf_database->putModel($memModel,$model);

	}*/

/*
$start = setStart();

$dbModel = $rdf_database->getModel($model);

$parsetime = setEnd($start);

$findstart = setStart();

$result = $dbModel->find($s,$p,$o);

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("<p>runtime RAP for getting the model: $parsetime sec.<br>\n"); 
print("runtime RAP(find all triples): $findtime sec. <br>\n"); 
print("runtime RAP(get model + find): $alltime sec. <br>\n"); 
print("triples found through RAP: ".count($result->triples)."<br>\n\n"); 
*/

/*
// -----------------------------------------------------------------------
// Testing find method from file map
// -----------------------------------------------------------------------
$start = setStart();  // start

$R2D2Model = new R2D2Model($baseURI);  // creating new virtual model
//$R2D2Model->enableDebug();

$parsetime = setEnd($start); // end creating the model

$findstart =  setStart();   // starting the find operation

$result = $R2D2Model->find($s,$p,$o);  //search all triples

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("<p>\nruntime R2D2 for reading a Map from file: $parsetime sec.<br>\n"); 
print("runtime R2D2(find all triples): $findtime sec. <br>\n"); 
print("runtime R2D2(create model + find): $alltime sec. <br>\n"); 
print("triples found through R2D2: ".count($result)."<br>\n\n"); 
*/







// --------------------------------------------------------------------------------
/*
$start = setStart();
	// check if map model exists
	if (!$rdf_database->modelExists($modelName)){
		// save map file as RAP model in RAP Datastore
		$memModel = new MemModel();
		// Load and parse map file
		$memModel->load($baseURI);
		if ($memModel)
		$rdf_database->putModel($memModel,$modelName);
	}
$time = setEnd($start);
//print("runtime R2D2(copy file map to db): $time sec. <br>\n\n"); 
	*/

// -----------------------------------------------------------------------
// Testing find method from db map
// -----------------------------------------------------------------------

/*
$db = new DBinfo("mysql","localhost", "rap", "root","root");
$start = setStart();

$R2D2DBModel = new R2D2DbModel($r2d2ModelName,$db);

$parsetime = setEnd($start);

$findtime = setStart();

$result = $R2D2DBModel->find($s,$p,$o);

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findtime);  // time for find operation

print("<p>runtime R2D2DB for reading a Map from db: $parsetime sec.<br>\n"); 
print("runtime R2D2DB(find all triples): $findtime sec. <br>\n"); 
print("runtime R2D2DB(get model + find): $alltime sec. <br>\n"); 
print("triples found through R2D2DB: ".count($result)."<br>\n\n"); 

*/








// -----------------------------------------------------------------------
// SPARQL TEST
// -----------------------------------------------------------------------
print ("\nSPARQL TEST\n");

print ("SPARQLquery: ".$sparqlQuery."\n");


// -----------------------------------------------------------------------
// Testing sparq method for RAP STORE
// -----------------------------------------------------------------------


$start = setStart();

##  Load a DbModel 
## -----------------
$dbModel = $rdf_database->getModel($model);

$parsetime = setEnd($start);

$findstart = setStart();

$result = $dbModel->sparqlQuery($sparqlQuery);

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("<p>SPARQL:");
//print("runtime RAP for getting the model: $parsetime sec.<br>\n"); 
//print("runtime RAP(find all triples): $findtime sec. <br>\n"); 
print("runtime RAP(get model + find): $alltime sec. <br>\n"); 
print("triples found through RAP: ".count($result)."<br>\n\n"); 


// -----------------------------------------------------------------------
// Testing sparq method from file map
// -----------------------------------------------------------------------
$start = setStart();  // start

$R2D2Model = new R2D2Model($baseURI);  // creating new virtual model

$parsetime = setEnd($start); // end creating the model

$findstart =  setStart();   // starting the find operation

$result = $R2D2Model->sparqlQuery1($sparqlQuery);  //search all triples

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("<p>SPARQL:");
print("runtime R2D2 for reading a Map from file: $parsetime sec.<br>\n"); 
print("runtime R2D2(find all triples): $findtime sec. <br>\n"); 
print("runtime R2D2(create model + find): $alltime sec. <br>\n"); 
print("triples found through R2D2: ".count($result)."<br>\n\n"); 




// -----------------------------------------------------------------------
// Testing sparql method from db map
// -----------------------------------------------------------------------


$start = setStart();

$R2D2DBModel = new R2D2DbModel($modelName,$db);

$parsetime = setEnd($start);

$findstart = setStart();

$result = $R2D2DBModel->sparqlQuery1($sparqlQuery);

$alltime = setEnd($start);  // whole runtime
$findtime = setEnd($findstart);  // time for find operation

print("<p>SPARQL:");
print("runtime R2D2 for reading a Map from db: $parsetime sec.<br>\n"); 
print("runtime R2D2(find all triples): $findtime sec. <br>\n"); 
print("runtime R2D2DB(get model + find): $alltime sec. <br>\n"); 
print("triples found through R2D2DB: ".count($result)."<br>\n\n"); 


$rdf_database->close();





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
