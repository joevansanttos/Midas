<?php

namespace App\Http\Modules\libs;

class xmlConverter {
	
	/**
	 * Função responsavel por pegar o resultado em xml e tranforma-lo em array
	 * @param $daasResult
	 * @param $consult
	 * @param $joinArray
	 * @return $csvResult
	 */

	public function formatter($data){	

		// creating object of SimpleXMLElement
		$palavra = '<?xml version="1.0"?><data></data>';
		$xml = simplexml_load_string($palavra);
		$xml = $this->to_xml($xml, $data);
		print_r($xml);		
	}

	function to_xml($object, $data){
	    foreach ($data as $key => $value) {
	    	print_r($value);
	        if (is_array($value)) {
	            $new_object = $object->addChild($key);

	            $this->to_xml($new_object, $value);
	        } else {   
	            $object->addChild($key, $value);
	        }   
	    }
	    return $new_object;   
	}   
	
}