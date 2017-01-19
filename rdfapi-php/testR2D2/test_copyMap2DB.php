<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * This is an demo of the RDF to Database Mapping Language R2D2.
 * It loads a map from RAP-database and makes a copy as file
 *
 * History:
 * 07-06-2006                : creating Modell
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */
?>


<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");



define('DB_HOST','localhost');
define('DB_NAME','wordpress');
define('DB_USER','root');
define('DB_PASSWORD','root');

define('MODEL','WP_MAP_V2-0-4-v1');


	$dbsystem  = "mysql";

	// Set the DB information for database to store the map

	$rdf_database = new DbStore($dbsystem, DB_HOST, DB_NAME, DB_USER, DB_PASSWORD);
	
		
	if ($rdf_database->modelExists(MODEL)){
		 $model = $rdf_database->getModel(MODEL);
	

    	// get all triples from DbModel and save as memModel
    	$newmodel = $model->getMemModel();
    	
    	$newmodel->saveAs('wordpress_map.rdf');

    	$newmodel->addNamespace('d2rq','http://www.wiwiss.fu-berlin.de/suhl/bizer/D2RQ/0.1#');
    }
    	$rdf_database->close(); 
	


?>