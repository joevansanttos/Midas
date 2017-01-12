<?php
/**
 * Created by GT-Cloud.
 * User: maires
 * Date: 04/12/16
 * Time: 02:13
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\DaaSModel;
use App\Http\Modules\QueryDecomposerModule;
use App\Http\Modules\QueryBuilderModule;
use App\Http\Modules\ResultFormatterModule;

class MainController extends Controller
{

    public function index() {
        return view('main');
    }

    /**
     * Função que recebe a query e chama as demais funções
     * @param Request $request
     */
    public function daas(Request $request)
    {

        // query capturada da requisição do SaaS
        $query = urldecode($request->input('query'));

        // chamada da função de decomposição da query
        $query_decomposed = new QueryDecomposerModule();
        $query_decomposed = $query_decomposed->decomposer($query);


        // chamada da função que consulta ao banco para pegar as informações do DaaS informado na query
        $daas_model = new DaaSModel;
        $api_params = $daas_model->get_provider_api($query_decomposed["dataset"]);

        // chamada da função que contrói a url para a API
        $query_builder = new QueryBuilderModule();
        $daas_request_url = $query_builder->builder($api_params[0], $query_decomposed);


        // requisita ao DaaS as informações
        $daas_result = file_get_contents($daas_request_url, false);


        // formata o resultado para ser compatível com o SaaS
        //$result = $this->result_formatter(json_decode(utf8_encode($daas_result)), $query_decomposed, $api_params[0]);
        $result_formatter = new ResultFormatterModule();
        $result = $result_formatter->formatter($daas_result, $query_decomposed, $api_params[0]);

        // envia para a view o resultado formatado
        return view('daas')->with(compact('result'));

    }

}
