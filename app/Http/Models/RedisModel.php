<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class RedisModel extends Model {
    public function get($conn, $dataset, $field = null) {
        $type = config('database.redis.' . $dataset . '.type');
        if ($type == 'hash') {
            return $conn->hgetall($field);
        } else if ($type == 'list') {
            return $conn->lrange($field, 0, 4);
        } else {
            return $conn->get($field);
        }
    }
}