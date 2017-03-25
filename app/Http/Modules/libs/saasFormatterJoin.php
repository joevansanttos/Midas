<?php


namespace App\Http\Modules\libs;


use App\Http\Modules\libs\jsonFormatter;
use App\Http\Modules\libs\xmlFormatter;
use App\Http\Modules\libs\csvFormatter;
use App\Http\Modules\libs\joinFormatter;
use App\Http\Modules\libs\filterFormatterJoin;
use App\Http\Modules\libs\selectFormatterJoin;
use App\Http\Modules\libs\limitFormatter;
use App\Http\Modules\libs\orderFormatter;


class saasFormatterJoin {

    /**
     * Sub-Modulo responsavel por contruir o resultado quando ha joins
     * @param $daasResult
     * @param $queryDecomposed
     * @return saasResult
    */

    // funcao principal do sub-modulo que determina o resultado a partir do formato do daaas(json, xml, csv) 
    public function formatter($daasResult, $queryDecomposed){
      $filters = [];
      $limit;
      $consults = $queryDecomposed;
      unset($consults['join']);
      foreach ($consults as $consult) {
        $operator = $daasResult[$consult['dataset']][0];
        if($operator == "[" || $operator == "{"){
          // chamada da funcao que controi o resultado em json
          $json_formatter =  new jsonFormatter();
          $saasResult[$consult['dataset']] = $json_formatter->formatter($daasResult[$consult['dataset']], $consult, $queryDecomposed['join']);
        }else if ($operator == "<"){
          // chamada da funcao que controi o resultado em xml
          $xml_formatter =  new xmlFormatter();
          $saasResult[$consult['dataset']] = $xml_formatter->formatter($daasResult[$consult['dataset']], $consult, $queryDecomposed['join']);
        }else{
          // chamada da funcao que controi o resultado em json
          $csv_formatter =  new csvFormatter();
          $saasResult[$consult['dataset']] = $csv_formatter->formatter($daasResult[$consult['dataset']], $consult, $queryDecomposed['join']);                   
        }
        // se o array filtros na consulta nao estiver vazio o filtro e adicionado ao array filtros     
        if(!empty($consult['filters'])){
          $filters = $consult['filters'];
          $dataset = $consult['dataset'];
        }
        // se o array limit na consulta nao estiver vazio o limit e adicionado a var limit
        if(!empty($consult['limit'])){
          $limit = $consult['filters'];
        }
      }
      // chamada da funcao que realiza o join entre os daas         
      $join_formatter =  new joinFormatter();
      $saasResult = $join_formatter->formatter($saasResult,$queryDecomposed['join'], $consults);
      // se ha filtros e chamada a funcao que realiza a filtragem
      if(!empty($filters)){
        $filter_formatter =  new filterFormatterJoin();
        $saasResult = $filter_formatter->formatter($saasResult, $filters, $dataset); 
      }
      $select_formatter =  new selectFormatterJoin();
      $saasResult = $select_formatter->formatter($saasResult,$consults);
      // se ha um valor na variavel limit e chamada a funcao que limita o resultado
      if(!empty($limit)){
        $limit_formatter =  new limitFormatter();
        $saasResult = $limit_formatter->formatter($saasResult,$limit); 
      }

      $order_formatter =  new orderFormatter();
      $order = $order_formatter->formatter($saasResult,'facility_name');
      return $saasResult;      
  }

}