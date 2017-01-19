<?php
// ----------------------------------------------------------------------------------
// PHP Script: test_find.php
// ----------------------------------------------------------------------------------

/*
 * Test file for using the new find method in R2D2.
 * It loads a map from file $base URI and creates a R2D2Model.
 * After all some find operations are set up und the result
 * set will be printed
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
	<title>Test generating SQL</title>
</head>
<body>

<?php

// Include RAP
define("RDFAPI_INCLUDE_DIR", "./../api/");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");





// ------------------------------------------------------------------
// Filename of an RDF document

//$baseURI="WP-d2rq.n3";

$baseURI="wordpress_map.n3";
//$baseURI="genericMap.n3";
$baseURI="ISWC-d2rq.n3";


$model = new R2D2Model($baseURI);


// enable this to show debug information
//$model->enableDebug();

// -----------------------------------------------------------------------

$NS = "http://annotation.semanticweb.org/iswc/iswc.daml#";

$s=		new Resource('http://www.conference.org/conf02004/paper#Paper2');
$p=		new Resource($NS.'year');
$o=		new Literal('2002');

$s =	null;
$p =  	new Resource($NS . "title");
$o = 	new Literal ("Titel of the Paper: Trusting Information Sources One Citizen at a Time", "en");

$s=		new Resource('http://www.conference.org/conf02004/paper#Paper2');
$p=		new Resource( $NS.'year');
$o=		new Literal('2002');



$s = new Resource("http://www.conference.org/conf02004/paper#Paper1");
$p = $RDF_type;
$o = new Resource($NS . "InProceedings");



$s = new Resource ("http://trellis.semanticweb.org/expect/web/semanticweb/iswc02_trellis.pdf#Varun Ratnakar");
$p = new Resource ($NS . "author_of");
$o = new Resource ("http://www.conference.org/conf02004/paper#Paper1");


$s = new Resource("http://www.conference.org/conf02004/paper#Paper1");
$p = $RDF_type;
$o = new Resource($NS . "InProceedings");

$s = null;
$p = $RDF_type;
$o = null;



$ns= "http://annotation.semanticweb.org/iswc/iswc.daml#";

$s = null;
$p = null;
$o = new Resource ("http://www.conference.org/conf02004/paper#Paper1");

$s = null;
$p = null;
$o = null;

$s = new Resource("http://www.conference.org/conf02004/paper#Paper1");
$p = $RDF_type;
$o = new Resource($NS . "InProceedings");


$s = new Resource("http://www-uk.hpl.hp.com/people#andy_seaborne");
$p = null;
$o = null;
// --------------------------------------------------------------------------------

$result = array();
$result = $model->find($s,$p,$o);



$i=1;
if ($result != null){
for (reset($result); $triple = current ($result); next($result)):
	$s = $triple->getSubject();
	$p = $triple->getPredicate();
	$o = $triple->getObject();
	
	// check if a node is resource, literal or blanknode and returns the label
	$s = tools::checkNodeType($s);
	$p = tools::checkNodeType($p);
	$o = tools::checkNodeType($o);
	
	echo "<br> ----------------------------------------------------<br>";
	echo "Triple ".$i."<br> <br>";
	
	echo "Subject:   ".$s."<br>";
	echo "Predicate: ".$p."<br>";
	echo "Object:    ".$o."<br>";
	echo "=======================================================<br>";

	
	$i++;
endfor;
}
print("triples found through R2D2: ".count($result)."<br>\n\n"); 
?>
</body>
</html>
