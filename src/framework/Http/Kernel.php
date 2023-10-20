<?php

namespace framework\Http;

use Error;
use ErrorException;
use Exception;
use framework\Application;
use framework\Exception\ExceptionHandler;
use framework\Routing\Router;
use framework\Service\ServiceProvider;

class Kernel
{
    /**
     * @var Application
     */
    private $app;
    private Router $router;
    private ExceptionHandler $exceptionHandler;

    public function __construct(
        Application $app,
        Router $router,
        ExceptionHandler $exceptionHandler
    ) {
        $this->app = $app;
        $this->router = $router;
        $this->exceptionHandler = $exceptionHandler;
    }

    final public function handle(Request $request)
    {
        $this->app->boot();
        $this->router::setServiceProvider($this->app->serviceProvider);
        // add global middleware.
        // $this->router->middleware($this->middlewares);
        try {
            return $this->router->dispatch($request);
        } catch (Exception $exception) {
            return $this->exceptionHandler->render($request, $exception);
        }
    }
}
