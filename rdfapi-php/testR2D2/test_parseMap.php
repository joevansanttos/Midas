<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_DB_MEM_CreateR2D2Modell.php
// ----------------------------------------------------------------------------------

/*
 * This is an demo of the RDF to Database Mapping Language R2D2.
 * It loads a map from a file $baseURI and parses all map informations
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
	<title>Test Store Models in Database</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");



## 2. Store a memory model in database.
## ------------------------------------

// Load an RDF-Documtent into a memory model

// Filename of an RDF document
$baseURI="WP-d2rq.n3";

//$baseURI="WP-FOAF-test.n3";
$R2D2Model = new R2D2Model($baseURI);
// enable this to show debug information
$R2D2Model->enableDebug();




?>
</body>
</html>
