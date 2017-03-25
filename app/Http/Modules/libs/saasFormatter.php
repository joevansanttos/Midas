<?php


namespace App\Http\Modules\libs;

use App\Http\Modules\libs\jsonFormatter;
use App\Http\Modules\libs\xmlFormatter;
use App\Http\Modules\libs\csvFormatter;
use App\Http\Modules\libs\xmlConverter;
use App\Http\Modules\libs\filterFormatter;
use App\Http\Modules\libs\limitFormatter;

class saasFormatter {

  /**
   * Sub-Modulo responsavel por contruir o resultado quando nao ha joins
   * @param $daasResult
   * @param $queryDecomposed
   * @return saasResult
   */

  // funcao principal do sub-modulo que determina o resultado a partir do formato do daaas(json, xml, csv) 
  public function formatter($daasResult, $queryDecomposed) {
    if($daasResult[0] == "[" || $daasResult[0] == "{"){
      // chamada da funcao que controi o resultado em json
      $json_formatter =  new jsonFormatter();
      $saasResult = $json_formatter->formatter($daasResult, $queryDecomposed, NULL);
    }elseif ($daasResult[0] == "<"){
      // chamada da funcao que controi o resultado em xml
      $xml_formatter =  new xmlFormatter();
      $saasResult = $xml_formatter->formatter($daasResult, $queryDecomposed, NULL);
    }else{
      // chamada da funcao que controi o resultado em csv
      $csv_formatter =  new csvFormatter();
      $saasResult = $csv_formatter->formatter($daasResult , $queryDecomposed, NULL);
    }
    if(!empty($queryDecomposed['consulta1']['filters'])){
      $filter_formatter =  new filterFormatter();
      $saasResult = $filter_formatter->formatter($saasResult,$queryDecomposed['consulta1']['filters']); 
    }
    if(!empty($queryDecomposed['consulta1']['limit'])){
      $limit_formatter =  new limitFormatter();
      $saasResult = $limit_formatter->formatter($saasResult,$queryDecomposed['consulta1']['limit']);
    }

    return $saasResult;
  }


}