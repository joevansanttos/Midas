<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use DB;

class ApiModel extends Model
{
	/**
	 * Funcão...
	 *
	 **/
    public function get_data($dataset, $fields = null, $order = null, $limit = null, $format = null, $refine = null, $exclude = null){
    	$conn = DB::connection($dataset);
        $type = $conn->getConfig('driver');

        switch ($type) {
            case 'mysql':
                $result = $this->mysql($conn, $fields, $order, $limit, $refine, $exclude);
                break;
            case 'pgsql':
                $result = $this->postgres($conn);
                break;
            case 'mongodb':
                $result = $this->mongodb($conn, $fields, $order, $limit, $refine, $exclude);
                break;
        }

    	return $result;
    }

    private function mysql($conn, $fields = null, $order = null, $limit = null, $refine = null, $exclude = null) {
        // organiza os campos
        if (empty($fields)) {
            if (empty($conn->getConfig('fields'))) {
                $fields = '*';
            } else {
                $fields = $conn->getConfig('fields');
            }
        }

        // organiza as restrições
        if (!empty($conn->getConfig('where'))) {
            $where = ' WHERE ' . $conn->getConfig('where');
            if (!empty($refine)) {
                foreach ($refine AS $key => $value) {
                    foreach ($refine AS $key => $value) {
                        $where .= ' AND ' . "$key = '{$v}'";
                    }
                }
            }

            if (!empty($exclude)) {
                foreach ($exclude AS $key => $value) {
                    foreach ($value AS $v) {
                        $where .= ' AND ' . "$key <> '{$v}'";
                    }
                }
            }
        } else {
            if (empty($refine) AND empty($exclude)) {
                $where = '';
            } else {
                $where = ' WHERE ';
                if (!empty($refine)) {
                    foreach ($refine AS $key => $value) {
                        foreach ($value AS $v) {
                            $where .= "$key = '{$v}' AND ";
                        }
                    }
                }

                if (!empty($exclude)) {
                    foreach ($exclude AS $key => $value) {
                        foreach ($value AS $v) {
                            $where .= "$key <> '{$v}' AND ";
                        }
                    }
                }
            }
        }

        // organiza a ordenação
        if (empty($order)) {
            $o = $conn->getConfig('order');
            $order = empty($o) ? '' : ' ORDER BY ' . $o;
        } else {
            $order = ' ORDER BY ' . $order;
        }

        // organiza o limite
        if (empty($limit)) {
            $l = $conn->getConfig('limit');
            $limit = empty($l) ? '' : ' LIMIT ' . $l;
        } else {
            $limit = ' LIMIT ' . $limit;
        }

        return $conn->select('SELECT ' .
            $fields .
            ' FROM ' . $conn->getConfig('from') .
            ' ' . $conn->getConfig('join') .
            substr($where, 0, -4) .
            $order .
            $limit);
    }

    private function postgres($conn, $fields = null, $sort = null, $limit = null, $format = null, $refine = null, $exclude = null) {
    	
    }

    private function mongodb($conn, $fields = null, $sort = null, $limit = null, $format = null, $refine = null, $exclude = null) {
    	print_r($conn);
    }

    private function redis() {
    	
    }

    private function voltdb() {
    	
    }

    private function memsql() {
    	
    }
}
