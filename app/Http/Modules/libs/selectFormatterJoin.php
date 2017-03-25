<?php


namespace App\Http\Modules\libs;


class selectFormatterJoin {

  /**
   * sub-modulo responsavel para fazer a selecao quando existir um join
   * @param $daasResult
   * @param $consults
   * @return $selectResult
   */

    // funcao principal do sub-modulo
    public function formatter($daasResult,$consults) {
      $fields = $this->getFields($consults);
      $selectResult = $this->getSelectResult($daasResult,$fields);
      return $selectResult; 
    } 

    // encontra todos os campos juntamente com seu respectivo banco
    private function getFields($consults){
      foreach ($consults as  $value) {
        $i = 0;
        foreach ($value['fields'] as  $field) {
          $fields[$value['dataset']][$i] = trim($field);
          $i++;
        }
      }
      return $fields;
    }


    // funcao que seleciona os campos formados a partir de um join
    private function getSelectResult($daas_result,$fields) {
      $i = 0;
      $saas_result =[];
      foreach ($daas_result as  $daas) {
        $j = 0;
        foreach ($fields as $chave => $field) {
          if(array_key_exists($chave, $daas)){
            foreach ($field as $value) {
              if(array_key_exists($value,$daas[$chave])){
                $saas_result[$i][$value] = $daas[$chave][$value];
                $j++;
              }
            }
          }
        }
        $i++;
      }
      return $saas_result;
    }    

    
} 