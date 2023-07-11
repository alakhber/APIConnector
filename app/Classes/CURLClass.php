<?php

namespace App\Classes;

use App\Services\ApiConnector;
use Exception;

class CURLClass extends ApiConnector
{

    private $curl;
    private $stderr;
    private array 
    $curlVersions = [
        CURL_HTTP_VERSION_NONE,
        CURL_HTTP_VERSION_1_0,
        CURL_HTTP_VERSION_1_1,
        CURL_HTTP_VERSION_2_0,
        CURL_HTTP_VERSION_2TLS,
        CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE
    ]; 

    private array $curlOptions = [];

    public function __construct(string $url, string $method)
    {
        if (!curl_init()) throw new Exception('cURL Not Initialized !', 401);
        $this->stderr = fopen('php://temp', 'w+');
        $this->curl = curl_init();
        parent::__construct($url, $method);
    }

    public function build()
    {
       
        $postFields = null;
       
        $headers = [];
        $this->redirect = true;

        if (count(array_filter([!empty($this->formParams) and count($this->formParams) > 0, !is_null($this->body), !empty($this->json), !empty($this->multipart) and count($this->multipart) > 0])) >= 2) {
            throw new Exception('Only one is accepted Forms', 403);
        }
        if(!in_array($this->version,$this->curlVersions)) throw new Exception('cURL Version Not Supported',403);
        if (empty($this->version)) {
            $this->curlOptions[CURLOPT_HTTP_VERSION] = phpversion() < 7.62 ?  CURL_HTTP_VERSION_2TLS : CURL_HTTP_VERSION_1_1;
        }
        if (!empty($this->headers)) {
            foreach ($this->headers as $key => $header) {
                $headers[$key] = $header;
            }
        }

        $this->curlOptions[CURLOPT_USERAGENT] = $this->userAgent;
        $this->curlOptions[CURLOPT_URL] = $this->url;
        $this->curlOptions[CURLOPT_CUSTOMREQUEST] = strtoupper($this->method);
        $this->curlOptions[CURLOPT_FOLLOWLOCATION] = $this->redirect;
        $this->curlOptions[CURLOPT_STDERR] = $this->stderr;
        $this->curlOptions[CURLOPT_TIMEOUT] = $this->timeout;
        $this->curlOptions[CURLOPT_ENCODING] = '';
        $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $this->curlOptions[CURLOPT_MAXREDIRS] = 10;


        if ($this->method == 'POST') {
            $this->curlOptions[CURLOPT_POST] = true;
        }
        
        if (!empty($this->formParams) and count($this->formParams) > 0) {
            $postFields = $this->formParams;
        }
        if (!empty($this->body)) {
            $postFields =  $this->body;
        }
        if (!is_null($this->json)) {
            $postFields =  $this->json;
            $headers['Content-Type'] = 'application/json';
            $headers['Content-Length'] = strlen($this->json);
        }
        if (!empty($this->multipart) and count($this->multipart) > 0) {
            $postFields =  $this->multipart;
        }
        if (!empty($this->cert) > 0 and is_array($this->cert)) {
            if (!empty($this->cert['cert'])) {
                if (!file_exists($this->cert['cert'])) throw new Exception('File Not Found', 404);
                $this->curlOptions[CURLOPT_SSLCERT] = $this->cert['cert'];
            }
            if (!empty($this->cert['password'])) {
                $this->curlOptions[CURLOPT_SSLCERTPASSWD] = $this->cert['password'];
            }
        }
        if (!empty($this->cookies)) {
            $this->curlOptions[CURLOPT_COOKIEFILE] = $this->cookies;
            $this->curlOptions[CURLOPT_COOKIEJAR] = $this->cookies;
        }
        if (!is_null($this->verify)) {
            $this->curlOptions[CURLOPT_SSL_VERIFYHOST] = $this->verify;
            $this->curlOptions[CURLOPT_SSL_VERIFYPEER] = $this->verify;
        }
        if ($this->connectTimeout != 0.0) {
            $this->curlOptions[CURLOPT_CONNECTTIMEOUT] = $this->connectTimeout;
        }
        if (!empty($this->decodeContent)) {
            $this->curlOptions[CURLOPT_HEADER] = false;
            if ($this->decodeContent === false) {
                $headers['Accept-Encoding'] = 'gzip';
            } elseif ($this->decodeContent == 'gzip') {
                $this->curlOptions[CURLOPT_ENCODING] = 'gzip';
            }
        }
        if (!empty($this->referer)) {
            $this->curlOptions[CURLOPT_REFERER] = $this->referer;
        }
        if (!empty($this->auth)) {
            $this->curlOptions[CURLOPT_USERPWD] = $this->auth;
        }
        if (!empty($this->sink)) {
            $this->curlOptions[CURLOPT_FILE] = $this->sink;
        }
        if (!empty($this->version)) {
            $this->curlOptions[CURLOPT_HTTP_VERSION] = $this->version;
        }
        if (!empty($this->ipResolve)) {
            if ($this->ipResolve != 'v4' || $$this->ipResolve != 'v6') throw new Exception('Invalid Version', 404);
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
            $this->curlOptions[CURLOPT_IPRESOLVE] = 'CURL_IPRESOLVE_V4' . strtoupper($this->version);
        }
        if (!empty($this->httpErrors)) {
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
            $this->curlOptions[CURLOPT_FAILONERROR] = $this->httpErrors;
        }
        if (!empty($this->idnConversion) and $this->idnConversion == false) {
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
        }

        if (count($headers) > 0) {
            $head = [];
            foreach ($headers as $key => $header) {
                $head[] = $key . ': ' . $header;
            }
            dd($head);
            $this->curlOptions[CURLOPT_HTTPHEADER] = $head;
        }
        if (!is_null($postFields)) {
            $this->curlOptions[CURLOPT_POSTFIELDS] = $postFields;
        }
       
        curl_setopt_array($this->curl, $this->curlOptions);
       
        $curlInfo = curl_getinfo($this->curl);
        $e = curl_error($this->curl);
       
       
        return $this;
    }


    public function run()
    {
        $out = curl_exec($this->curl);
        return $out;
    }

    private function writeLog($curl)
    {
        $data['error_no'] = curl_errno($curl);
        $data['error_message'] = curl_error($curl);
        rewind($this->stderr);
        $log = "cURL error (#" . curl_errno($curl) . "): " . htmlspecialchars(curl_error($curl)) . PHP_EOL
            . htmlspecialchars(curl_error($curl)) . PHP_EOL . "\n"
            . "Verbose information: " . PHP_EOL . PHP_EOL . "\n"
            . htmlspecialchars(stream_get_contents($this->stderr));
        $data['log'] = $log;
        // $data['response'] = $res;

        curl_close($curl);
        throw new Exception("Curl Call Error! See extra!", 500);
    }
}
