<?php


namespace App\Http\Modules\libs;


class filterFormatterJoin {

  /**
   * sub-modulo responsavel para fazer a filtragem entre o resultado
   * @param $daasResult
   * @param $queryDecomposed
   * @return $filterResult
   */

    // funcao principal do sub-modulo
    public function formatter($daasResult,$filters, $dataset) {
      $filters = $this->filtersToArray($filters);
      array_push($filters,$dataset);
      $filterResult = $this->getFilterResult($daasResult,$filters,$dataset);
      return $filterResult; 
    } 


    //funcao que filtra os resultados de um join a partir de um where
    private function getFilterResult($daasResult,$filters,$dataset) {
      $i = 0;
      $filterResult = [];
      $field1 = trim($filters[0]);
      $field2 = trim($filters[2]);
      $dataset = trim($filters[3]);
      if($filters[1] == '='){
        foreach ($daasResult as $daas) {
          if(array_key_exists($dataset,$daas)){
            if(array_key_exists($field1, $daas[$dataset])){
              if($daas[$dataset][$field1] == $field2){
                $filterResult[$i] = $daas;
                $i++;
              }
            }
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