<?php

// ----------------------------------------------------------------------------------
// Class: logging.php
// ----------------------------------------------------------------------------------

/**
 * 
 * This class offers logging methods. They will be used to print error, warning or debug informations on
 * standard output
 * 
 * <BR><BR>History:<UL>
 * <LI>01-06-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package logging
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

class logging{
	
	
	var $debug = FALSE;
	
	
	
	// ------------------------------------------------------------------------------------------------------------
	
	/**
	 * Constructor of class logging
	 * if set debug to true, debug messages ars shown
	 * @param boolean $debug
	 * @return logging
	 */
	function logging($debug=FALSE){
		$this->debug = $debug;
	}
	/**
	 * returns actual debug mode
	 *
	 * @return boolean $debug
	 */
	function getDebug(){
		return $this->debug;
	}
	
	/**
	 * enable debug mode
	 *
	 * @access public
	 */
	function setDebug(){
		$this->debug = true;
	}
	
	/**
	 * disable debug mode
	 * 
	 * @access public
	 */
	function unsetDebug(){
		$this->debug = false;
	}
	
	/**
	 * prints a debug message if debug mode is set
	 *
	 * @param string $message
	 * @access public
	 */
	function debug($message){
		if($this->debug)
			echo $message." <br />\n";
	}
	
	/**
	 * prints a warning message
	 *
	 * @param string $message
	 * @access public
	 */
	
	function warning($message){
		echo "Warning: ".$message." <br />\n";
	}
	
	/**
	 * prints a error message and exits with error code
	 *
	 * @param string $message
	 * @access public
	 */
	
	function error($message){
		echo "<br>\nError: ".$message." <br><br>\n";
		echo "shut down... <br>";
		exit;
	}
	
	
	
}




























?>