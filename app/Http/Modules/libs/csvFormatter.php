<?php


namespace App\Http\Modules\libs;


class csvFormatter {
    
    /**
     * sub-modulo responsavel por receber o resultado em csv e tranforma-lo em array selecionando os seus campos escolhidos 
     * @param $daasResult
     * @param $consult
     * @param $joinArray
     * @return $csvResult
     */

    // funcao principal do sub-modulo  
    public function formatter($daasResult, $consult, $joinArray) {
       if (empty($joinArray)) {
         $fields = $consult['consulta1']['fields'];
       }else{
         $fields = $consult["fields"];
         $joins = $this->getJoins($joinArray,$consult['dataset']);
         $fields = $this->addJoinsToFields($joins,$fields);
       } 
       $daasResult = $this->csvToArray($daasResult);                        
       $keys = $this->csvGetKeys($fields,$daasResult[0]); 
       $csvResult = $this->getCsvResult($daasResult,$keys,$fields);
       return $csvResult;
    }

    // funcao que seleciona os atributos do join referentes a um dataset
    private function getJoins($value,$dataset){
      $joins = [];
      if($value['dataset1'][1] == $dataset){
        $joins[] = $value['dataset1'][0];
      }if($value['dataset2'][1] == $dataset){
        $joins[] = $value['dataset2'][0];
      }
      return $joins;
    }   

    // funcao que adiciona os filtros aos campos de selecao do dataset
    private function addJoinsToFields($joins,$fields) {
      foreach ($joins as $key => $value) {
        array_push($fields,$value);
      }
      return $fields;
    }

    // funcao que recebe os dados em csv e retorna em array
    private function csvToArray($daas_result) {      
      $lines = str_getcsv($daas_result,"\n");
      $array = array();
      foreach ($lines as $line) {
        $array[] = str_getcsv($line,",");
      }
      return $array;      
    }  

    // funcao que pecorre os dados em array e encontra as chaves dos @fields retorna um vetor
    private function csvGetKeys($fields,$daas_result) {
        $i = 0;
        $vetor_keys = [];        
        foreach ($fields as $key_fields => $value_fields) {
          foreach ($daas_result as $key_daas => $value_daas) {
              $str = strtolower($value_daas);              
              if($value_fields == $str){
                $vetor_keys[$i] = $key_daas;
                $i++;
              }              
          }
        }        
        return $vetor_keys;
    }

    // funcao que encontra os campos selecionados utilizando os ids
    private function getCsvResult($daasResult,$ids,$fields) {
      $i = 0;        
      unset($daasResult[0][0]);
      foreach ($daasResult as $daas) {       
        $j = 0;
        foreach ($ids as $id) {
          foreach ($daas as $key => $value) {            
            if($id == $key){             
              $field = $fields[$j];
              $csvResult[$i][$field] = $value;
              $j++;
            }
          }
        }        
        $i++;
      }              
      return $csvResult;
    }



}


 