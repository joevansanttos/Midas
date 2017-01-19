<?
// ----------------------------------------------------------------------------------
// Class: CSVParser
// ----------------------------------------------------------------------------------

/**
 * Parses the contents of a CSV file into a Map. The CVS
 * file must contain exactly two columns. Keys come from the
 * first, values from the second.
 * 
 * <BR><BR>History:<UL>
 * <LI>01-06-2006                : First version of this class.</LI>
 * 
 * 
 * 
 * @version  V0.1
 * @author Christian Lehmann <Lehmann.Christian@gmx.net>
 * 
 * @package map
 * @todo nothing
 * @access	public
 * 
 * 
 * 
 * 
 */

class CSVParser {
	
	var $URL;
	var $reader;
	
	
//	public
	function CSVParser($url) {
		
		$this->reader = fopen($url,"r");
		$this->URL = $url;
	}
	
	//public
	function parseURI() {
		
			$result = array();
			
			
			while (!feof($this->reader)){
				$line = fgets($this->reader,1000);
				
				$pos = strpos($line,',');
				if ($pos !== FALSE){
					$ind = substr($line,0,$pos);
					$value = substr($line,$pos);
				}
				else if (FALSE !== strpos($line,';')){
					$pos = strpos($line,';');
					$ind = substr($line,0,$pos);
					$value = substr($line,$pos);
				}
				else if (FALSE !== strpos($line,':')){
					$pos = strpos($line,':');
					$ind = substr($line,0,$pos);
					$value = substr($line,$pos);
				}
				
				$result[$ind] = $value;
				
			}
			return result;		
	}
}
?>