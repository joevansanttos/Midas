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
        if(array_key_exists('join', $query_decomposed)){
          if(!empty($query_decomposed['join'])){
            // chamada da função que consulta ao dis para pegar as informações do DaaS informado na query
            $daas_model = new DaaSModel;
            // chamada da função que contrói a url para a API 
            $query_builder = new QueryBuilderModule();
            $consults = $query_decomposed;
            unset($consults['join']);
            foreach ($consults as $key => $value) {
               $api_params = $daas_model->get_provider_api($value['dataset']);
               $daas_request_url = $query_builder->builder($api_params, $value); 
               $daas_result[$value['dataset']] = file_get_contents($daas_request_url, false);
            }
          }else{
            $daas_model = new DaaSModel;
            $api_params = $daas_model->get_provider_api($query_decomposed['consulta1']['dataset']);
            $query_builder = new QueryBuilderModule();
            $daas_request_url = $query_builder->builder($api_params, $query_decomposed['consulta1']);
            $daas_result = file_get_contents($daas_request_url, false);
          }
        }else{
          $daas_model = new DaaSModel;
          $api_params = $daas_model->get_provider_api($query_decomposed['dataset']);

          $query_builder = new QueryBuilderModule();
          $daas_request_url = $query_builder->builder($api_params, $query_decomposed);
          $daas_result = file_get_contents($daas_request_url, false);
          $query_decomposed['filters'] = "city = 'New York'";
          $query_decomposed['consulta1'] = $query_decomposed;
        }
        
        // formata o resultado para ser compatível com o SaaS
        $result_formatter = new ResultFormatterModule();
        $result = $result_formatter->formatter($daas_result, $query_decomposed);

        // envia para a view o resultado formatado
        return view('daas')->with(compact('result'));

    }

    /**
     * Função da API que disponibilizará banco de dados como DaaS
     * @param Request $request dados informados da api
     * @return mixed retorna o conjunto de dados como o cliente solicitou
     **/
    public function api(Request $request) {
        // dados coletados para a api
        $dataset = urldecode($request->input('dataset'));
        $fields = urldecode($request->input('fields'));
        $sort = urldecode($request->input('sort'));
        $limit = urldecode($request->input('limit'));
        $format = urldecode($request->input('format'));
        $refine = urldecode($request->input('refine'));
        $exclude = urldecode($request->input('exclude'));

        print_r($exclude);

    }

}