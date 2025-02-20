<?php

namespace App\Service; use Symfony\Component\HttpFoundation\RequestStack;

;

class PathService
{
    public function __construct(
        protected RequestStack $request,
    ){}

    public function getURI(){
        $request = $this->request->getCurrentRequest();
        $fullHttp = $request->getUri();
        return $fullHttp;
    }

    public function getScheme(){
        $request = $this->request->getCurrentRequest();
        $fullHttp = $request->getUri();
        $scheme = parse_url($fullHttp, PHP_URL_SCHEME);
        return $scheme;
    }

    public function getPort(){
        $request = $this->request->getCurrentRequest();
        $fullHttp = $request->getUri();
        $port = parse_url($fullHttp, PHP_URL_PORT);
        return $port;
    }

    public function getHost(){
        $request = $this->request->getCurrentRequest();
        $fullHttp = $request->getUri();
        $host = parse_url($fullHttp, PHP_URL_HOST);
        return $host;
    }

}