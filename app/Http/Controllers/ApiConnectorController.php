<?php

namespace App\Http\Controllers;

use App\Classes\CURLClass;
use App\Services\ApiConnector;
use Illuminate\Http\Request;

class ApiConnectorController extends Controller
{
    public function connector(Request $request){
        
        $headers = [
                'Accept'=>'a',
                'Accept-Charset'=>'application/json',
                'Accept-Encoding'=>'application/ecmascript',
                'Accept-Language'=>'az',
             ];
             
        $a = 
         (new CURLClass($request->url,'post'))
         ->setHeaders($headers)
         ->setFormParams(['fin'=>1112])
         ->setConnectTimeout(2)
         ->setReferer($request->url)
         ->setAuth('username:password')
         ->setVersion(2)
         ->setBody('test')
         ->build()
         ->run();
        dd($a);
    }
}

