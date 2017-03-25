<?php


namespace App\Http\Modules\libs;


class filterFormatter {

  /**
   * sub-modulo responsavel para fazer a filtragem entre o resultado
   * @param $daasResult
   * @param $queryDecomposed
   * @return $filterResult
   */

    // funcao principal do sub-modulo
    public function formatter($daasResult,$filters) {
      $filters = $this->filtersToArray($filters);
      $filterResult = $this->getFilterResult($daasResult,$filters);
      return $filterResult; 
    } 

    //funcao que filtra os resultados de um join a partir de um where
    private function getFilterResult($daas_result,$filters) {
      $i = 0;
      $filterResult = [];      
      $field1 = trim($filters[0]);
      $field2 = trim($filters[2]);
      if($filters[1] == '>'){
        foreach ($daas_result as $daas) {
          if($daas[$field1] > $field2){
            $filterResult[] = $daas;
          }
        }          
      }elseif ($filters[1] == '=') {
        foreach ($daas_result as $daas) {
          if($daas[$field1] == $field2){
            $filterResult[] = $daas;
          }
        } 
      }
      return $filterResult;
    }


   private function filtersToArray($filter){
     $pos = strpos($filter, '=');     
     if($pos == true){
      $pieces = explode("=", $filter);
      $string = trim($pieces[1]);
      $string = substr($string, 1);
      $string = substr($string, 0, -1);
      unset($pieces[1]);
      trim($string);
      $pieces[1] = '=';
      $pieces[2] = trim($string);
     }
     $pos = strpos($filter, '>');
     if($pos == true){
      $pieces = explode(">", $filter);
      $string = trim($pieces[1]);
      $string = substr($string, 1);
      $string = substr($string, 0, -1);
      unset($pieces[1]);
      trim($string);
      $pieces[1] = '>';
      $pieces[2] = trim($string);
     }
     return $pieces;  

   }  

    
} 