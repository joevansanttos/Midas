<?php

namespace App\Http\Modules;

class QueryBuilderModule extends Module {

    /**
     * Função responsavel por contruir a url para consultar o DaaS
     * @param $api_params array
     * @param $values array
     * @return string
     */
    public function builder($api_params, $values)
    {
        // dominio da api do DaaS
        $daas_request = $api_params->domain;
        // URI da api, quando houver
        $daas_request .= $api_params->search_path;

        // identificação do dataset, quando é exigido
        if (!empty($api_params->dataset_param)) {
            $daas_request .= "?" . $api_params->dataset_param . "=" . $values["dataset"];
        } else {
            $daas_request .= $values["dataset"];
        }

        // caso tenha filtros, acrescenta como parametros da url
        if (!empty($api_params->query_param)) {
            if (strpos($daas_request, "?") !== false) {
                $daas_request .= "&" . $api_params->query_param . "=" . $values["filters"];
            } else {
                $daas_request .= "?" . $api_params->query_param . "=" . $values["filters"];
            }
        }
        
        // caso tenha ordem, acrescenta como parametros da url
        if (!empty($api_params->sort_param)) {
            if(array_key_exists('order', $values)){
                if (strpos($daas_request, "?") !== false) {
                    $daas_request .= "&" . $api_params->sort_param . "=" . $values["order"];
                } else {
                    $daas_request .= "?" . $api_params->sort_param . "=" . $values["order"];
                }
            }
            
        }

        // caso tenha limite, acrescenta como parametros da url
        if (!empty($api_params->limit_param)) {
            if(array_key_exists('limit', $values)){
                if (strpos($daas_request, "?") !== false) {
                    $daas_request .= "&" . $api_params->limit_param . "=" . $values["limit"];
                } else {
                    $daas_request .= "?" . $api_params->limit_param . "=" . $values["limit"];
                }
            }  
        }
        
        if(!empty($api_params->format_param)){
            $daas_request .= $api_params->format_param;

        }
        return $daas_request;
    }

}