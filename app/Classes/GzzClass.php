<?php

namespace App\Classes;
use App\Services\ApiConnector;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client as GuzzleClient;

class GzzClass extends ApiConnector{
    private $options;
    private $client;

    public function __construct($url,$method) {
        parent::__construct($url, $method);
        $this->client = new GuzzleClient();
    }

    public function build(){
        $headers = [];
        if (count(array_filter([!empty($this->formParams) and count($this->formParams) > 0, !is_null($this->body), !empty($this->json), !empty($this->multipart) and count($this->multipart) > 0])) >= 2) {
            throw new Exception('Only one is accepted Forms', 403);
        }

        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $header) {
                $headers[$key] = $header;
            }
        }
        if (!empty($this->formParams) and count($this->formParams) > 0) {
            $this->options['form_params']=$this->formParams;
        }
        if (!empty($this->body)) {
            $this->options['body'] =  $this->body;
        }
        if (!is_null($this->json)) {
            $this->options['json'] =  json_decode($this->json,true);
        }
        if (!empty($this->multipart) and count($this->multipart) > 0) {
            $this->options['multipart'] =  $this->multipart;
        }
        if (!empty($this->cert) > 0 and is_array($this->cert)) {
                if (!file_exists($this->cert['cert'])) throw new Exception('File Not Found', 404);
                $this->options['cert'] = [$this->cert['cert'],$this->cert['password']];
        }
        if (!empty($this->cookies)) {
            $this->options['cookies'] =  $this->cookies;
        }
        if (!empty($this->verify)) {
            $this->options['verify'] =  $this->verify;
        }
        if (!empty($this->connectTimeout)) {
            $this->options['connect_timeout'] =  $this->connectTimeout;
        }
        if (!empty($this->decodeContent)) {
            if(!$this->decodeContent){
                $this->options['headers'] =  ['Accept-Encoding' => 'gzip'];
            }
            $this->options['decode_content'] =  $this->decodeContent;
        }
        if (!empty($this->referer)) {
            $this->options['connect_timeout'] =  $this->connectTimeout;
        }
        if (!empty($this->auth)) {
            $this->options['auth'] =  $this->auth;
        }
        if (!empty($this->sink)) {
            $this->options['sink'] =  $this->sink;
        }
        if (!empty($this->ipResolve)) {
            if ($this->ipResolve != 'v4' || $$this->ipResolve != 'v6') throw new Exception('Invalid Version', 404);
            $this->options['force_ip_resolve'] = $this->ipResolve;
        }
        if (!empty($this->httpErrors)) {
            $this->options['http_errors'] = $this->httpErrors;
        }
        if (!empty($this->httpErrors)) {
            $this->options['http_errors'] = $this->httpErrors;
        }
        if (!empty($this->idnConversion)) {
            $this->options['idn_conversion'] = $this->idnConversion;
        }
        if (!empty($this->idnConversion)) {
            $this->options['idn_conversion'] = $this->idnConversion;
        }
        if (count($headers) > 0) {
            $this->options['headers'] = $headers;
           
        }
      return $this;
    }
    public function run()
    {
        $res = $this->client->request(strtoupper($this->method), $this->url, [$this->options]);
        dd($res->getBody()->getContents());
    }
    
}