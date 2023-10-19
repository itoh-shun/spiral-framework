<?php

namespace framework\Http\Middleware;

use framework\Exception\ClassNotFoundException;
use framework\Http\Request;

/**
 * Trait MiddlewareTrait
 *
 * @package App\Http\Middleware
 */
trait MiddlewareRouterTrait
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
