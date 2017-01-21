// ----------------------------------------------------------------------------------
//        R2D2 - mapping between relational data and RDFR based ontologies
// ----------------------------------------------------------------------------------

/*
 * This is the first beta version of the RDF to Database Mapping Language R2D2.
 * R2D2 v0.1 is a mapping language which present an virtual, read-only graph in RAP to query a relational
 * non-RDF database in RDF.
 *
 * History:
 * 07-10-2006                : first version (beta)
 *
 * To Do: *support for d2rq:translationTable, d2rq:valueRegEx, d2rq:valueContains,d2rq:valueMaxLength
 *        *queries with SPARQL
 *        *automatic generation of a generic map which represents the formal structure of a database
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 */

Architecture:
- Map: R2D2 uses the D2RQ Mapping Language(http://sites.wiwiss.fu-berlin.de/suhl/bizer/d2rq/index.htm) 
       to describe the mapping relations between a relational database schema and an ontology.
- Mapper: Through the information stored in the Map, R2D2 generated a virtual read-only graph which is integrated in RAP and rewrites a query from
          the RAP API into SQL and returns the mapped RDF triples

Installation:
You have to copy the "R2D2" directory into the RAP main folder (this folder contains folders like
api, netapi, doc, tools).
To use the functionalities of R2D2 some test files are stored in testR2D2.

How to use?

To set up a find statement, first you have do define the RAP and R2D2 include operations:
define("RDFAPI_INCLUDE_DIR", "./../api/");
include(RDFAPI_INCLUDE_DIR . "RdfAPI.php");
define("R2D2_INCLUDE_DIR", "./../R2D2/");
include(R2D2_INCLUDE_DIR . "R2D2Model.php");

Secondly you have to create a R2D2 model with a baseUri which contains the location
of your map file. This works in the same way like creating a memModel:
$R2D2Model = new R2D2Model($baseUri);

Now you are able to search the model through the find(spo) method:
$result = $R2D2Model->find($s,$p,$o);

To set up more complex queries, you are able to use SPARQL.
To set up a query you have to use the sparql method:
$result = $R2D2Model->sparql($query);

To print the result set as HTML table, use:
SparqlEngine::writeQueryResultAsHtmlTable($result);

If you want to see, how R2D2 transform your queries to SQL, you can activate debug mode:
$R2D2Model->enableDebug();


R2D2 Example: (stored in testR2D2)
	
Example Mapping:
	WP-d2rq.n3	
Example Database:	
	Wordpress-DB-Dump.sql
Result of a find(spo) query against the database: MappedRDF-WP-d2rq.n3 / MappedRDF-WP-d2rq.rdf

