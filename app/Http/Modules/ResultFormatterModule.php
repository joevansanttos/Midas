<?php

namespace App\Http\Modules;

class ResultFormatterModule extends Module {

    /**
     * Função responsavel por pegar o resultado do daas e montar um json
     * @param $daas_result
     * @param $query_decomposed
     * @param $api_params
     * @return string
     */
    public function formatter($daas_result, $query_decomposed, $api_params)
    {
        if($daas_result[0] == "{"){
            $saas_result = $this->json_formatter($daas_result,$query_decomposed, $api_params);
        }else if ($daas_result[0] == "<"){
            $saas_result = $this->xml_formatter($daas_result,$query_decomposed, $api_params);
        }else{
            $saas_result = $this->csv_formatter($daas_result, $query_decomposed, $api_params);
        }
        return $saas_result;
    }

    private function json_formatter($daas_result, $query_decomposed, $api_params)
    {
        $daas_result = json_decode(utf8_encode($daas_result));
        $records_param = $api_params->records_param;
        $fields_param = $api_params->fields_param;
        $fields = $query_decomposed["fields"];

        if ($records_param != null && $fields_param != null) {
            $saas_result = $this->preenchido_preenchido($fields,$daas_result,$records_param,$fields_param);
        } else if ($records_param != null && $fields_param == null) {
            $saas_result = $this->preenchido_vazio($fields,$daas_result,$records_param,$fields_param);
        }else if ($records_param == null && $fields_param != null){
            $saas_result = $this->vazio_preenchido($fields,$daas_result,$records_param,$fields_param);
        }else{
            $saas_result = $this->vazio_vazio($fields,$daas_result,$records_param,$fields_param);
        }

        return json_encode($saas_result);
    }

    private function xml_formatter($daas_result,$query_decomposed, $api_params)
    {
        $daas_result = simplexml_load_string($daas_result);
        $fields_param = $api_params->fields_param;
        $fields = $query_decomposed["fields"];

        $i = 0;
        foreach ($fields as $field) {
            $elements = $daas_result->xpath('////'.$field);
            $j = 0;
            foreach ($elements as $element) {
                $value = (string)$element;
                $saas_result[$j][$field] = $value;
                $j++;
            }
            $i++;
        }

        return json_encode($saas_result);
    }

    private function csv_formatter($daas_result,$query_decomposed, $api_params)
    {
        $records_param = $api_params->records_param;
        $fields_param = $api_params->fields_param;
        $fields = $query_decomposed["fields"];

        $daas_result = $this->csv_to_array($daas_result);
        $keys = $this->find_keys_attributes($fields,$daas_result[0]);
        $saas_result = $this->create_saas_result($daas_result,$keys,$fields);
        return json_encode($saas_result);
    }

    private function csv_to_array($daas_result) //transforma csv em um array
    {
        $lines = explode(PHP_EOL, $daas_result);
        $array = array();
        foreach ($lines as $line) {
            $array[] = str_getcsv($line);
        }

        return $array;
    }

    private function find_keys_attributes($fields,$daas_result) //funcao para encontrar ids dos campos
    {
        $i = 0;
        foreach ($fields as $key_fields => $value_fields) { //fields  sao os campos de filtragem:name etc.
          foreach ($daas_result as $key_daas => $value_daas) { //daas_result e o primeiro array com os nomes e os ids vindos do daas
            if($value_fields == $value_daas){
              $vetor_keys[$i] = $key_daas;
              $i++;
            }
          }
        }
        return $vetor_keys;
    }

    private function create_saas_result($daas_result,$ids,$fields) //funcao para criar o resultado para enviar ao saas
    {
      $i = 0;
      unset($daas_result[0]); //remover elemento do array que contem os campos desnecessarios
      foreach ($daas_result as $key_result => $daas) {
        $j = 0;
        foreach ($ids as $key_ids => $id) {
          foreach ($daas as $key_daas => $value) {
            if($id == $key_daas){
              $nome = $fields[$j];
              $saas_result[$i][$nome] = $value; //adicionar campo para json
              $j++;
            }
          }
        }
        $i++;
      }
      return $saas_result;
    }

    private function preenchido_preenchido($fields,$daas_result,$records_param,$fields_param){
        if (count($fields) == 1 && $fields[0] == "*") {
            for ($i = 0; $i < count($daas_result->{$records_param}); $i++) {
                $row = get_object_vars($daas_result->{$records_param}[$i]->{$fields_param});
                foreach ($row as $key => $value) {
                    $saas_result[$i][] = $value;
                    $saas_result[$i][$key] = $value;
                }
            }
        } else {
            for ($i = 0; $i < count($daas_result->{$records_param}); $i++) {
                $row = get_object_vars($daas_result->{$records_param}[$i]->{$fields_param});
                foreach ($row as $key => $value) {
                    if (in_array($key, $fields)) {
                        $saas_result[$i][] = $value;
                        $saas_result[$i][$key] = $value;
                    }
                }
            }
        }

        return $saas_result;
    }

    private function preenchido_vazio($fields,$daas_result,$records_param,$fields_param){
        if (count($fields) == 1 && $fields[0] == "*") {
            for ($i = 0; $i < count($daas_result->{$records_param}); $i++) {
                $row = get_object_vars($daas_result->{$records_param}[$i]);

                foreach ($row as $key => $value) {
                    $saas_result[$i][] = $value;
                    $saas_result[$i][$key] = $value;
                }
            }
        } else {
            for ($i = 0; $i < count($daas_result->{$records_param}); $i++) {
                $row = get_object_vars($daas_result->{$records_param}[$i]);

                foreach ($row as $key => $value) {
                    if (in_array($key, $fields)) {
                        $saas_result[$i][] = $value;
                        $saas_result[$i][$key] = $value;
                    }
                }
            }
        }
        return $saas_result;
    }

    private function vazio_preenchido($fields,$daas_result,$records_param,$fields_param){
        if (count($fields) == 1 && $fields[0] == "*") {
            for ($i = 0; $i < count($daas_result); $i++) {
                $row = get_object_vars($daas_result[$i]->{$fields_param});

                foreach ($row as $key => $value) {
                    $saas_result[$i][] = $value;
                    $saas_result[$i][$key] = $value;
                }
            }
        } else {
            for ($i = 0; $i < count($daas_result); $i++) {
                $row = get_object_vars($daas_result[$i]->{$fields_param});

                foreach ($row as $key => $value) {
                    if (in_array($key, $fields)) {
                        $saas_result[$i][] = $value;
                        $saas_result[$i][$key] = $value;
                    }
                }
            }
        }
        return $saas_result;
    }

    private function vazio_vazio($fields,$daas_result,$records_param,$fields_param){
        if (count($fields) == 1 && $fields[0] == "*") {
            for ($i = 0; $i < count($daas_result); $i++) {
                $row = get_object_vars($daas_result[$i]);

                foreach ($row as $key => $value) {
                    $saas_result[$i][] = $value;
                    $saas_result[$i][$key] = $value;
                }
            }
        } else {
            for ($i = 0; $i < count($daas_result); $i++) {
                $row = get_object_vars($daas_result[$i]);

                foreach ($row as $key => $value) {
                    if (in_array($key, $fields)) {
                        $saas_result[$i][] = $value;
                        $saas_result[$i][$key] = $value;
                    }
                }
            }
        }
        return $saas_result;
    }

}
