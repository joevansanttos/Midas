<?php

namespace App\Http\Models;

class Neo4jModel extends DbaasModel {
    public function get($conn, $fields = null, $order = null, $limit = null, $refine = null, $exclude = null) {
        // variaveis configuradas
        $c_fields = $conn->getConfig('fields');
        $c_from = $conn->getConfig('from');
        $c_join = $conn->getConfig('join');
        $c_order = $conn->getConfig('order');
        $c_where = $conn->getConfig('where');
        $c_limit = $conn->getConfig('limit');
        $c_group = $conn->getConfig('groupBy');

        // montagem da consulta ao banco
        $query = $conn->table($c_from);

        // organiza os joins (4 em 4 valores do array)
        if (!empty($c_join)) {
            for ($i = 0; $i < count($c_join); $i+=4) {
                $query = $query->join($c_join[$i], $c_join[$i+1], $c_join[$i+2], $c_join[$i+3]);
            }
        }

        // organiza os campos
        if (empty($fields)) {
            if (empty($c_fields)) {
                $fields = array('*');
            } else {
                $fields = $c_fields;
            }
        } else {
            $fields = explode(',', $fields);
        }
        $query = call_user_func_array(array($query, 'select'), $fields);

        // organiza as restrições
        if (!empty($c_where)) {
            for ($i = 0; $i < count($c_where); $i+=3) {
                $query = $query->where($c_where[$i], $c_where[$i+1], $c_where[$i+2]);
            }
            if (!empty($refine)) {
                foreach ($refine AS $key => $value) {
                    $query = $query->where($key, '=', $value);
                }
            }

            if (!empty($exclude)) {
                foreach ($exclude AS $key => $value) {
                    $query = $query->where($key, '<>', $value);
                }
            }
        } else {
            if (!empty($refine)) {
                foreach ($refine AS $key => $value) {
                    $query = $query->where($key, '=', $value);
                }
            }

            if (!empty($exclude)) {
                foreach ($exclude AS $key => $value) {
                    $query = $query->where($key, '<>', $value);
                }
            }
        }

        // organiza a ordenação
        if (empty($order) && !empty($c_order)) {
            $query = $query->orderBy($c_order[0], $c_order[1]);
        } else if (!empty($order)) {
            $order = (strpos(trim($order), ' ') !== false) ? explode(' ', $order) : array($order, 'asc');
            $query = $query->orderBy($order[0], $order[1]);
        }

        // organiza o limite
        if (empty($limit) && !empty($c_limit)) {
            $query = $query->limit($c_limit);
        } else if (!empty($limit)) {
            $query = $query->limit($limit);
        }

        // organiza o group by
        if (!empty($c_group)) {
            $query = $query->groupBy($c_group);
        }

        $result = array();
        foreach ($query->get() AS $q) {
            foreach ($q AS $row) {
                $result[] = $row->getProperties();
            }
        }

        return $result;
    }
}