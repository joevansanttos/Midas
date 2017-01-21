<?php
// ----------------------------------------------------------------------------------
// class: R2D2Model.php
// ----------------------------------------------------------------------------------

/* offers an R2D2 read-only RAP model backed by a non-RDF database.
 * This class provides methods for quering an R2D2-Modell 
 *
 * History:
 * 07-07-2006                : 
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 * @version V0.1
 * @see de....R2D2Model
 * 
 * @package R2D2
 * @access public
 */
 
 class R2D2DbModel extends R2D2Model {	
 	
	
	 // ------------------------------------------------------------------------------------------------------------
     
     /** 
	 * Create a non-RDF database-based model from a Map configuration stored in the RAP database. 
	 * @param string $modelURI  name of the map model in the database
	 * @param DBinfo object
	 * @access public
	 */     
	function R2D2DbModel($modelURI,$db) {
		
		$this->baseURI = $modelURI;
		
		$this->log = new logging();
		$rdf_database = new DbStore($db->getDBSystem(),$db->getHost(),$db->getDBname(), $db->getUser(),$db->getPWD());

		if ($rdf_database->modelExists($modelURI))
			$R2D2DbModel = $rdf_database->getModel($modelURI);
		else
    		$this->log->error("Model with URI: '$modelURI' doesn´t exists in Database. Please create a model before using it.");

    	// get all triples from DbModel and save as memModel
    	$R2D2Model = $R2D2DbModel->getMemModel();
    	$rdf_database->close(); 
		// store a Map into internal data structures to be accessible
		$this->MapInit($R2D2Model);
		
	}
	

 }

	
?>