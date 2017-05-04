<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Redis;
use App\Http\Models\MysqlModel;
use App\Http\Models\PostgresModel;
use App\Http\Models\MongoModel;
use App\Http\Models\Neo4jModel;
use App\Http\Models\RedisModel;
use DB;

class ApiModel extends Model {
	/**
	 * Funcão...
	 *
	 **/
    public function get_data($info){
        // identifica os dados
        $dataset = (!empty($info['dataset'])) ? $info['dataset'] : null;
        unset($info['dataset']);
        $fields = (!empty($info['fields'])) ? $info['fields'] : null;
        unset($info['fields']);
        $order = (!empty($info['order'])) ? $info['order'] : null;
        unset($info['order']);
        $limit = (!empty($info['limit'])) ? $info['limit'] : null;
        unset($info['limit']);
        $format = (!empty($info['format'])) ? $info['format'] : null;
        unset($info['format']);

        // identifica as restrinções
        $refine = []; $exclude = [];
        foreach ($info AS $key => $value) {
            if (substr($key, 0, 6) == 'refine') {
                $refine[substr($key, 7)][] = $value;
            } else if (substr($key, 0, 7) == 'exclude') {
                $exclude[substr($key, 8)][] = $value;
            }
        }

        // verifica se é redis ou outro banco (pois no arquivo de configuração é separado)
        if (empty(config('database.connections.' . $dataset))) {
            $conn = Redis::connection($dataset);
            $redis = new RedisModel;
            $result = $redis->get($conn, $dataset, $fields);
        } else {
            $conn = DB::connection($dataset);
            $type = $conn->getConfig('driver');

            // verifica qual banco de dado foi requisitado
            switch ($type) {
                case 'mysql':
                    $mysql = new MysqlModel;
                    $result = $mysql->get($conn, $fields, $order, $limit, $refine, $exclude);
                    break;
                case 'pgsql':
                    $pgsql = new PostgresModel;
                    $result = $pgsql->get($conn, $fields, $order, $limit, $refine, $exclude);
                    break;
                case 'mongodb':
                    $mongo = new MongoModel;
                    $result = $mongo->get($conn, $fields, $order, $limit, $refine, $exclude);
                    break;
                case 'neo4j':
                    $neo4j = new Neo4jModel;
                    $result = $neo4j->get($conn, $fields, $order, $limit, $refine, $exclude);
                    break;
            }
        }

        // formatar
        switch($format) {
            case 'json':
                $result = json_encode($result);
                break;
            case 'xml':
                // em caso de ser objeto, trata para converter em csv
                if (is_object($result)) {
                    $result = json_decode(json_encode($result), true);
                }

                // function to convert an array to XML using SimpleXML
                function array_to_xml($array, &$xml) {
                    foreach($array as $key => $value) {
                        if(is_array($value)) {
                            if(!is_numeric($key)){
                                $subnode = $xml->addChild(htmlspecialchars("$key"));
                                array_to_xml($value, $subnode);
                            } else {
                                array_to_xml($value, $xml);
                            }
                        } else {
                            $xml->addChild(htmlspecialchars("$key"),htmlspecialchars("$value"));
                        }
                    }
                }

                // create simpleXML object
                $xml = new \SimpleXMLElement("<data/>");

                // function call to convert array to xml
                array_to_xml($result, $xml);

                // display XML to screen
                $result = $xml->asXML();
                break;
            case 'csv':
                // em caso de ser objeto, trata para converter em csv
                if (is_object($result)) {
                    $result = json_decode(json_encode($result), true);
                }

                // função que converte array em csv
                function array_2_csv($array) {
                    $csv = array();
                    foreach ($array as $item) {
                        if (is_array($item)) {
                            $csv[] = array_2_csv($item);
                        } else {
                            $csv[] = $item;
                        }
                    }
                    return implode(',', $csv) . PHP_EOL;
                } 

                $resultKey = implode(',', array_keys($result[0])) . PHP_EOL;
                $resultValues = array_2_csv($result);
                $result = $resultKey . $resultValues;
                break;
            default:
                $result = json_encode($result);
                break;
        }

    	return $result;
    }
}
