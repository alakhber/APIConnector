<?php

namespace App\Services;

use Exception;

class ApiConnector{
    protected string $url; //
    protected array $methods = ['POST','GET','DELETE','PUT','PATCH'];
    protected string $method ; //
    protected array $headers ; //
    protected bool $redirect = false; //
    protected $auth ; // 
    protected $body ; // 
    protected array $cert ; // 
    protected $cookies ; //
    protected float $connectTimeout = 0 ; //
    protected $debug ; //
    protected $decodeContent; // 
    protected $delay ; // ?
    protected $expect ; // ?
    protected string $ipResolve ; //
    protected array $formParams ;  //
    protected bool $httpErrors ; //
    protected bool $idnConversion ; //
    protected $json ; // ?
    protected array $multipart ; 
    protected $onHeaders ; 
    protected $proxy ; 
    protected $query ; 
    protected float $readTimeout = 3.14;
    protected $sink ;  //
    protected $sslKey ; 
    protected bool $stream = false;
    protected bool $synchronous ;
    protected $verify = null ;  //
    protected float $timeout = 0; //
    protected $version ; //
    protected string $userAgent ; //
    protected string $referer ; //

    public function __construct(string $url,string $method){
        // if(!in_array(strtoupper($method),$this->methods)) throw new Exception('Invalid method',404);
        $this->url = $url;
        $this->method = strtoupper($method);
        $this->userAgent = $_SERVER['HTTP_USER_AGENT'];
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
    public function setRedirect(bool $redirect){
        $this->redirect = $redirect;
        return $this;
    }
    public function setAuth(array|string|null $auth){
        $this->auth = $auth;
        return $this;
    }
    public function setBody($body){
        $this->body = $body;
        return $this;
    }
    public function setCert(string $cert,string $password){
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
    public function setConnectTimeout(float $second){
        $this->connectTimeout = $second;
        return $this;
    }
    public function setDebug($debug){
        $this->debug = $debug;
        return $this;
    }
    public function setDecodeContent(string|bool $decodeContent){
        $this->decodeContent = $decodeContent;
        return $this;
    }
    public function setDelay(float|int $delay){
        $this->delay = $delay;
        return $this;
    }
    public function setExpect(bool|int $expect){
        $this->expect = $expect;
        return $this;
    }
    public function setIpResolve(string $ipResolve){
        $this->ipResolve = $ipResolve;
        return $this;
    }
    public function setFormParams(array $formParams){
        $this->formParams = $formParams;
        return $this;
    }
    public function setHttpErrors(bool $httpErrors){
        $this->httpErrors = $httpErrors;
        return $this;
    }
    public function setIdnConversion(bool|int $idnConversion){
        $this->idnConversion = $idnConversion;
        return $this;
    }
    public function setJson( $json){
        $json = json_decode($json,true);
        if(is_null($json)) throw new Exception('Invalid Json');
        $this->json = json_encode($json);
        return $this;
    }
    public function setMultipart(array $multipart){
        $this->multipart = $multipart;
        return $this;
    }
    public function setOnHeaders(array $onHeaders){
        $this->onHeaders = $onHeaders;
        return $this;
    }
    public function setProxy(array|string $proxy){
        $this->proxy = $proxy;
        return $this;
    }
    public function setQuery(array|string $query){
        $this->query = $query;
        return $this;
    }
    public function setReadTimeout(float $readTimeout){
        $this->readTimeout = $readTimeout;
        return $this;
    }
    public function setSink($sink){
        $this->sink = $sink;
        return $this;
    }
    public function setSslKey(array|string $sslKey){
        $this->sslKey = $sslKey;
        return $this;
    }
    public function setStream(bool $stream){
        $this->stream = $stream;
        return $this;
    }
    public function setSynchronous(bool $synchronous){
        $this->synchronous = $synchronous;
        return $this;
    }
    public function setVerify(bool|string $verify){
        $this->verify = $verify;
        return $this;
    }
    public function setTimeout(float $timeout){
        $this->timeout = $timeout;
        return $this;
    }
    public function setVersion(float|string $version){
        $this->version = $version;
        return $this;
    }
    public function setUserAgent(string $userAgent){
        $this->userAgent = $userAgent;
        return $this;
    }
    public function setReferer(string $referer){
        $this->referer = $referer;
        return $this;
    }
    
}