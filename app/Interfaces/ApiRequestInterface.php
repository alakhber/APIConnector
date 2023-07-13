<?php
namespace App\Interfaces;
interface ApiRequestInterface
{
    public function build();
    public function send();
    public function setHeaders(array $headers);
    public function setRedirect( $redirect);
    public function setAuth($auth);
    public function setBody($body);
    public function setCert( $cert, $password);
    public function setCookies($cookies);
    public function setConnectTimeout( $second);
    public function setDebug($debug);
    public function setDecodeContent( $decodeContent);
    public function setDelay( $delay);
    public function setExpect( $expect);
    public function setIpResolve( $ipResolve);
    public function setFormParams( $formParams);
    public function setHttpErrors( $httpErrors);
    public function setIdnConversion( $idnConversion);
    public function setJson($json);
    public function setMultipart( $multipart);
    public function setOnHeaders( $onHeaders);
    public function setProxy( $proxy);
    public function setQuery($query);
    public function setReadTimeout( $readTimeout);
    public function setSink($sink);
    public function setSslKey( $sslKey);
    public function setStream( $stream);
    public function setSynchronous( $synchronous);
    public function setVerify($verify);
    public function setTimeout( $timeout);
    public function setVersion($version);
    public function setUserAgent( $userAgent);
    public function setReferer( $referer);
}