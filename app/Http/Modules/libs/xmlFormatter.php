<?php


namespace App\Http\Modules\libs;


class xmlFormatter {

    /**
     * sub-modulo responsavel por receber o resultado em xml e tranforma-lo em array selecionando os seus campos escolhidos 
     * @param $daasResult
     * @param $consult
     * @param $joinArray
     * @return $xmlResult
     */

    // funcao principal do sub-modulo  
    public function formatter($daasResult, $consult, $joinArray) {
        $daasResult = simplexml_load_string($daasResult);
        if (empty($joinArray)) {
          $fields = $consult['consulta1']['fields'];
        }else{
          $fields = $consult['fields'];
          $joins = $this->getJoins($joinArray,$consult['dataset']);
          $fields = $this->addJoinsToFields($joins,$fields);
        } 
        $xmlResult = $this->getXmlResult($fields,$daasResult);
        return $xmlResult;                     
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
    

    // funcao que recebe dados em xml e seleciona os dados por atributos
    private function getXmlResult($fields,$daasResult) {
      foreach ($fields as $key => $field) {
          $field = trim($field);
          $elements = $daasResult->xpath('////'.$field);
          $j = 0;          
          foreach ($elements as $element) {            
            $value = (string)$element;
            $xmlResult[$j][$field] = $value;
            $j++;            
          }         
          
      }
      return $xmlResult;
    }


}

