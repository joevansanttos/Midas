<?php
// ----------------------------------------------------------------------------------
// class: GenericMap.php
// ----------------------------------------------------------------------------------

/* offers an R2D2 read-only RAP model backed by a non-RDF database.
 * This class provides methods for quering an R2D2-Modell 
 *
 * History:
 * 07-06-2006                : 
 *
 * @author Christian Lehmann Lehmann.Christian@gmx.net
 * @version V0.1
 * @see de....R2D2Model
 * 
 * @package model
 * @access public
 */
 
include ( R2D2_INCLUDE_DIR   . "createMap/database.php");
include ( RDFAPI_INCLUDE_DIR . "vocabulary/RDF.php");

define ('NS','http://d2rq.org/');
define ('D2RQ','http://www.wiwiss.fu-berlin.de/suhl/bizer/D2RQ/0.1#');
define ('R2D2','http://bis.informatik.uni-leipzig.de/r2d2/0.1#');
define ('RDF','http://www.w3.org/1999/02/22-rdf-syntax-ns#');
define ('RDFS','http://www.w3.org/2000/01/rdf-schema#');
define ('XSD','http://www.w3.org/2001/XMLSchema#');
define ('OWL','http://www.w3.org/2002/07/owl#');
define ('DC','http://purl.org/dc/elements/1.1/');
define ('FOAF','http://xmlns.com/foaf/0.1/');
define ('SIOC','http://rdfs.org/sioc/ns#');
define ('ATOM','http://www.ietf.org/rfc/rfc4287.txt#');



define('NoKey',0);
define('PrimaryKey',1);
define('ForeignKey',2);
define('Property2PKClass',3);

define ('URIPATTERN','$$URIPATTERN$$');



class GenericMap{
 
	var $dbsys;
	var $host;
	var $db;
	var $user;
	var $password;
	var $outputfilename;


	var $database;



	function GenericMap($dbsys,$host,$db,$user,$password){
		$this->dbsys = $dbsys;
		$this->host = $host;
		$this->db = $db;
		$this->user = $user;
		$this->password = $password;
		
	}

	/**
 * main function CreateGenericMap. Takes database and output file informations
 * and generates a generic Map.
 *
 * @param string $dbsys   database system (db2, msql, mysql)
 * @param string $host    database host
 * @param string $db      database name
 * @param string $user
 * @param string $password
 * @param string $outputfilename
 * @return $string $filename
 * @access public
 */
	function CreateGenericMapAsFile($outputfilename=null){
		
		$this->outputfilename = $outputfilename;

		$db = new DBinfo($this->dbsys,$this->host,$this->db,$this->user,$this->password);

		$this->database = $this->readDBInformation($db);

		$date = date('h-i-s,j-m-y');
		// generate a standard name for output files
		if ($this->outputfilename === null)
		$filename = "genericMap-DB-".$this->database->dbName.'-'.$date.".n3";
		else $filename = $this->outputfilename;

		// generate the output file
		$filepnt = fopen($filename,"w");


		// Write file header
		fwrite($filepnt, "# ----------------------------------------------------------------------------------\n" );
		fwrite($filepnt, "# ---                         R2D2 GENERIC MAP GENERATOR                         ---\n" );
		fwrite($filepnt, "# generic Map ".$filename."\n#\n" );
		fwrite($filepnt, "# for database: ".$this->database->getHost()."\n" );
		fwrite($filepnt, "# created at: ".$date."\n" );
		fwrite($filepnt, "# ----------------------------------------------------------------------------------\n\n" );


		// storing all statements in the output file in n3 notation
		$this->storeMAPInformationAsFile($filepnt);

		return $filename;
	}


	/**
 * main function CreateGenericMap. Takes database and output file informations
 * and generates a generic Map.
 *
 * @param string $dbsys   database system (db2, msql, mysql)
 * @param string $host    database host
 * @param string $db      database name
 * @param string $user
 * @param string $password
 * @param string $outputfilename
 * @return $string $filename
 * @access public
 */
	function CreateGenericMapAsMemModel(){

		$db = new DBinfo($this->dbsys,$this->host,$this->db, $this->user,$this->password);

		$this->database = $this->readDBInformation($db);

		$memModel = new MemModel();

		// storing all statements in the output file in n3 notation
		$this->storeMAPInformationAsModel($memModel);

		return $memModel;
	}



	/**
 * Read all meta information (tables,columns, keys) of a database
 *
 * @param string $db
 * @return database
 * @access private
 */
	function readDBInformation($db){

		$db->DBConnection();

		$con = $db->getConnectionID();

		$tables = $db->readTables();

		// read all tables
		for (reset($tables); $curtable = current ($tables); next($tables)):
		if(!$this->isRAPtable($curtable)){
			$table = new DBTable($curtable);

			$primaryKey = $db->readPrimaryKeys($curtable);
			$foreignKey = $db->readForeignKeys($curtable);
			
			if( (!$primaryKey) && (!$foreignKey)){		
				$primaryKey = array();		
				$Columns = $db->readColumns($curtable); 
				reset($Columns);
				$firstColumn = current($Columns);
				array_push($primaryKey,$firstColumn->name);				
				Logging::warning("No primary key defined for table '".$curtable."'.   - 
				                 using first table column as URI: '".$firstColumn->name."'\n<p>");				
			}


			if($primaryKey){
				for (reset($primaryKey); $key = current ($primaryKey); next($primaryKey)):
				$table->addPrimaryKey($key);
				endfor;
			}

			if($foreignKey){
				foreach($foreignKey as $keyref => $fk){
					$table->addForeignKey($keyref,$fk);
				}
			}


			// search for Columns
			$Columns = $db->readColumns($curtable);

			if($Columns){
				for (reset($Columns); $col = current ($Columns); next($Columns)):
				$autoIncrement = $col->auto_increment; //bool
				$binary = $col->binary;   //bool
				$has_default = $col->has_default; //bool
				$length = $col->max_length;
				$name = $col->name;
				$notNull = $col->not_null; //bool
				$primaryKey = $col->primary_key; //bool
				$scale = $col->scale;
				$datatype = $col->type;

				$column = new DBColumn($name, $autoIncrement,$binary,	$has_default,
				$length, $notNull,$primaryKey,$scale,$datatype);

				$table->addColumn($column);


				endfor;
			}
			// add one Table to database information
			$db->addTable($table);
		}

		endfor;

		$db->DBClose();

		return $db;
	}

	/**
 * writes all statements into a map file
 *
 * @param FILE $filepnt
 * @access private
 */
	function storeMAPInformationAsFile(&$filepnt){


		$URI = $this->createURI();

		define('DB',$URI);

		fwrite($filepnt, "\n# -------------------------------------------------------------------------------------------\n" );
		fwrite($filepnt, "# Namespace declaration\n" );
		fwrite($filepnt, "# -------------------------------------------------------------------------------------------\n" );

		fwrite($filepnt, "@prefix : <" .NS."> .\n" );
		fwrite($filepnt, "@prefix db: <" .DB."> .\n" );
		fwrite($filepnt, "@prefix rdf: <" .RDF."> .\n" );
		fwrite($filepnt, "@prefix rdfs: <" .RDFS."> .\n\n" );
		fwrite($filepnt, "@prefix xsd: <" .OWL."> .\n\n" );
		
		fwrite($filepnt, "@prefix d2rq: <" .D2RQ."> .\n" );
		fwrite($filepnt, "@prefix r2d2: <" .R2D2."> .\n\n" );

		fwrite($filepnt, "@prefix dc: <" .DC."> .\n" );
		fwrite($filepnt, "@prefix xsd: <" .XSD."> .\n\n" );
		
		

		fwrite($filepnt, "@prefix foaf: <" .FOAF."> .\n" );
		fwrite($filepnt, "@prefix sioc: <" .SIOC."> .\n" );
		fwrite($filepnt, "@prefix atom: <" .ATOM."> .\n\n" );



		//set database information
		$this->buildDatabaseAsFile($filepnt);



		fwrite($filepnt, "\n\n# -------------------------------------------------------------------------------------------\n" );
		fwrite($filepnt, "# BEGIN GENERIC MAPPING OF ALL TABLES AND COLUMNS \n" );
		fwrite($filepnt, "# -------------------------------------------------------------------------------------------\n" );

		// store all tables as classes
		foreach($this->database->tables as $tablename => $tablecontent){
			$this->buildClassMapAsFile($tablename,$tablecontent, $filepnt);

			//store all property bridges of current class
			foreach($tablecontent->Columns as $columnname => $columncontent){
				$fkey = $pkey =  FALSE;
				if( in_array($columnname,$tablecontent->primaryKeys)){
					$pkey = TRUE;
				}
				if( in_array($columnname,$tablecontent->foreignKeys)){
					$fkey = TRUE;
				}


				if(($pkey) && ($fkey)) $keyFlag = Property2PKClass;
				else if($pkey) $keyFlag = PrimaryKey;
				else if($fkey) $keyFlag = ForeignKey;
				else $keyFlag = NoKey;

				$this->buildPropertyBridgeAsFile($tablename, $columnname,$columncontent,$keyFlag, $filepnt);
			}
		}



	}

	/**
 * Enter description here...
 *
 * @param File object $filepnt
 * @access private
 */
	function buildDatabaseAsFile(&$filepnt){
		//  store database information
		fwrite($filepnt, "\n# -------------------------------------------------------------------------------------------\n" );
		fwrite($filepnt, "# Database information\n" );
		fwrite($filepnt, "# -------------------------------------------------------------------------------------------\n" );

		fwrite($filepnt, ":database a d2rq:Database;\n" );

		if ( false !== stristr ($this->database->getDriver(), 'jdbc')){
			fwrite($filepnt, "   d2rq:jdbcDriver \"".$this->database->getDriver()."\";\n" );

			if ( false !== stristr ($this->database->getjdbcAdress(), 'jdbc')){
				fwrite($filepnt, "   d2rq:jdbcDSN \"".$this->database->getjdbcAdress()."\";\n" );
			}
			else
			echo "wrong database DSN. Use dqr1:jdbcDSN if using a jdbcDriver!<br>\n";
		}


		if($this->database->username !== ''){
			fwrite($filepnt, "   d2rq:username \"".$this->database->getUser()."\";\n" );
		}
		if($this->database->password !== ''){
			fwrite($filepnt, "   d2rq:password \"".$this->database->getPWD()."\";\n" );
		}

		$i_table=1;
		$tablenum = count($this->database->tables);
		foreach($this->database->tables as $tablename => $tablecontent){
			$i_column=1;
			foreach($tablecontent->Columns as $columnname => $columncontent){
				$i_columnnum = count($tablecontent->Columns);
				//echo $columnname.':       '. $columncontent->datatype."<br>\n";
				$datatype = $this->checkDatatype($columncontent->datatype);
				if($datatype==null){
					echo "warning: ignoring unknown datatype '".$columncontent->datatype."' of column '".$columnname."'.<br>\n";
					fwrite($filepnt, "      d2rq:textColumn \"".$tablename.".".$columnname."\"" );
				}
				else{
					fwrite($filepnt, "      d2rq:".$datatype." \"".$tablename.".".$columnname."\"");
				}

				if ( !($i_table == $tablenum) || !($i_column == $i_columnnum) ){
					fwrite($filepnt,";\n");
				}

				$i_column++;
			}
			$i_table++;
		}


		fwrite($filepnt, "\n      .\n\n" );


	}


	/**
	 * Enter description here...
	 *
	 * @param unknown_type $tablename
	 * @param unknown_type $tablecontent
	 * @param unknown_type $filepnt
	 * @access private
	 */
	function buildClassMapAsFile($tablename,$tablecontent, &$filepnt){
		// Create a Class Map
		$classMap = 'classMap_'.$tablename;

		fwrite($filepnt, "\n# -------------------------------------------------------------------------------------------\n" );
		fwrite($filepnt, "# Table ".$tablename."\n" );
		fwrite($filepnt, "# -------------------------------------------------------------------------------------------\n" );
		fwrite($filepnt, ":".$classMap." a d2rq:ClassMap;\n" );

		fwrite($filepnt, "   d2rq:dataStorage :database ;\n" );

		$pattern = DB.$tablename;
		for($i=0; $i<count($tablecontent->primaryKeys);$i++){
			if($i>0)
			$pattern.='-';
			$pattern .= '@@'.$tablename.'.'.$tablecontent->primaryKeys[$i].'@@';
		}

		fwrite($filepnt, "   d2rq:uriPattern \"".$pattern."\" ;\n" );
		fwrite($filepnt, "   d2rq:class db:".$tablename."\n" );

		fwrite($filepnt, "   .\n\n" );

	}


	/**
 * builds a property bridge
 *
 * @param string $tablename
 * @param string $columnname
 * @param object $columncontent
 * @param int $keyFlag
 * @param FILE $filepnt
 * @access private
 */
	function buildPropertyBridgeAsFile($tablename, $columnname,$columncontent,$keyFlag, &$filepnt){


		//check fk constraints
		$fk = $this->database->getForeignKeys($tablename);
		if ($fk){
			foreach($fk as $refTable => $key){
				foreach($key as $ind => $tmp){
					$pos = strpos($tmp,"=");
					$col = substr($tmp,0,$pos);
					$pos ++;
					$refCol = substr($tmp,$pos);

					if ($col === $columnname ){ // $column is fk
						$bridge = 'propertyBridge_'.$columnname.'_'.$col;

						fwrite($filepnt, ":".$bridge." a d2rq:ObjectPropertyBridge ;\n" );
						fwrite($filepnt, "   d2rq:belongsToClassMap :classMap_".$tablename." ;\n" );
						fwrite($filepnt, "   d2rq:refersToClassMap :classMap_".$refTable." ;\n" );
						fwrite($filepnt, "   d2rq:join \"".$tablename.".".$columnname." = ".$refTable.".".$refCol."\" ;\n" );
					}
					else{
						$bridge = 'propertyBridge_'.$tablename.'_'.$columnname;

						fwrite($filepnt, ":".$bridge." a d2rq:DatatypePropertyBridge ;\n" );
						fwrite($filepnt, "   d2rq:belongsToClassMap :classMap_".$tablename." ;\n" );
					}
				}

			}


		}
		else{
			$bridge = 'propertyBridge_'.$tablename.'_'.$columnname;

			fwrite($filepnt, ":".$bridge." a d2rq:DatatypePropertyBridge ;\n" );
			fwrite($filepnt, "   d2rq:belongsToClassMap :classMap_".$tablename." ;\n" );
		}

		$datatype = null;
		if ( false !== stristr ($columncontent->datatype, 'bigint'))
		$datatype = "int";
		if ( false !== stristr ($columncontent->datatype, 'long'))
		$datatype = "long";

		if($datatype){
			fwrite($filepnt, "   d2rq:datatype xsd:".$datatype." ;\n" );
		}


		fwrite($filepnt, "   d2rq:property db:".$tablename.'_'.$columnname." ;\n" );
		fwrite($filepnt, "   d2rq:column \"".$tablename.'.'.$columnname."\"\n" );




		/*if($columncontent->primaryKey){
		fwrite($filepnt, "   r2d2:primaryKey \"true\" ;\n" );
		}
		if($keyFlag == 2){
		fwrite($filepnt, "   r2d2:foreignKey \"true\" ;\n" );
		}*/

		fwrite($filepnt, "   .\n\n" );

	}




	/**
 * writes all statements into a memModel
 *
 * @param memModel $model
 * @access private
 */
	function storeMAPInformationAsModel(&$model){


		$URI = $this->createURI();

		define('DB',$URI);


		$model->removeNamespace('/localhost/');

		$model->addNamespace('',NS);
		$model->addNamespace('db',DB);


		//set database information
		$this->buildDatabaseAsMemModel($model);

		// store all tables as classes
		foreach($this->database->tables as $tablename => $tablecontent){
			$this->buildClassMapAsMemModel($tablename,$tablecontent, $model);

			//store all property bridges of current class
			foreach($tablecontent->Columns as $columnname => $columncontent){
				$fkey = $pkey =  FALSE;
				if( in_array($columnname,$tablecontent->primaryKeys)){
					$pkey = TRUE;
				}
				if( in_array($columnname,$tablecontent->foreignKeys)){
					$fkey = TRUE;
				}


				if(($pkey) && ($fkey)) $keyFlag = Property2PKClass;
				else if($pkey) $keyFlag = PrimaryKey;
				else if($fkey) $keyFlag = ForeignKey;
				else $keyFlag = NoKey;

				$this->buildPropertyBridgeAsMemModel($tablename, $columnname,$columncontent,$keyFlag, $model);
			}
		}



	}

	/**
 * Enter description here...
 *
 * @param File object $filepnt
 * @access private
 */
	function buildDatabaseAsMemModel( $memModel){

		$Node = new Statement(new Resource(NS.'database'),$GLOBALS['RDF_type'],new Resource(D2RQ.'Database'));
		$memModel->add($Node);

		if ( false !== stristr ($this->database->getDriver(), 'jdbc')){
			$Node = new Statement(new Resource(NS.'database'),new Resource(D2RQ.'jdbcDriver'),new Literal($this->database->getDriver()));
			$memModel->add($Node);

			if ( false !== stristr ($this->database->getjdbcAdress(), 'jdbc')){
				$Node = new Statement(new Resource(NS.'database'),new Resource(D2RQ.'jdbcDSN'),new Literal($this->database->getjdbcAdress()));
				$memModel->add($Node);
			}
			else
			echo "wrong database DSN. Use dqr1:jdbcDSN if using a jdbcDriver!<br>\n";
		}


		if($this->database->username !== ''){
			$Node = new Statement(new Resource(NS.'database'),new Resource(D2RQ.'username'),new Literal($this->database->getUser()));
			$memModel->add($Node);
		}
		if($this->database->password !== ''){
			$Node = new Statement(new Resource(NS.'database'),new Resource(D2RQ.'password'),new Literal($this->database->getPWD()));
			$memModel->add($Node);
		}

		$i_table=1;
		foreach($this->database->tables as $tablename => $tablecontent){
			foreach($tablecontent->Columns as $columnname => $columncontent){
				$datatype = $this->checkDatatype($columncontent->datatype);
				if($datatype==null){
					echo "warning: ignoring unknown datatype '".$columncontent->datatype."' of column '".$columnname."'.<br>\n";
					$Node = new Statement(new Resource(NS.'database'),new Resource(D2RQ.'textColumn'),new Literal($tablename.".".$columnname));
					$memModel->add($Node);
				}
				else{
					$Node = new Statement(new Resource(NS.'database'),new Resource(D2RQ.$datatype),new Literal($tablename.".".$columnname));
					$memModel->add($Node);

				}
			}
		}


	}




	/**
 * builds a class map
 *
 * @param string $tablename
 * @param object $tablecontent
 * @param memModel $model
 * @access private
 */
	function buildClassMapAsMemModel( $tablename,$tablecontent, &$memModel){
		// Create a Class Map
		$classMap = 'classMap_'.$tablename;

		$Node = new Statement(new Resource(NS.$classMap),$GLOBALS['RDF_type'],new Resource(D2RQ.'ClassMap'));
		$memModel->add($Node);

		$Node = new Statement(new Resource(NS.$classMap),new Resource(D2RQ.'dataStorage'),new Resource(NS.'database'));
		$memModel->add($Node);

		$pattern = DB.$tablename;
		for($i=0; $i<count($tablecontent->primaryKeys);$i++){
			if($i>0)
			$pattern.='-';
			$pattern .= '@@'.$tablename.'.'.$tablecontent->primaryKeys[$i].'@@';
		}

		$Node = new Statement(new Resource(NS.$classMap),new Resource(D2RQ.'uriPattern'),new Literal($pattern));
		$memModel->add($Node);
		$Node = new Statement(new Resource(NS.$classMap),new Resource(D2RQ.'class'),new Resource(DB.$tablename));
		$memModel->add($Node);

	}


	/**
 * builds a property bridge
 *
 * @param string $tablename
 * @param string $columnname
 * @param object $columncontent
 * @param int $keyFlag
 * @param memModel $memModel
 * @access private
 */
	function buildPropertyBridgeAsMemModel($tablename, $columnname,$columncontent,$keyFlag, &$memModel){


		//check fk constraints
		$fk = $this->database->getForeignKeys($tablename);
		if ($fk){
			foreach($fk as $refTable => $key){
				foreach($key as $ind => $tmp){
					$pos = strpos($tmp,"=");
					$col = substr($tmp,0,$pos);
					$pos ++;
					$refCol = substr($tmp,$pos);

					if ($col === $columnname ){ // $column is fk
						$bridge = 'propertyBridge_'.$columnname.'_'.$col;

						$Node = new Statement(new Resource(NS.$bridge),$GLOBALS['RDF_type'],new Resource(D2RQ.'ObjectPropertyBridge'));
						$memModel->add($Node);
						$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'belongsToClassMap'),new Resource('classMap_'.$tablename));
						$memModel->add($Node);
						$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'refersToClassMap'),new Resource('classMap_'.$refTable));
						$memModel->add($Node);
						$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'join'),new Literal($tablename.".".$columnname."=".$refTable.".".$refCol));
						$memModel->add($Node);

					}
					else{
						$bridge = 'propertyBridge_'.$tablename.'_'.$columnname;

						$Node = new Statement(new Resource(NS.$bridge),$GLOBALS['RDF_type'],new Resource(D2RQ.'DatatypePropertyBridge'));
						$memModel->add($Node);

						$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'belongsToClassMap'),new Resource('classMap_'.$tablename));
						$memModel->add($Node);

						$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'column'),new Literal($tablename.".".$columnname));
						$memModel->add($Node);

					}
				}

			}


		}
		else{
			$bridge = 'propertyBridge_'.$tablename.'_'.$columnname;

			$Node = new Statement(new Resource(NS.$bridge),$GLOBALS['RDF_type'],new Resource(D2RQ.'DatatypePropertyBridge'));
			$memModel->add($Node);

			$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'belongsToClassMap'),new Resource('classMap_'.$tablename));
			$memModel->add($Node);

			$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'column'),new Literal($tablename.".".$columnname));
			$memModel->add($Node);


		}

		$datatype = null;
		if ( false !== stristr ($columncontent->datatype, 'bigint'))
		$datatype = "int";
		if ( false !== stristr ($columncontent->datatype, 'long'))
		$datatype = "long";

		if($datatype){
			$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'datatype'),new Resource(XSD.$datatype));
			$memModel->add($Node);
		}


		$Node = new Statement(new Resource(NS.$bridge),new Resource(D2RQ.'property'),new Resource($this->database->Host.'#'.$tablename.'_'.$columnname));
		$memModel->add($Node);


		/*if($columncontent->primaryKey){
		fwrite($filepnt, "   r2d2:primaryKey \"true\" ;\n" );
		}
		if($keyFlag == 2){
		fwrite($filepnt, "   r2d2:foreignKey \"true\" ;\n" );
		}*/

		//fwrite($filepnt, "   .\n\n" );

	}





	/**
 * checks a column datatype and returns if it s numeric, text of date type
 *
 * @param string $name
 * @return unknown
 * @access private
 */
	function checkDatatype($name){

		if ( ($name === 'long') || ($name === 'bigint') || ($name ==='int')
		|| ($name ==='tinyint')|| ($name ==='smallint')|| ($name ==='float')
		|| ($name ==='double')|| ($name ==='decimal'))
		return "numericColumn";
		else if ( ($name === 'char') || ($name === 'varchar') ||
		($name === 'longtext') || ($name === 'tinytext') ||
		($name === 'text') || ($name === 'enum')||($name === 'mediumtext')
		|| ($name ==='tinyblob') || ($name ==='blob') || ($name ==='mediumblob')|| ($name ==='longblob')
		|| ($name ==='bool')|| ($name ==='binary')|| ($name ==='set')|| ($name ==='varbinary')
		|| (false != strstr($name,'enum')))
		return "textColumn";
		else if ( ($name === 'date') || ($name === 'datetime')|| ($name ==='timestamp')
		|| ($name ==='time')|| ($name ==='year') )
		return "dateColumn";

		else
		return null;


	}

	/**
 * creates an URI with the pattern:
 * tag:databasehost,date:user#
 *
 * @return string URI
 * @access public
 */
	function createURI(){

		$date = date('j-m-y');
		$user = $_ENV["USERNAME"];
		$uri = "tag:".$this->database->getHost();
		$uri .= "/".$this->database->getDBname();
		return $uri."/".$date."/".$user."#";
	}


	/**
 * checks if a table has the name of RAP table
 *
 * @param string $tablename
 * @access private
 * @return boolean
 */
	function isRAPtable($tablename){

		$raptablenames = array("datasets","dataset_model","models","namespaces","statements");

		if (in_array($tablename,$raptablenames))
		return true;
		else return false;
	}
}
?>