<?php

namespace App\Http\Modules;

class QueryDecomposerModule extends Module {

    /**
     * Função que identifica as palavras chave da linguagem de consulta e retorna as informações úteis
     * @param $query string
     * @return mixed array
     */
    public function decomposer($query2)
    {
        $jsonArray2 =  QueryDecomposerModule::decomposerMySql($query2) ;//->decomposerMySql($query2);
       //$jsonArray2 =  QueryDecomposerModule::decomposerMongoDB(query2) ;//->decomposerMySql($query2);

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
        $indexes[] = strpos($sql, ".find({");//where 1
        $indexes[] = strpos($sql, "},{");// select 2
        $indexes[] = strpos($sql, "})");// fim select 3
        $indexes[] = strpos($sql, ".sort({");//order by 4
        //$indexes[] = strpos($sql, "limit");//limit 5

        // guarda os campos, dataset, filtros, ordem e limite
        $jsonArray["fields"]    = $this->getFields2($sql, $indexes[2] +3,$indexes[3]);//equivale ao select
        $jsonArray["dataset"]   = $this->getDataset2($sql, $indexes[0]+3 ,$indexes[1]);//from  (ok)
        $jsonArray["filters"]   = $this->getFilters2($sql,$indexes[1] + 7, $indexes[2]  );//where  (ok)
       // $jsonArray["order"]     = $indexes[3] === false ? false : $this->getOrder2($sql, $indexes[4] + 7, $indexes, 4);//order by
       // $jsonArray["limit"]     = $indexes[3] === false ? false : $this->getLimit2($sql, $indexes[5] + 5);//limit

        return $jsonArray;
    }




    private function getOrder2($sql, $begin, $indexes, $start)
    {
        for ($i = $start; $i < count($indexes); $i++) {
            if ($indexes[$i] !== false) {
                return trim(substr($sql, $begin, $indexes[$i] - $begin));
            }
        }

        return trim(substr($sql, $begin));
    }

    private function getLimit2($sql, $begin)
    {
        return trim(substr($sql, $begin));
    }


    private function getFields2($sql, $inicio ,$fim)
    {
        $str_fields = trim(substr($sql, $inicio, $fim - $inicio ));
        return explode(",", str_ireplace(" ", "", $str_fields));
    }

    private function getDataset2($sql, $inicio ,$fim)
    {
        $str_fields = trim(substr($sql, $inicio, $fim - $inicio ));
        return explode(",", str_ireplace(" ", "", $str_fields));
    }

    private function getFilters2($sql, $inicio ,$fim)
    {
        $str_fields = trim(substr($sql, $inicio, $fim - $inicio ));
          return explode(",", str_ireplace(" ", "", $str_fields));
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