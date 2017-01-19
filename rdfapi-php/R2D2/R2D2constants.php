
<?php
// ----------------------------------------------------------------------------------
// R2D2 Constants
// ----------------------------------------------------------------------------------
// Version                   : 0.1
// Authors                   : 
//
// Description               : Constants and default configuration
// ----------------------------------------------------------------------------------
// History:

// ----------------------------------------------------------------------------------


// ----------------------------------------------------------------------------------
// General
// ----------------------------------------------------------------------------------


// ----------------------------------------------------------------------------------
// R2D2 Packages
// ----------------------------------------------------------------------------------

define('PACKAGE_FIND','find/FIND.php');
define('PACKAGE_MAP','map/MAP.php');
define('PACKAGE_TOOLS','tools/tools.php');

require_once (PACKAGE_TOOLS);
include(RDFAPI_INCLUDE_DIR . "vocabulary/RDF.php");


// NODE TYPE
define('URI',1);
define('LITERAL',2);
define('BNODE',3);
define('FIXEDNODE',4);


// VALUE SOURCE TYPE
define('DBSOURCE_COLUMN',1);
define('DBSOURCE_PATTERN',2);
define('DBSOURCE_BNODEID',3);
define('DBSOURCE_REGEX',4);

define("DB_noColumnType",0);
define("DB_numericColumnType",1);
define("DB_textColumnType",2);
define("DB_dateColumnType",3);



?>