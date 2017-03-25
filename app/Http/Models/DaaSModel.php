<?php

/**
 * Created by GT-Nuvem.
 * User: maires
 * Date: 04/12/16
 * Time: 17:19
 */

namespace App\Http\Models;

//use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model;

class DaaSModel extends Model
{

    public function get_provider_api($dataset_id){
      $dis = file_get_contents('dis.json');
      $json = json_decode($dis);
      $table = $json->$dataset_id;
      return $table;
    }
}
