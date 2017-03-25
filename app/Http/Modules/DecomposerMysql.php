<?php

namespace App\Http\Modules;

class DecomposerMysql extends QueryDecomposerModule {

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

        if (strpos($this->getSqlmin(), "join"))
        {
            $jsonArray["join"] = $this->getJoin($sql);

            $jsonArray2["consulta"] = $this->gerarInfor($sql);
            $jsonArray["consulta1"] = $this -> simplificarsql( $jsonArray2["consulta"],$jsonArray["join"]["dataset1"][1]);
            $jsonArray["consulta2"] = $this -> simplificarsql( $jsonArray2["consulta"],$jsonArray["join"]["dataset2"][1]);
            $jsonArray["consulta2"] = array_filter ( $jsonArray["consulta2"]);
            $jsonArray["consulta1"] = array_filter ( $jsonArray["consulta1"]);
        }
        else
        {
            $jsonArray["join"] = null;
            $jsonArray["consulta1"] = $this->gerarInfor($sql);
            $jsonArray["consulta2"] = null;

        }
                return $jsonArray;

        exit();

    }

    public function gerarInfor($sql)
    {

        // pega a posição de cada palavra reservada da linguagem
        $indexes[] = strpos( $this->getSqlmin(), "from");
        $indexes[] = strpos($this->getSqlmin(), "where");
        $indexes[] = strpos($this->getSqlmin(), "order by");
        $indexes[] = strpos($this->getSqlmin(), "limit");

        // guarda os campos, dataset, filtros, ordem e limite
        $jsonArrayInfo["fields"]    = $this->getFields($sql, $indexes[0]); //equivale ao select
        $jsonArrayInfo["filters"]   = $indexes[1] === false ? false : $this->getFilters($sql, $indexes[1] + 5, $indexes, 2); //where
        $jsonArrayInfo["order"]     = $indexes[2] === false ? false : $this->getOrder($sql, $indexes[2] + 8, $indexes, 3);//order by
        $jsonArrayInfo["limit"]     = $indexes[3] === false ? false : $this->getLimit($sql, $indexes[3] + 5);//limit
        $jsonArrayInfo["dataset"] = $this->getDataset($sql, $indexes[0] + 4, $indexes, 1);//from
        $jsonArray["info"] =  ($jsonArrayInfo);


        return $jsonArrayInfo;
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

   /* private function getJoin($sql, $begin)
    {
        //pega o dataset do 1 Join
        $str_join = trim(substr($sql, $begin+5));
        $indexes[] = strpos($str_join, ".");
        $str_joindata1 = trim(substr($str_join, 0,$indexes[0] ));

        //constroi o dataset do 2 Join
        $indexes[] = strpos($str_join, "=");
        $str_join2 = trim(substr($str_join ,$indexes[1]+1 ));
        $indexes2[] = strpos($str_join2, ".");
        $str_joindata2 = trim(substr( $str_join2 ,0, $indexes2[0]));

        $jsonArray["dataset1"] =  ($str_joindata1);
        $jsonArray["dataset2"] =  ($str_joindata2);
        return $jsonArray ;
    }*/

    private function getJoin($sql)
    {
        $indexes[] = strpos($this->getSqlmin(), "join");//
        $join = trim(substr($sql, $indexes[0]+5));
        $joinmin = strtolower(trim($join));
        $indexes[] = strpos($joinmin, "=");
        $indexes[] = strpos($joinmin, "where");
        $indexes[] = strpos($joinmin, "on");



        //coloca a condição
        if (strpos($this->getSqlmin(), "cross join"))
            $jsonArrayJoin["tipo"] = "cross join";
        //não
        elseif (strpos($this->getSqlmin(), "inner join"))
            $jsonArrayJoin["tipo"] = "inner join" ;

        elseif (strpos($this->getSqlmin(), "left outer join"))
            $jsonArrayJoin["tipo"] = "left outer join" ;

        elseif (strpos($this->getSqlmin(), "right outer join"))
            $jsonArrayJoin["tipo"] = "right outer join" ;
        //não
        elseif (strpos($this->getSqlmin(), "outer full join"))
            $jsonArrayJoin["tipo"] = "outer full join" ;

        elseif (strpos($this->getSqlmin(), "join"))
            $jsonArrayJoin["tipo"] = "join"  ;


        $joindoOn = trim(substr($join,0, $indexes[3] ));
        //identifica o dataset 1
        $join1 = trim(substr($join, $indexes[3]+2,($indexes[1]- $indexes[3]-2) ));
        $jsonArrayJoin["dataset1"]= explode(".",$join1);
        $jsonArrayJoin["cond"]= trim(substr($join,$indexes[1], 2))  ;



        //identifica o dataset 2
        $join2 = trim(substr($join,$indexes[1]+2,  ($indexes[2]) - $indexes[1]-2));
        $jsonArrayJoin["dataset2"]= explode(".",$join2);

        if ($join1 != $joindoOn )
        {
            $jsonArrayJoinTroca = $jsonArrayJoin["dataset2"];
            $jsonArrayJoin["dataset2"] =  null ;
            $jsonArrayJoin["dataset2"] = $jsonArrayJoin["dataset1"];
            $jsonArrayJoin["dataset1"] = null;
            $jsonArrayJoin["dataset1"] = $jsonArrayJoinTroca;
        }


        return  $jsonArrayJoin ;
    }

    private function simplificarsql ($jsonArray, $dataset)
    {
        if (strpos($jsonArray["limit"], $dataset))
        {
            $jsonArray["limit"] = str_ireplace(" ","" ,str_ireplace(".","",str_ireplace($dataset, "", $jsonArray["limit"]))) ;
        }
        else
            $jsonArray["limit"] = null ;

        if (strpos($jsonArray["order"], $dataset))
        {
            $jsonArray["order"] =  str_ireplace(" ","" ,str_ireplace(".","",str_ireplace($dataset, "", $jsonArray["order"]))) ;
        }
        else
            $jsonArray["order"] = null ;

        $pos2 = strpos($jsonArray["filters"], $dataset);
        if ($pos2 === false)
        {
            $jsonArray["filters"] = null ;
        }
        else
            $jsonArray["filters"] = str_ireplace(" ","", str_ireplace(".","",str_ireplace($dataset, "", $jsonArray["filters"]))) ;

        $tamanho = count($jsonArray["fields"]);
        for ($i = 0; $i < $tamanho; )
        {
            $pos = strpos($jsonArray["fields"][$i], $dataset);
            if ($pos === false)
            {
                $jsonArray["fields"][$i] = null ;
            }
            else
                $jsonArray["fields"][$i] = str_ireplace(" ","", str_ireplace(".","",str_ireplace("$dataset","",$jsonArray["fields"][$i]))) ;
            $i++ ;
        }

        $jsonArray["fields"] = array_filter ( $jsonArray["fields"]);
        $jsonArray["dataset"] = $dataset ;

        return $jsonArray;

    }

    private function teste ($jsonArray, $dataset)
    {
          $jsonArray["limit"] =  str_ireplace("."," , ", $jsonArray["limit"]) ;
          $jsonArray["order"] =  str_ireplace("."," , ",$jsonArray["order"]) ;
          $jsonArray["filters"] =  str_ireplace("."," , ", $jsonArray["filters"]) ;
          $tamanho = count($jsonArray["fields"]);
          for ($i = 0; $i < $tamanho; )
            {
                $jsonArray["fields"][$i] =  str_ireplace("."," , ",str_ireplace("$dataset", " ", $jsonArray["fields"][$i])) ;
                $i++ ;
            }
        $jsonArray["dataset"] = $dataset ;

        $jsonArray = array_filter ( $jsonArray);

        return $jsonArray;

    }

}
