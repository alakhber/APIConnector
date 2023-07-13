<?php

namespace App\Services;

use App\Services\ApiRequest;
use App\Interfaces\ApiRequestInterface;
use Exception;
use GuzzleHttp\Client as HttpClient;

class GuzzleConnector extends ApiRequest implements ApiRequestInterface
{
    private $options;
    private $client;
    private $parent;


    public function __construct($parent)
    {
        $this->parent = $parent;
        $this->client = new HttpClient();
    }


    public function build()
    {
        $headers = [];
        if (count(array_filter([!empty($this->parent->formParams) and count($this->parent->formParams) > 0, !is_null($this->parent->body), !empty($this->parent->json), !empty($this->parent->multipart) and count($this->parent->multipart) > 0])) >= 2) {
            throw new Exception('Only one is accepted Forms ', 403);
        }
        if (!empty($this->parent->headers)) {
            foreach ($this->parent->headers as $key => $header) {
                $headers[$key] = $header;
            }
        }
        if (!empty($this->parent->formParams) and count($this->parent->formParams) > 0) {
            $this->options['form_params'] = $this->parent->formParams;
        }
        if (!empty($this->parent->body)) {
            $this->options['body'] =  $this->parent->body;
        }
        if (!is_null($this->parent->json)) {
            $this->options['json'] =  json_decode($this->parent->json, true);
        }
        if (!empty($this->parent->multipart) and count($this->parent->multipart) > 0) {
            $this->options['multipart'] =  $this->parent->multipart;
        }
        if (!empty($this->parent->cert) > 0 and is_array($this->parent->cert)) {
            if (!file_exists($this->parent->cert['cert'])) throw new Exception('File Not Found', 404);
            $this->options['cert'] = [$this->parent->cert['cert'], $this->parent->cert['password']];
        }
        if (!empty($this->parent->cookies)) {
            $this->options['cookies'] =  $this->parent->cookies;
        }
        if (!empty($this->parent->verify)) {
            $this->options['verify'] =  $this->parent->verify;
        }
        if (!empty($this->parent->connectTimeout)) {
            $this->options['connect_timeout'] =  $this->parent->connectTimeout;
        }
        if (!empty($this->parent->decodeContent)) {
            if (!$this->parent->decodeContent) {
                $this->options['headers'] =  ['Accept-Encoding' => 'gzip'];
            }
            $this->options['decode_content'] =  $this->parent->decodeContent;
        }
        if (!empty($this->parent->referer)) {
            $this->options['connect_timeout'] =  $this->parent->connectTimeout;
        }
        if (!empty($this->parent->auth)) {
            $this->options['auth'] =  $this->parent->auth;
        }
        if (!empty($this->parent->sink)) {
            $this->options['sink'] =  $this->parent->sink;
        }
        if (!empty($this->parent->ipResolve)) {
            if ($this->parent->ipResolve != 'v4' || $$this->parent->ipResolve != 'v6') throw new Exception('Invalid Version', 404);
            $this->options['force_ip_resolve'] = $this->parent->ipResolve;
        }
        if (!empty($this->parent->httpErrors)) {
            $this->options['http_errors'] = $this->parent->httpErrors;
        }
        if (!empty($this->parent->idnConversion)) {
            $this->options['idn_conversion'] = $this->parent->idnConversion;
        }
        if (count($headers) > 0) {
            $this->options['headers'] = $headers;
        }
        return $this;
    }
    public function send()
    {
        try {
            $response = $this->client->{$this->parent->method}($this->parent->url,$this->options);
            return $response->getBody()->getContents();
        }catch (\Throwable  $th){
            throw new Exception($th);
        }
    }
}