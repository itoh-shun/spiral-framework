<?php

namespace framework\SpiralConnecter;

use Exception;
use HttpRequest;
use HttpRequestParameter;
use Logger;
use Spiral;
use SpiralApiRequest;

class SpiralConnecter implements SpiralConnecterInterface
{
    private $apiCommunicator;

    public static ?Logger $logger = null;

    public function __construct($spiral)
    {

        if(!RateLimiter::isRequestAllowed()) {
            throw new Exception('Access frequency limit exceeded. scope is all api.', 801);
        }

        $this->apiCommunicator = $spiral->getSpiralApiCommunicator();
    }

    public function request(
        XSpiralApiHeaderObject $header,
        HttpRequestParameter $httpRequestParameter
    ) {
        if(class_exists('SpiralApiRequest')) {
            $request = new \SpiralApiRequest();
        }

        foreach ($httpRequestParameter->toArray() as $key => $val) {
            $request->put($key, $val);
        }

        $response = $this->apiCommunicator->request(
            $header->func(),
            $header->method(),
            $request
        );

        if ($response->getResultCode() != 0) {
            throw new Exception(
                $response->getMessage(),
                $response->getResultCode()
            );
        }
        RateLimiter::addRequest();
        return $response->entrySet();
    }

    public function bulkRequest(
        XSpiralApiHeaderObject $header,
        array $httpRequestParameters
    ) {
        $result = [];
        $log = [];
        foreach ($httpRequestParameters as $key => $httpRequestParameter) {
            if ($httpRequestParameter instanceof HttpRequestParameter) {
                $res = $this->request($header, $httpRequestParameter);
                array_merge($result, $res);
            }
        }

        return $result;
    }
}
