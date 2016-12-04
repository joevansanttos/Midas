<?php
/**
 * Created by PhpStorm.
 * User: maires
 * Date: 04/12/16
 * Time: 02:13
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\DaaSModel;

class MainController extends Controller
{

    public function index() {
        return view('main');
    }

    /**
     * Função que recebe a query e chama as demais funções
     * @param Request $request
     */
    public function daas(Request $request)
    {
        $daas_model = new DaaSModel;

        $query = urldecode($request->input('query'));
        $query_decomposed = $this->query_decomposer($query);
        $api_params = $daas_model->get_provider_api($query_decomposed["dataset"]);
        $daas_request_url = $this->query_builder($api_params[0], $query_decomposed);

        $daas_result = file_get_contents($daas_request_url, false);
        $result = $this->result_formatter(json_decode(utf8_encode($daas_result)), $query_decomposed, $api_params[0]);

        return view('daas')->with(compact('result'));

    }

    /**
     * Função que decompoe a query para pegar as informações úteis
     * @param $query
     * @return mixed
     */
    private function query_decomposer($query)
    {
        $sql = strtolower(trim($query));

        $indexes[] = strpos($sql, "from");
        $indexes[] = strpos($sql, "where");
        $indexes[] = strpos($sql, "order by");
        $indexes[] = strpos($sql, "limit");

        $jsonArray["fields"] = $this->getFields($sql, $indexes[0] - 6);
        $jsonArray["dataset"] = $this->getDataset($sql, $indexes[0] + 4, $indexes, 1);
        $jsonArray["filters"] = $indexes[1] === false ? false : $this->getFilters($sql, $indexes[1] + 5, $indexes, 2);
        $jsonArray["order"] = $indexes[2] === false ? false : $this->getOrder($sql, $indexes[2] + 8, $indexes, 3);
        $jsonArray["limit"] = $indexes[3] === false ? false : $this->getLimit($sql, $indexes[3] + 5);

        return $jsonArray;
    }

    /**
     * Função responsavel por montar o request para enviar ao daas
     * @param $api_params
     * @param $values
     * @return string
     */
    private function query_builder($api_params, $values)
    {
        $daas_request = $api_params->domain;
        $daas_request .= $api_params->search_path;

        if ($api_params->dataset_param !== null) {
            $daas_request .= "?" . $api_params->dataset_param . "=" . $values["dataset"];
        } else {
            $daas_request .= $values["dataset"];
        }

        if ($values["filters"]) {
            if (strpos($daas_request, "?") !== false) {
                $daas_request .= "&" . $api_params->query_param . "=" . $values["filters"];
            } else {
                $daas_request .= "?" . $api_params->query_param . "=" . $values["filters"];
            }
        }

        if ($values["order"]) {
            if (strpos($daas_request, "?") !== false) {
                $daas_request .= "&" . $api_params->sort_param . "=" . $values["order"];
            } else {
                $daas_request .= "?" . $api_params->sort_param . "=" . $values["order"];
            }
        }

        if ($values["limit"]) {
            if (strpos($daas_request, "?") !== false) {
                $daas_request .= "&" . $api_params->limit_param . "=" . $values["limit"];
            } else {
                $daas_request .= "?" . $api_params->limit_param . "=" . $values["limit"];
            }
        }

        return $daas_request;
    }

    /**
     * Função responsavel por pegar o resultado do daas e montar um json
     * @param $daas_result
     * @param $query_decomposed
     * @param $api_params
     * @return string
     */
    private function result_formatter($daas_result, $query_decomposed, $api_params)
    {
        $records_param = $api_params->records_param;
        $fields_param = $api_params->fields_param;
        $fields = $query_decomposed["fields"];

        if ($records_param != null && $fields_param != null) {
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
        } else if ($records_param != null && $fields_param == null) {
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
        } else if ($records_param == null && $fields_param != null) {
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
        } else {
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
        }

        return json_encode($saas_result);
    }

    /* ------------------------------------------------ */
    /* -------------- Funções auxiliares -------------- */
    /* ------------------------------------------------ */

    private function getFields($sql, $from)
    {
        $str_fields = trim(substr($sql, 6, $from));
        return explode(",", str_ireplace(" ", "", $str_fields));
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

    private function getFilters($sql, $begin, $indexes, $start)
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