<?php


namespace App\Http\Modules\libs;

use App\Http\Modules\libs\JsonStore;


class jsonFormatter {

   /**
    * sub-modulo responsavel por receber o resultado em json e tranforma-lo em array selecionando os seus campos escolhidos 
    * @param $daasResult
    * @param $consult
    * @param $joinArray
    * @return $jsonResult
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
      $jsonResult = $this->getJsonResult($fields,$daasResult);
      return $jsonResult;
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


    // funcao que recebe dados em json e seleciona os dados por atributos
    private function getJsonResult($fields,$daasResult) {
      $i = 0;
      // chamada da funcao que tranforma o json em array
      $store = new jsonStore($daasResult);
      foreach ($fields as  $field) {
        $field = trim($field);
        $key = '$..'.($field);
        // elementos sao pegos a partir do seu nome
        $elements = $store->get($key);
        $j = 0;          
        foreach ($elements as $element) {            
          $value = (string)$element;
          $value = trim($value);
          $saas_result[$j][$field] = $value;
          $j++;            
        }         
      }
      return $saas_result;
    }
    
}