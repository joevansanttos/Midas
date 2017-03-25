<?php


namespace App\Http\Modules\libs;


class joinFormatter {

  /**
   * Função responsavel para fazer o join entre os daas escolhidos
   * @param $daasResult
   * @param $joins
   * @return $joinResult
   */

  public function formatter($daasResult, $joinArray) { 
    $joinResult = [];
    $i = 0;
    $joins[] = $joinArray;
    foreach ($joins as  $join) {
      $daas1 = $join['dataset1'][1];
      $field1 = $join['dataset1'][0];      
      $daas2 = $join['dataset2'][1];
      $field2 = $join['dataset2'][0];      
      $joinType = $join['tipo'];
      if($joinType == 'inner join'){
        if($i == 0){
          $joinResult = $this->firstInnerJoin($field1,$field2,$daasResult[$daas1],$daasResult[$daas2],$daas1,$daas2,$joinType);            
        }else{
          $joinResult = $this->secondInnerJoin($joinResult,$daasResult[$daas2],$daas1,$daas2,$field1,$field2,$joinType);
      }
      }elseif($joinType == 'left join'){
        if ($i == 0){
          $joinResult = $this->firstLeftJoin($field1,$field2,$daasResult[$daas1],$daasResult[$daas2],$daas1,$daas2,$joinType);            
        }else{            
          $joinResult = $this->secondLeftJoin($joinResult,$daasResult[$daas2],$daas1,$daas2,$field1,$field2,$joinType);
        }    
      }elseif ($joinType == 'right join') {            
        if ($i == 0){
          $joinResult = $this->firstRightJoin($field1,$field2,$daasResult[$daas1],$daasResult[$daas2],$daas1,$daas2,$joinType);      
        }else{
          $joinResult = $this->secondRightJoin($joinResult,$daasResult[$daas2],$daas1,$daas2,$field1,$field2,$joinType);
        }     
      }elseif($joinType == 'left excluding'){
        if ($i == 0){
          $joinResult = $this->firstLeftExcludingJoin($field1,$field2,$daasResult[$daas1],$daasResult[$daas2],$daas1,$daas2,$joinType);            
        }else{            
          $joinResult = $this->secondLeftExcludingJoin($joinResult,$daasResult[$daas2],$daas1,$daas2,$field1,$field2,$joinType);
        }  
      }
      $i++;                       
    }
      return $joinResult;    
  }
  

  //funcao que faz o primeiro inner join entre dois daas
  private function firstInnerJoin($field1,$field2,$daas1_result,$daas2_result,$daas1,$daas2,$join_type){
    $i = 0;                
    $join_result = [];                                   
    foreach ($daas1_result as $value1) { 
      $j = 0;                                                    
      foreach ($daas2_result as $value2){
        if (array_key_exists($field2, $value2)){
          if(array_key_exists($field1, $value1)){
            if ($value1[$field1] == $value2[$field2]){                                                                  
              $join_result[$i][$daas1] = $value1;
              $join_result[$i][$daas2] = $value2;
              $i++;
              $j++;                                         
            }    
          }
                  
        }
      }            
      if($j == 0){
        $join_result[$i][$daas1] = $value1;                
        $i++;
      }  
                
      
    }
    return $join_result;
  }

  //funcao que faz o segundo inner join entre um resultado de um join e um daas 
  private function secondInnerJoin($join_result,$daas2_result,$daas1,$daas2,$field1,$field2,$join_type) {
    $i = 0;
    $new_join_result = [];                       
    foreach ($daas2_result as  $value2) {
      $j = 0;
      foreach ($join_result as  $value1) {
        if(array_key_exists($daas1, $value1)){
          if (array_key_exists($field1,$value1[$daas1])) {                
            if(array_key_exists($field2, $value2)){                  
              if($value1[$daas1][$field1] == $value2[$field2]){
                $new_join_result[$i] = $value1;
                $new_join_result[$i][$daas2] = $value2;                                                          
                $i++;
                $j++;                                      
              }
            }
          }
        }     
       }
    }
    return $new_join_result;          
  }        


  //funcao que faz o primeiro left join entre dois daas
  private function firstLeftJoin($field1,$field2,$daas1_result,$daas2_result,$daas1,$daas2,$join_type){
    $i = 0;        
    $join_result = [];                                   
    foreach ($daas1_result as $value1) { 
      $j = 0;                                                    
      foreach ($daas2_result as $value2){
        if (array_key_exists($field2, $value2)){
          if ($value1[$field1] == $value2[$field2]){                                                                  
            $join_result[$i][$daas1] = $value1;
            $join_result[$i][$daas2] = $value2;
            $i++;
            $j++;                                         
          }                    
        }
      }            
      
      if($j == 0){
        $join_result[$i][$daas1] = $value1;                
        $i++;
      }  
                 
      
    }
    return $join_result;
  }

  //funcao que faz o segundo left join entre um resultado de um join e um daas
  private function secondLeftJoin($join_result,$daas2_result,$daas1,$daas2,$field1,$field2,$join_type) {
    $i = 0;
    $new_join_result = [];
    foreach ($join_result as $value1) {
      $j = 0;
      foreach ($daas2_result as $value2) {
        if (array_key_exists($field2, $value2)){
          if(array_key_exists($field1, $value1[$daas1])){
            if($value1[$daas1][$field1] == $value2[$field2]){                        
              $new_join_result[$i] = $value1;
              $new_join_result[$i][$daas2] = $value2;                                                   
              $i++;
              $j++;
            }   
          }                             
         }               
      }
       
      if($j == 0){
        $new_join_result[$i] = $value1;
        $i++;
      }
             
      
    }
    return $new_join_result;
  }


  //funcao que faz o primeiro right join entre dois daas
  private function firstRightJoin($field1,$field2,$daas1_result,$daas2_result,$daas1,$daas2,$join_type){
    $i = 0;                
    $join_result = [];                                   
    foreach ($daas2_result as $value2) { 
      $j = 0;                                                    
      foreach ($daas1_result as $value1){
        if (array_key_exists($field2, $value2)){
          if(array_key_exists($field1, $value1)){
            if ($value1[$field1] == $value2[$field2]){                                                                  
              $join_result[$i][$daas1] = $value1;
              $join_result[$i][$daas2] = $value2;
              $i++;
              $j++;                                         
            }     
          }
                 
        }
      }            
      
      if($j == 0){
        $join_result[$i][$daas2] = $value2;                
        $i++;
      }                               
      
    }
    return $join_result;
  }


  //funcao que faz o segundo right join entre um resultado de um join e um daas
  private function secondRightJoin($join_result,$daas2_result,$daas1,$daas2,$field1,$field2,$join_type) {
    $i = 0;
    $new_join_result = [];                       
    foreach ($daas2_result as  $value2) {
      $j = 0;
      foreach ($join_result as  $value1) {
        if(array_key_exists($daas1, $value1)){
          if (array_key_exists($field1,$value1[$daas1])) {                
            if(array_key_exists($field2, $value2)){                  
              if($value1[$daas1][$field1] == $value2[$field2]){
                $new_join_result[$i] = $value1;
                $new_join_result[$i][$daas2] = $value2;                                                          
                $i++;
                $j++;                                      
              }
            }
          }
        }     
       }
       if($j == 0){
         $new_join_result[$i][$daas2] = $value2;
         $i++;
       }   
    }
    return $new_join_result;          
  }    

  //funcao que faz a operacao left excluding join entre dois daas
  private function firstLeftExcludingJoin($field1,$field2,$daas1_result,$daas2_result,$daas1,$daas2,$join_type){
    $i = 0;        
    $join_result = [];                                   
    foreach ($daas1_result as $value1) { 
      $j = 0;                                                    
      foreach ($daas2_result as $value2){
        if (array_key_exists($field2, $value2)){
          if ($value1[$field1] == $value2[$field2]){                                     
            $j++;                                         
          }                    
        }
      }            
      if($j == 0){
        $join_result[$i][$daas1] = $value1;                
        $i++;
      }  
                 
      
    }
    return $join_result;
  }
  

  //funcao que faz o segundo left excluding join  entre um resultado de um join e um daas
  private function secondLeftExcludingJoin($join_result,$daas2_result,$daas1,$daas2,$field1,$field2,$join_type) {
    $i = 0;
    $new_join_result = [];
    foreach ($join_result as $value1) {
      $j = 0;
      foreach ($daas2_result as $value2) {
        if (array_key_exists($field2, $value2)){
          if(array_key_exists($field1, $value1[$daas1])){
            if($value1[$daas1][$field1] == $value2[$field2]){                                                                        
              $j++;
            }   
          }                             
         }               
      }
       
      if($j == 0){
        $new_join_result[$i] = $value1;
        $i++;
      }
             
      
    }
    return $new_join_result;
  }
  
}