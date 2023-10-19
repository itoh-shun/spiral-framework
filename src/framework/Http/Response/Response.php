<?php

namespace framework\Http;

class Response
{
    protected $content;
    protected $statusCode;
    protected $message;
    protected $requestPath;

    public function __construct($content = '', $statusCode = 200, $message = '', $requestPath = '')
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->requestPath = $requestPath;
    }

    public function getContent()
    {
        $responseData = [
            'status' => (string) $this->statusCode,
            'data' => $this->content,
            'message' => $this->message,
            'request_path' => $this->requestPath
        ];

        return json_encode($responseData);
    }

    public function send()
    {
        echo $this->getContent();
    }

    public static function json($data, $statusCode = 200, $message = '', $requestPath = '')
    {
        return new self($data, $statusCode, $message, $requestPath);
    }
}
