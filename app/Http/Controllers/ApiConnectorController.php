<?php

namespace App\Http\Controllers;

use App\Services\ApiRequest;
use Illuminate\Http\Request;

class ApiConnectorController extends Controller
{
    public function connector(Request $request){
       
        $new = (new ApiRequest('https://countriesnow.space/api/v0.1/countries/population/cities','get'))
            ->send();

        return $new;
    }
}

