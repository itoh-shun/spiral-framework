<?php

namespace framework\Http;

use framework\Http\Middleware\MiddlewareInterface;
use framework\Http\Request;
use framework\Support\ServiceProvider;

class Controller
{
    protected Request $request;
    protected ?ServiceProvider $serviceProvider;

    public function __construct(Request $request, ?ServiceProvider $serviceProvider)
    {
        $this->request = $request;
        $this->serviceProvider = $serviceProvider;
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
