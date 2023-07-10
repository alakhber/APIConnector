<?php

namespace App\Classes;

use App\Services\ApiConnector;
use Exception;

class CURLClass extends ApiConnector
{

    private $curl;
    private $stderr;

    private array $curlOptions = [];

    public function __construct(string $url, string $method)
    {
        if (!curl_init()) throw new Exception('cURL Not Initialized !', 401);
        $this->curl = curl_init();
        $this->stderr = fopen('php://temp', 'w+');
        parent::__construct($url, $method);
    }

    public function build()
    {
        $postFields = null;
        $headers = [];
        $this->redirect = true;
        curl_setopt($this->curl,CURLOPT_USERAGENT,$this->userAgent);
        curl_setopt($this->curl,CURLOPT_FOLLOWLOCATION,$this->redirect);
        curl_setopt($this->curl,CURLOPT_STDERR,$this->stderr);
        if ($this->method == 'POST') {
            curl_setopt($this->curl,CURLOPT_POST , true);
        }
        if (!is_null($this->formParams) and count($this->formParams) > 0 and !is_null($this->body)) {
            $postFields = [
                'body' => $this->body,
                'data' => http_build_query($this->formParams)
            ];
        } elseif (!is_null($this->formParams) and count($this->formParams) > 0 and is_null($this->body)) {
            $postFields = $this->formParams;
        } elseif ((is_null($this->formParams) or count($this->formParams) < 1) and !is_null($this->body)) {
            $postFields =  $this->body;
        }
        if (!empty($this->cert) > 0 and is_array($this->cert)) {
            if (!empty($this->cert['cert'])) {
                curl_setopt($this->curl,CURLOPT_SSLCERT, $this->cert['cert']);
            }
            if (!empty($this->cert['password'])) {
                curl_setopt($this->curl,CURLOPT_SSLCERTPASSWD, $this->cert['password']);
            }
        }
        if (!empty($this->cookies)) {
            curl_setopt($this->curl,CURLOPT_COOKIEFILE, $this->cookies);
            curl_setopt($this->curl,CURLOPT_COOKIEJAR, $this->cookies);
            curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);
        }
        if (!is_null($this->verify)) {
            curl_setopt($this->curl,CURLOPT_SSL_VERIFYHOST, $this->verify);
            curl_setopt($this->curl,CURLOPT_SSL_VERIFYPEER, $this->verify);
        }
        if ($this->connectTimeout != 0.0) {
            curl_setopt($this->curl,CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        }
        if (!empty($this->decodeContent)) {
            curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl,CURLOPT_HEADER, false);
            if ($this->decodeContent === false) {
                curl_setopt($this->curl,CURLOPT_HTTPHEADER,'Accept-Encoding: gzip');
                curl_setopt($this->curl,CURLOPT_ENCODING,'');
            } elseif ($this->decodeContent == 'gzip') {
                curl_setopt($this->curl,CURLOPT_ENCODING, 'gzip');
            }
        }
        if ($this->timeout != 0.0) {
            curl_setopt($this->curl,CURLOPT_TIMEOUT, $this->timeout);
        }
        if (!empty($this->referer)) {
            curl_setopt($this->curl,CURLOPT_REFERER, $this->referer);
        }
        if (!empty($this->auth)) {
            curl_setopt($this->curl,CURLOPT_USERPWD, $this->auth);
        }
        if (!empty($this->sink)) {
            curl_setopt($this->curl,CURLOPT_FILE, $this->sink);
        }
        if (!empty($this->version)) {
            curl_setopt($this->curl,CURLOPT_HTTP_VERSION, $this->version);
        }
        if (!empty($this->ipResolve)) {
            if ($this->ipResolve != 'v4' || $$this->ipResolve != 'v6') throw new Exception('Invalid Version', 404);
            curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl,CURLOPT_IPRESOLVE,CURL_IPRESOLVE_V4 . strtoupper($this->version));
        }
        if (!empty($this->httpErrors)) {
            curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->curl,CURLOPT_FAILONERROR, $this->httpErrors);
        }
        if (!empty($this->idnConversion) and $this->idnConversion == false) {
            curl_setopt($this->curl,CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($this->curl,CURLOPT_IDN_CONVERSION,CURL_IDN_DISABLE);
        }
        if (!empty($this->json)) {
            $headers = [];
            $headers['Content-Type'] = 'application/json';
            $headers['Content-Length'] = strlen($this->json);
        }
        if (!is_null($this->headers)) {
            $headers = [];
            foreach ($this->headers['headers'] as $key => $header) {
                $headers[$key] = $header;
            }
        }
        if (count($headers) > 0) {
            $head = [];
            foreach ($headers as $key => $header) {
                $head[] = $key . ':' . $header;
            }
            curl_setopt($this->curl,CURLOPT_HTTPHEADER,  $head);
        }
        if (!is_null($postFields)) {
            curl_setopt($this->curl,CURLOPT_POSTFIELDS, $postFields);
        }
        dd($this->curl);
    }


    public function run()
    {
      
    }

    private function writeLog($res)
    {
        $data['error_no'] = curl_errno($this->curl);
        $data['error_message'] = curl_error($this->curl);
        rewind($this->stderr);
        $log = "cURL error (#" . curl_errno($this->curl) . "): " . htmlspecialchars(curl_error($this->curl)) . PHP_EOL
            . htmlspecialchars(curl_error($this->curl)) . PHP_EOL . "\n"
            . "Verbose information: " . PHP_EOL . PHP_EOL . "\n"
            . htmlspecialchars(stream_get_contents($this->stderr));
        $data['log'] = $log;
        $data['response'] = $res;

        curl_close($this->curl);

        throw new Exception("Curl Call Error! See extra!", 500);
    }
}
