<?php

namespace App\Http\Modules;

use App\Http\Modules\DecomposerMysql ;

class QueryDecomposerModule extends Module {

    /**
     * Função que identifica as palavras chave da linguagem de consulta e retorna as informações úteis
     * @param $query string
     * @return mixed array
     */
    public function decomposer($query)
    {
        $TipoBd = trim(substr($query, 6,3 ));
        echo $TipoBd;
        if($TipoBd === 'db.' )
        {

            $query_decomposer = new DecomposerMongo();
            $jsonArray2 = $query_decomposer-> decomposer($query);
            //$jsonArray2 =    self::decomposerMongoDB($query) ;
        }

        elseif ($TipoBd === 'SEL')
        {
            $query_decomposer = new DecomposerMysql();
            $jsonArray2 = $query_decomposer-> decomposer($query);

        }
        else
        {
            //print_r( "Banco não identificado");
            exit();
        }
        return $jsonArray2;
        exit();
        
    }



}
