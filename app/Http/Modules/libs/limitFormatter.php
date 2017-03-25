<?php


namespace App\Http\Modules\libs;


class limitFormatter {

  /**
   * Função responsavel para fazer a filtragem entre o resultado
   * @param $daasResult
   * @param $limit
   * @return $limitResult
   */
  public function formatter($daasResult,$limit) {
    $limitResult = [];
    $i = 0;
    foreach ($daasResult as  $value) {
      $limitResult[$i] = $value;
      $i++;    
      if($i == $limit){
        return $limitResult;
      }
    }
    return $limitResult;
  }
}
