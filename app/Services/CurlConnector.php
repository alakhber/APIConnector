<?php

namespace App\Services;

use App\Interfaces\ApiRequestInterface;
use App\Services\ApiRequest;
use Exception;

class CurlConnector extends ApiRequest implements ApiRequestInterface
{
    private $curl;
    private $stderr;
    private
        $curlVersions = [
        CURL_HTTP_VERSION_NONE,
        CURL_HTTP_VERSION_1_0,
        CURL_HTTP_VERSION_1_1,
        CURL_HTTP_VERSION_2_0,
        CURL_HTTP_VERSION_2TLS,
        CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE
    ];
    private $curlOptions = [];
    private $parent;
    public function __construct($parent)
    {
        $this->parent = $parent;
        if (!curl_init()) throw new Exception('cURL Not Initialized !', 401);
        $this->stderr = fopen('php://temp', 'w+');
        $this->curl = curl_init();

    }
    public function build()
    {
        $postFields = null;
        $headers = [];
        $this->parent->redirect = true;
        if (count(array_filter([!empty($this->parent->formParams) and count($this->parent->formParams) > 0, !is_null($this->parent->body), !empty($this->parent->json), !empty($this->parent->multipart) and count($this->parent->multipart) > 0])) >= 2) {
            throw new Exception('Only one is accepted Forms', 403);
        }
        if(!in_array($this->parent->version,$this->curlVersions)) throw new Exception('cURL Version Not Supported',403);
        if (empty($this->parent->version)) {
            $this->curlOptions[CURLOPT_HTTP_VERSION] = phpversion() < 7.62 ?  CURL_HTTP_VERSION_2TLS : CURL_HTTP_VERSION_1_1;
        }
        if (!empty($this->parent->headers)) {
            foreach ($this->parent->headers as $key => $header) {
                $headers[$key] = $header;
            }
        }

        $this->curlOptions[CURLOPT_USERAGENT] = $this->parent->userAgent;
        $this->curlOptions[CURLOPT_URL] = trim($this->parent->url);
        $this->curlOptions[CURLOPT_CUSTOMREQUEST] = trim(strtoupper($this->parent->method));
        $this->curlOptions[CURLOPT_FOLLOWLOCATION] = $this->parent->redirect;
        $this->curlOptions[CURLOPT_TIMEOUT] = $this->parent->timeout;
        $this->curlOptions[CURLOPT_ENCODING] = '';
        $this->curlOptions[CURLOPT_VERBOSE] = true;
        $this->curlOptions[CURLOPT_FAILONERROR] = true;
        $this->curlOptions[CURLOPT_STDERR] = $this->stderr;
        $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
        $this->curlOptions[CURLOPT_MAXREDIRS] = 10;
        if ($this->parent->method == 'POST') {
            $this->curlOptions[CURLOPT_POST] = true;
        }
        if (!empty($this->parent->formParams) and count($this->parent->formParams) > 0) {
            $postFields = $this->parent->formParams;
        }
        if (!empty($this->parent->body)) {
            $postFields =  $this->parent->body;
        }
        if (!empty($this->parent->json)) {
            $postFields =  $this->parent->json;
            $headers['Content-Type'] = 'application/json';
        }
        if (!empty($this->parent->multipart) and count($this->parent->multipart) > 0) {
            $postFields =  $this->parent->multipart;
        }
        if (!empty($this->parent->cert) > 0 and is_array($this->parent->cert)) {
            if (!empty($this->parent->cert['cert'])) {
                if (!file_exists($this->parent->cert['cert'])) throw new Exception('File Not Found', 404);
                $this->curlOptions[CURLOPT_SSLCERT] = $this->parent->cert['cert'];
            }
            if (!empty($this->parent->cert['password'])) {
                $this->curlOptions[CURLOPT_SSLCERTPASSWD] = $this->parent->cert['password'];
            }
        }
        if (!empty($this->parent->cookies)) {
            $this->curlOptions[CURLOPT_COOKIEFILE] = $this->parent->cookies;
            $this->curlOptions[CURLOPT_COOKIEJAR] = $this->parent->cookies;
        }
        if (!is_null($this->parent->verify)) {
            $this->curlOptions[CURLOPT_SSL_VERIFYHOST] = $this->parent->verify;
            $this->curlOptions[CURLOPT_SSL_VERIFYPEER] = $this->parent->verify;
        }
        if ($this->parent->connectTimeout != 0.0) {
            $this->curlOptions[CURLOPT_CONNECTTIMEOUT] = $this->parent->connectTimeout;
        }
        if (!empty($this->parent->decodeContent)) {
            $this->curlOptions[CURLOPT_HEADER] = false;
            if ($this->parent->decodeContent === false) {
                $headers['Accept-Encoding'] = 'gzip';
            } elseif ($this->parent->decodeContent == 'gzip') {
                $this->curlOptions[CURLOPT_ENCODING] = 'gzip';
            }
        }
        if (!empty($this->parent->referer)) {
            $this->curlOptions[CURLOPT_REFERER] = $this->parent->referer;
        }
        if (!empty($this->parent->auth)) {
            $this->curlOptions[CURLOPT_USERPWD] = $this->parent->auth;
        }
        if (!empty($this->parent->sink)) {
            $this->curlOptions[CURLOPT_FILE] = $this->parent->sink;
        }
        if (!empty($this->parent->version)) {
            $this->curlOptions[CURLOPT_HTTP_VERSION] = $this->parent->version;
        }
        if (!empty($this->parent->ipResolve)) {
            if ($this->parent->ipResolve != 'v4' || $$this->parent->ipResolve != 'v6') throw new Exception('Invalid Version ', 404);
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
            $this->curlOptions[CURLOPT_IPRESOLVE] = 'CURL_IPRESOLVE_' . strtoupper($this->parent->version);
        }
        if (!empty($this->parent->httpErrors)) {
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = true;
            $this->curlOptions[CURLOPT_FAILONERROR] = $this->parent->httpErrors;
        }
        if (!empty($this->parent->idnConversion)) {
            $this->curlOptions[CURLOPT_RETURNTRANSFER] = $this->parent->idnConversion;
        }
        if (count($headers) > 0) {
            $head = [];
            foreach ($headers as $key => $header) {
                $head[] = $key . ': ' . $header;
            }
            $this->curlOptions[CURLOPT_HTTPHEADER] = $head;
        }
        if (!is_null($postFields)) {

            $this->curlOptions[CURLOPT_POSTFIELDS] = $postFields;
        }
        curl_setopt_array($this->curl, $this->curlOptions);
        return $this;
    }
    public function send()
    {
        $result = curl_exec($this->curl);
        $this->writeLog($result);
        return $result;
    }
    private function writeLog($result)
    {
        if($result===FALSE){
            $data['error_no'] = curl_errno($this->curl);
            $data['error_message'] = curl_error($this->curl);
            rewind($this->stderr);
            $verboseLog = stream_get_contents($this->stderr);
            $log = "cURL error (#" . curl_errno($this->curl) . "): " . htmlspecialchars(curl_error($this->curl)) . PHP_EOL
                . htmlspecialchars(curl_error($this->curl)) . PHP_EOL . "\n"
                . "Verbose information: " . PHP_EOL . PHP_EOL . "\n"
                . $verboseLog;
            $data['log'] = $log;
            $stsCode = curl_getinfo($this->curl);
            curl_close($this->curl);
            throw new Exception("Curl Call Error! See extra!", $stsCode['http_code'] !=0 ? $stsCode['http_code'] : 500 , null,null, '', $data);
        }
    }
}