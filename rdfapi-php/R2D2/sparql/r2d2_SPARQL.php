<?php
// ----------------------------------------------------------------------------------
// SPARQL
// ----------------------------------------------------------------------------------
//
// Description               : sprql package
// ----------------------------------------------------------------------------------

define("R2D2_SPARQL_DIR", R2D2_INCLUDE_DIR ."sparql/");

require_once(R2D2_SPARQL_DIR . "r2d2_SparqlEngine.php");
require_once(R2D2_SPARQL_DIR . "r2d2_TripleGenerator.php");
require_once(R2D2_SPARQL_DIR . "r2d2_TripleNode.php");
require_once(R2D2_SPARQL_DIR . "r2d2_SQLGenerator.php");
require_once(R2D2_SPARQL_DIR . "r2d2_TripleResult.php");
require_once(R2D2_SPARQL_DIR . "r2d2_Combiner.php");
require_once(R2D2_SPARQL_DIR . "r2d2_Constraint.php");
require_once(R2D2_SPARQL_DIR . "r2d2_GraphPattern.php");
require_once(R2D2_SPARQL_DIR . "r2d2_Query.php");
require_once(R2D2_SPARQL_DIR . "r2d2_QueryTriple.php");
require_once(R2D2_SPARQL_DIR . "r2d2_SparqlParser.php");
require_once(R2D2_SPARQL_DIR . "r2d2_SparqlParserException.php");

?>
