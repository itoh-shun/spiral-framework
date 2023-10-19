<?php

namespace framework\Http\Middleware;

use framework\Exception\ClassNotFoundException;
use framework\Http\Request;
use framework\Support\ServiceProvider;

/**
 * Trait MiddlewareTrait
 *
 * @package App\Http\Middleware
 */
trait MiddlewareTrait
{
    /**
     * @var string[]
     */
    private array $middlewares = [];

    /**
     * @var string[]
     */
    private static array $groupMiddlewares = [];

    private $container;

    /**
     * Add middleware.
     *
     * @param string|array $middleware
     *
     * @return $this
     */
    final public function middleware($middleware): self
    {
        if (is_array($middleware)) {
            $this->middlewares = array_unique(
                array_merge($this->middlewares, $middleware)
            );
        }

        if (is_string($middleware)) {
            $this->middlewares = array_unique(
                array_merge($this->middlewares, [$middleware])
            );
        }

        return $this;
    }

    /**
     * @param Request $request
     */
    private function processMiddleware(Request $request, ServiceProvider $serviceProvider, array $vars = []): bool
    {

        foreach (
            $this->createTokens($request)
            as $exploded_uri_pattern => $exploded_uri
        ) {
            if ($this->startsWith($exploded_uri_pattern, ':')) {
                $vars[ltrim($exploded_uri_pattern, ':')] = $exploded_uri;
            }
        }

        foreach ($this->middlewares as $middleware) {
            $instance = new $middleware($request, $serviceProvider);
            if (!$instance->process($vars)) {
                return false;
            }
        }

        return true;
    }

    // eazy DI
    public function service($instance)
    {
        if (get_class($instance) === false) {
            throw new ClassNotFoundException('Class is Not Found');
        }
        $this->service[] = $instance;
        return $this;
    }

    final public static function middlewares($middleware, callable $func)
    {
        if (is_array($middleware)) {
            self::$groupMiddlewares = array_unique(
                array_merge(self::$groupMiddlewares, $middleware)
            );
        }

        if (is_string($middleware)) {
            self::$groupMiddlewares = array_unique(
                array_merge(self::$groupMiddlewares, [$middleware])
            );
        }

        $func();

        if (is_array($middleware)) {
            self::$groupMiddlewares = array_diff(
                self::$groupMiddlewares,
                $middleware
            );
        }

        if (is_string($middleware)) {
            self::$groupMiddlewares = array_diff(self::$groupMiddlewares, [
                $middleware,
            ]);
        }
    }
}
