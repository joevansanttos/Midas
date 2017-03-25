<?php

namespace App\Http\Modules;

use App\Http\Modules\libs\saasFormatter;
use App\Http\Modules\libs\saasFormatterJoin;


class ResultFormatterModule extends Module {

    /**
     * Modulo responsavel por contruir o resultado que sera enviado ao SaaS 
     * @param $daasResult
     * @param $queryDecomposed
     * @return saasResult
     */

    public function formatter($daasResult, $queryDecomposed){
      if(count($daasResult) == 1){
        $saas_formatter = new saasFormatter();
        $saasResult = $saas_formatter->formatter($daasResult,$queryDecomposed);
      }else{
        $saas_formatter = new saasFormatterJoin();        
        $saasResult = $saas_formatter->formatter($daasResult,$queryDecomposed);
      }
      $saasResult = json_encode($saasResult);
      return $saasResult; 
    }

}


