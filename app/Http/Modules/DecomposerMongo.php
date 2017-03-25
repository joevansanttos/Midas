<?php

namespace App\Http\Modules;

class DecomposerMongo extends QueryDecomposerModule {

    /**
     * Função que identifica as palavras chave da linguagem de consulta e retorna as informações úteis
     * @param $query string
     * @return mixed array
     */



    public function decomposer($query)
    {
        // transforma em minusculas
        $sql = strtolower(trim($query));

        // pega a posição de cada palavra reservada da linguagem
        $indexes[] = strpos($sql, "db.");//from 0
        $indexes[] = strpos($sql, ".find");//where 1
        $indexes[] = strpos($sql, "sort");//order by 2
        $indexes[] = strpos($sql, "limit");//limit 3

        // guarda os campos, dataset, filtros, ordem e limite
        $jsonArray["fields"]    = $this->getFields($sql,$indexes[1] + 6 );//equivale ao select
        $jsonArray["dataset"]   = $this->getDataset($sql, $indexes[0]+3 ,$indexes[1]);//from  (ok)
        $jsonArray["filters"]   = $this->getFilters($sql,$indexes[1] + 6 );//where
        $jsonArray["order"]     = $indexes[3] === false ? false : $this->getOrder($sql, $indexes[2] + 6);//order by
        $jsonArray["limit"]     = $indexes[3] === false ? false : $this->getLimit($sql, $indexes[3] + 6);//limit

        return $jsonArray;
    }

    private function getDataset($sql, $begin, $indexes)
    {
        return trim(substr($sql, $begin, ($indexes - $begin)));
    }

    private function getFilters($sql, $begin )
    {
        $str_fields = trim(substr($sql, $begin  ));

        $indexes[] = strpos($str_fields, "}");

        return  str_ireplace ("'","", str_ireplace( "}","",
            str_ireplace(":", "=", trim(substr($str_fields,1,($indexes[0]))))));
    }

    private function getFields($sql, $begin)
    {
        $str_fields = str_ireplace (" ","",trim(substr($sql, $begin  ))) ;
        $indexes[] = strpos($str_fields, "},{");
        $indexes[] = strpos($str_fields, "})");
        $str_fields = trim(substr($str_fields,  $indexes[0] +3 ,($indexes[1] - $indexes[0]-3)));
        $ArraySelect = explode(",", str_ireplace("'", "", $str_fields));
        $i = 0 ;
        foreach ($ArraySelect as $value)
        {
            $indexes2[] = strpos($value, ":");
            $value =  trim(substr($value,0,$indexes2[$i])) ; //$value ;
            $ArraySelect[$i] = $value ;
            $i ++ ;
        }
        return  $ArraySelect ;
    }

    private function getOrder($sql, $begin)
    {
        $str_fields = trim(substr($sql, $begin ));
        $indexes[] = strpos($str_fields, ":");
        return     str_ireplace ("'","", trim(substr($str_fields, 0 ,$indexes[0]-1 )));
    }

    private function getLimit($sql, $begin)
    {
        $str_fields = trim(substr($sql, $begin  ));
        $indexes[] = strpos($str_fields, ")");
        return trim(substr($str_fields, 0 ,$indexes[0] ));
    }



}
