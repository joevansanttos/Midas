<?php

namespace App\Http\Modules;

class QueryDecomposerModule extends Module {

    /**
     * Função que identifica as palavras chave da linguagem de consulta e retorna as informações úteis
     * @param $query string
     * @return mixed array
     */
    public function decomposer($query)
    {
        $TipoBd = trim(substr($query, 6,3 ));
        if($TipoBd === 'db.' )
                $jsonArray2 =    self::decomposerMongoDB($query) ;
        elseif ($TipoBd === 'SEL')
            $jsonArray2 =  QueryDecomposerModule::decomposerMySql($query) ;
        else
        {
            print_r( "Banco não identificado");
            exit();
        }
        return $jsonArray2;
    }

    public function decomposerMySql($query)
    {
        // transforma em minusculas
        $sql = strtolower(trim($query));

        // pega a posição de cada palavra reservada da linguagem
        $indexes[] = strpos($sql, "from");
        $indexes[] = strpos($sql, "where");
        $indexes[] = strpos($sql, "order by");
        $indexes[] = strpos($sql, "limit");

        // guarda os campos, dataset, filtros, ordem e limite
        $jsonArray["fields"]    = $this->getFields($sql, $indexes[0]); //equivale ao select
        $jsonArray["dataset"]   = $this->getDataset($sql, $indexes[0] + 4, $indexes, 1);//from
        $jsonArray["filters"]   = $indexes[1] === false ? false : $this->getFilters($sql, $indexes[1] + 5, $indexes, 2); //where
        $jsonArray["order"]     = $indexes[2] === false ? false : $this->getOrder($sql, $indexes[2] + 8, $indexes, 3);//order by
        $jsonArray["limit"]     = $indexes[3] === false ? false : $this->getLimit($sql, $indexes[3] + 5);//limit

        return $jsonArray;
    }

    public function decomposerMongoDB($query)
    {
        // transforma em minusculas
        $sql = strtolower(trim($query));

        // pega a posição de cada palavra reservada da linguagem
        $indexes[] = strpos($sql, "db.");//from 0
        $indexes[] = strpos($sql, ".find");//where 1
        $indexes[] = strpos($sql, "sort");//order by 2
        $indexes[] = strpos($sql, "limit");//limit 3

        // guarda os campos, dataset, filtros, ordem e limite
        $jsonArray["fields"]    = $this->getFields2($sql,$indexes[1] + 6 );//equivale ao select
        $jsonArray["dataset"]   = $this->getDataset2($sql, $indexes[0]+3 ,$indexes[1]);//from  (ok)
        $jsonArray["filters"]   = $this->getFilters2($sql,$indexes[1] + 6 );//where
        $jsonArray["order"]     = $indexes[3] === false ? false : $this->getOrder2($sql, $indexes[2] + 6);//order by
        $jsonArray["limit"]     = $indexes[3] === false ? false : $this->getLimit2($sql, $indexes[3] + 6);//limit

        return $jsonArray;
    }


    private function getDataset2($sql, $begin, $indexes)
    {
        return trim(substr($sql, $begin, ($indexes - $begin)));
    }

    private function getFilters2($sql, $begin )
    {
        $str_fields = trim(substr($sql, $begin  ));

        $indexes[] = strpos($str_fields, "}");

           return  str_ireplace ("'","", str_ireplace( "}","",
               str_ireplace(":", "=", trim(substr($str_fields,1,($indexes[0]))))));
    }

    private function getFields2($sql, $begin)
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

    private function getOrder2($sql, $begin)
    {
        $str_fields = trim(substr($sql, $begin ));
        $indexes[] = strpos($str_fields, ":");
        return     str_ireplace ("'","", trim(substr($str_fields, 0 ,$indexes[0]-1 )));
    }

    private function getLimit2($sql, $begin)
    {
        $str_fields = trim(substr($sql, $begin  ));
        $indexes[] = strpos($str_fields, ")");
        return trim(substr($str_fields, 0 ,$indexes[0] ));
    }








    private function getFields($sql, $from)
    {
        $str_fields = trim(substr($sql, 12, $from - 12));
        return explode(",", str_ireplace(" ", "", $str_fields));
    }

    private function getFilters($sql, $begin, $indexes, $start)
    {
        for ($i = $start; $i < count($indexes); $i++) {
            if ($indexes[$i] !== false) {
                return trim(substr($sql, $begin, $indexes[$i] - $begin));
            }
        }

        return trim(substr($sql, $begin));
    }

    private function getDataset($sql, $begin, $indexes, $start)
    {
        for ($i = $start; $i < count($indexes); $i++) {
            if ($indexes[$i] !== false) {
                return trim(substr($sql, $begin, $indexes[$i] - $begin));
            }
        }

        return trim(substr($sql, $begin));
    }

    private function getOrder($sql, $begin, $indexes, $start)
    {
        for ($i = $start; $i < count($indexes); $i++) {
            if ($indexes[$i] !== false) {
                return trim(substr($sql, $begin, $indexes[$i] - $begin));
            }
        }

        return trim(substr($sql, $begin));
    }

    private function getLimit($sql, $begin)
    {
        return trim(substr($sql, $begin));
    }

}
