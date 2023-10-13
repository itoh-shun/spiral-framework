<?php

namespace framework\Http;

use framework\Http\Middleware\MiddlewareInterface;
use framework\Http\Request;

class Controller
{
    public Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }
    // ログの出力
    public function logging($message, string $file_name = 'app.log')
    {
        //$Logger = new Logger('logger');
        //$Logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/' . $file_name, Logger::INFO));
        //$Logger->addInfo($message);
    }

    protected function middleware($vars, MiddlewareInterface $middleware)
    {
        return $middleware->process($vars);
    }
}
