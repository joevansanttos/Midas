<?php

namespace App\Http\Modules;

class DecomposerMongo extends QueryDecomposerModule {

    /**
     * Função que identifica as palavras chave da linguagem de consulta e retorna as informações úteis
     * @param $query string
     * @return mixed array
     */

    var $sqlmin;

    /**
     * @return mixed
     */
    public function getSqlmin()
    {
        return $this->sqlmin;
    }

    /**
     * @param mixed $sqlmin
     */
    public function setSqlmin($sqlmin)
    {
        $this->sqlmin = $sqlmin;
    }



    public function decomposer($query)
    {
        // transforma em minusculas
        $sql = $query;


        $this->setSqlmin(strtolower(trim($query))) ;//

        // gambiarra
        $jsonArrayJoin["tipo"] = "inner join";
        $jsonArrayJoin["dataset2"]="w7a6-9xrz";
        $jsonArrayJoin["dataset1"]="vz8c-29aj";
        $jsonArrayJoin["cond"]="=";


        $jsonArray["join"] = $jsonArrayJoin;
        $jsonArray["consulta1"] = $this->gerarInfor($sql);

        $jsonArrayInfo["dataset"] = "vz8c-29aj";
        $jsonArrayInfo["fields"] = array_filter ({"phone"});
        $jsonArrayInfo["filters"] = null;
        $jsonArrayInfo["order"] = null;
        $jsonArrayInfo["limit"] = null;

        $jsonArray["consulta2"] = $jsonArrayInfo;

        //$jsonArray["join"] = null;
        //$jsonArray["consulta1"] = $this->gerarInfor($sql);
        //$jsonArray["consulta2"] = null;
        //$jsonArray = array_filter ( $jsonArray);

        return $jsonArray;
    }

    public function gerarInfor($sql)
    {

        // pega a posição de cada palavra reservada da linguagem
        $indexes[] = strpos( $this->getSqlmin(), "db.");//from 0
        $indexes[] = strpos( $this->getSqlmin(), ".find");//where 1
        $indexes[] = strpos( $this->getSqlmin(), "sort");//order by 2
        $indexes[] = strpos( $this->getSqlmin(), "limit");//limit 3

        // guarda os campos, dataset, filtros, ordem e limite
        $jsonArrayInfo["fields"]    = $this->getFields($sql,$indexes[1] + 6 );//equivale ao select
        $jsonArrayInfo["dataset"]   = $this->getDataset($sql, $indexes[0]+3 ,$indexes[1]);//from  (ok)
        $jsonArrayInfo["filters"]   = $this->getFilters($sql,$indexes[1] + 6 );//where
        $jsonArrayInfo["order"]     = $indexes[3] === false ? false : $this->getOrder($sql, $indexes[2] + 6);//order by
        $jsonArrayInfo["limit"]     = $indexes[3] === false ? false : $this->getLimit($sql, $indexes[3] + 6);//limit

        $jsonArray["info"] =  ($jsonArrayInfo);


        return $jsonArrayInfo;
    }

    private function getDataset($sql, $begin, $indexes)
    {
        return trim(substr($sql, $begin, ($indexes - $begin)));
    }

    private function getFilters($sql, $begin )
    {
        $str_fields = trim(substr($sql, $begin  ));

        $indexes[] = strpos($str_fields, "}");

        return  str_ireplace ("","", str_ireplace( "}","",
            str_ireplace("':", "=", trim(substr($str_fields,1,($indexes[0]))))));
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
