<?php

namespace App\Services;

use Exception;

class ApiRequest
{
    protected $url;
    protected $methods = ['POST','GET','DELETE','PUT','PATCH'];
    protected $method ;
    protected $headers ;
    protected $redirect = false;
    protected $auth ;
    protected $body ;
    protected $cert ;
    protected $cookies ;
    protected $connectTimeout = 0 ;
    protected $debug ;
    protected $decodeContent;
    protected $delay ;
    protected $expect ;
    protected $ipResolve ;
    protected $formParams ;
    protected $httpErrors ;
    protected $idnConversion ;
    protected $json ;
    protected $multipart ;
    protected $onHeaders ;
    protected $proxy ;
    protected $query ;
    protected $readTimeout = 3.14;
    protected $sink ;
    protected $sslKey ;
    protected $stream = false;
    protected $synchronous ;
    protected $verify = null ;
    protected $timeout = 0;
    protected $version ;
    protected $userAgent ;
    protected $referer ;
    protected $class ;
        const DEFAULTSERVICE = 'curl';

    public function __construct(string $url,string $method,$class=null){
        if(!in_array(strtoupper($method),$this->methods)) throw new Exception('This Method: '. $method.' Not Found !',404);
        $this->url = trim($url);
        $this->method = trim(strtoupper($method));
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
         $this->class =  trim($class);

    }
    public function setHeaders(array $headers){
        $hdrs = [];
        if(is_array($headers) and count($headers)>0){
            foreach ($headers as $key => $value) {
                $parse = explode(':',$value);
                if(count($parse)==2){
                    $hdrs[$parse[0]] =$parse[1];
                }
            }
        }
        $this->headers = $hdrs;
        return $this;
    }
    public function setRedirect( $redirect){
        $this->redirect = $redirect;
        return $this;
    }
    public function setAuth($auth){
        $this->auth = $auth;
        return $this;
    }
    public function setBody($body){
        $this->body = $body;
        return $this;
    }
    public function setCert( $cert, $password){
        $setCert = [
            'cert'=>$cert,
            'password'=>$password,
        ];
        $this->cert = $setCert;
        return $this;
    }
    public function setCookies($cookies){
        $this->cookies = $cookies;
        return $this;
    }
    public function setConnectTimeout( $second){
        $this->connectTimeout = $second;
        return $this;
    }
    public function setDebug($debug){
        $this->debug = $debug;
        return $this;
    }
    public function setDecodeContent( $decodeContent){
        $this->decodeContent = $decodeContent;
        return $this;
    }
    public function setDelay( $delay){
        $this->delay = $delay;
        return $this;
    }
    public function setExpect( $expect){
        $this->expect = $expect;
        return $this;
    }
    public function setIpResolve( $ipResolve){
        $this->ipResolve = $ipResolve;
        return $this;
    }
    public function setFormParams( $formParams){
        $this->formParams = $formParams;
        return $this;
    }
    public function setHttpErrors( $httpErrors){
        $this->httpErrors = $httpErrors;
        return $this;
    }
    public function setIdnConversion( $idnConversion){
        $this->idnConversion = $idnConversion;
        return $this;
    }
    public function setJson($json){
        $json = json_decode($json,true);
        if(is_null($json)) throw new Exception('Invalid Json');
        $this->json = json_encode($json);
        return $this;
    }
    public function setMultipart( $multipart){
        $this->multipart = $multipart;
        return $this;
    }
    public function setOnHeaders( $onHeaders){
        $this->onHeaders = $onHeaders;
        return $this;
    }
    public function setProxy( $proxy){
        $this->proxy = $proxy;
        return $this;
    }
    public function setQuery($query){
        $this->query = $query;
        return $this;
    }
    public function setReadTimeout( $readTimeout){
        $this->readTimeout = $readTimeout;
        return $this;
    }
    public function setSink($sink){
        $this->sink = $sink;
        return $this;
    }
    public function setSslKey( $sslKey){
        $this->sslKey = $sslKey;
        return $this;
    }
    public function setStream( $stream){
        $this->stream = $stream;
        return $this;
    }
    public function setSynchronous( $synchronous){
        $this->synchronous = $synchronous;
        return $this;
    }
    public function setVerify($verify){
        $this->verify = $verify;
        return $this;
    }
    public function setTimeout( $timeout){
        $this->timeout = $timeout;
        return $this;
    }
    public function setVersion($version){
        $this->version = $version;
        return $this;
    }
    public function setUserAgent( $userAgent){
        $this->userAgent = $userAgent;
        return $this;
    }
    public function setReferer( $referer){
        $this->referer = $referer;
        return $this;
    }
    public function send(){
        $obj = $this->getClass();
        $res = (new $obj($this))->build()->send();
        return $res;
    }
    private function getClass(){
        $prefix = '\App\Services\\';
        $class = !empty($this->class) ? $this->class :  ApiRequest::DEFAULTSERVICE;
        $class = ucfirst(strtolower($class));
        $serviceName = '{ServiceName}Connector';
        $serviceName = str_replace('{ServiceName}',$class,$serviceName);
        if(!file_exists(dirname(__FILE__).'/'.$serviceName.'.php')){
            $defaultClass = ucfirst(strtolower(ApiRequest::DEFAULTSERVICE));
            $serviceName = str_replace($class,$defaultClass,$serviceName);
        }
        $serviceName = $prefix.$serviceName;
        return $serviceName;
    }
}