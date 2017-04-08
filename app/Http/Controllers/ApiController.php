<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\ApiModel;

class ApiController extends Controller {

	/**
	 * Função que fornece a api dos bancos de dados
	 * @param Request $request variavel que recebe os parametros da api
	 * @return mixed retorna os dados no formato solicitado
	 **/
    public function main(Request $request) {
    	// dados coletados para a api
    	$all = $request->all();

    	if (!empty($all['dataset'])) {
    		// identifica os dados
	        $dataset = (!empty($all['dataset'])) ? $all['dataset'] : null;
	        unset($all['dataset']);
	        $fields = (!empty($all['fields'])) ? $all['fields'] : null;
	        unset($all['fields']);
	        $order = (!empty($all['order'])) ? $all['order'] : null;
	        unset($all['order']);
	        $limit = (!empty($all['limit'])) ? $all['limit'] : null;
	        unset($all['limit']);
	        $format = (!empty($all['format'])) ? $all['format'] : null;
	        unset($all['format']);

	        // identifica as restrinções
	        $refine = []; $exclude = [];
	        foreach ($all AS $key => $value) {
	        	if (substr($key, 0, 6) == 'refine') {
	        		$refine[substr($key, 7)][] = $value;
	        	} else if (substr($key, 0, 7) == 'exclude') {
					$exclude[substr($key, 8)][] = $value;
	        	}
	        }

	        $apiModel = new ApiModel;
	        $resApiModel = $apiModel->get_data($dataset, $fields, $order, $limit, $format, $refine, $exclude);
	        print_r($resApiModel);
	    } else {
	    	echo 'ERROR 400! Dataset NOT FOUND!';
	    }
    }
}
