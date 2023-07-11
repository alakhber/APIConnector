<?php

namespace App\Http\Controllers;

use App\Classes\CURLClass;
use App\Classes\GzzClass;
use Illuminate\Http\Request;

class ApiConnectorController extends Controller
{
    public function connector(Request $request){
       
        $headers = [
             'Content-Type: application/json',
        ];

        $a = 
         (new GzzClass($request->url,$request->method))
         ->setHeaders($headers)
         ->build()
         ->run();
        return $a;
    }
}

