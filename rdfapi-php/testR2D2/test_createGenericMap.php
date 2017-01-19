<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_createGenericMap.php
// ----------------------------------------------------------------------------------

/*
 * This test file creates a generic map from a database and stores it
 * as file and as memModel
 *
 * History:
 * 30-07-2006                : First version of this demo.
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");


 // include DBase Package
   require_once(RDFAPI_INCLUDE_DIR.PACKAGE_DBASE);
 
   $dbsystem  = "mySQL";
   $host      = "localhost";
   $dbname    = "dblp";
   $user      = "root";
   $password  = "root";
   
     
   $genericMap = new GenericMap($dbsystem,$host,$dbname,$user,$password);

   $filename = $genericMap->CreateGenericMapAsFile("wackowicki_map.n3");
   echo "file \"".$filename."\" created!\n";
     
   $memModel = $genericMap->CreateGenericMapAsMemModel();
   
   $memModel->writeAsHtmlTable();
   
   
                


?>