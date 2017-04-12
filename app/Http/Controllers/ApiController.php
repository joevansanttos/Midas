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
	        $apiModel = new ApiModel;
	        $resApiModel = $apiModel->get_data($all);
	        print_r($resApiModel);
	    } else {
	    	echo 'ERROR 400! Dataset NOT FOUND!';
	    }
    }
}
