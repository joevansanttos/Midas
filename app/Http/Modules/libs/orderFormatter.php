<?php


namespace App\Http\Modules\libs;


class orderFormatter {

  /**
   * Função responsavel para fazer a filtragem entre o resultado
   * @param $orderResult
   * @param $var
   * @return $orderResult
   */

    public function formatter($orderResult, $field){

      foreach ($orderResult as $key => $row) {
        $aux[$key] = $row[$field];
      }
      array_multisort($aux, SORT_ASC, $orderResult);
      return $orderResult;
    }
 } 