<?php
/**
 * Created by PhpStorm.
 * User: maires
 * Date: 04/12/16
 * Time: 02:13
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainController extends Controller {

    public function index(Request $request) {

        $query = $request->input('query');
        echo $query;

    }

}