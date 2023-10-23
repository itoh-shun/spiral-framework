<?php

namespace framework\Routing;

use Closure;
use framework\Exception\ClassNotFoundException;
use framework\Exception\NotFoundException;
use framework\Http\Middleware\MiddlewareTrait;
use framework\Http\Request;
use framework\Http\Response;
use framework\Support\ServiceProvider;

/**
 * Class Route
 *
 * @package App\Routing
 */
class Route
{
    use MiddlewareTrait;

    private string $method;
    private string $pass;
    private $handler;
    private string $action;
    private string $alias = '';
    public array $service = [];
    /**
     * Route constructor.
     *
     * @param string             $method
     * @param string             $pass
     * @param array|Closure   $handler
     */

    public function __construct(string $method, string $pass, $handler)
    {
        $this->method = $method;
        $this->pass = $pass;
        if (is_array($handler)) {
            $this->handler = $handler[0];
            $this->action = $handler[1];
        } else {
            $this->handler = $handler;
        }
    }

    final public function processable(
        Request $request,
        $isMethodCheck = true
    ): bool {
        if (
            $isMethodCheck &&
            \mb_strtolower($request->getMethod()) !==
            \mb_strtolower($this->method)
        ) {
            return false;
        }
        if (($tokens = $this->createTokens($request)) === []) {
            return false;
        }

        foreach ($tokens as $exploded_uri_pattern => $exploded_uri) {
            if ($this->startsWith($exploded_uri_pattern, ':')) {
                continue;
            }

            if ($exploded_uri_pattern !== $exploded_uri) {
                return false;
            }
        }

        return true;
    }

    final public function process(Request $request, $service, ServiceProvider $serviceProvider)
    {
        $vars = [];

        foreach (
            $this->createTokens($request)
            as $exploded_uri_pattern => $exploded_uri
        ) {
            if ($this->startsWith($exploded_uri_pattern, ':')) {
                $vars[ltrim($exploded_uri_pattern, ':')] = $exploded_uri;
            }
        }

        if ($this->processMiddleware($request, $serviceProvider, $vars) === false) {
            return false;
        }

        /*
        $handler = is_string($this->handler)
            ? $this->serviceProvider->get($this->handler)
            : $this->handler;
        */
        $handler = $this->handler;

        if (!is_string($handler)) {
            if ($service == null) {
                return $handler($vars);
            }
            return $handler($vars, ...$service);
        }

        if (!is_string($this->action)) {
            throw new NotFoundException('not found');
        }

        $action = $this->action;
        //__constract Method実行
        $instance = new $handler($request, $serviceProvider);

        if ($service == null) {
            return $instance->$action($vars);
        }
        return $instance->$action($vars, ...$service);
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    private function createTokens(Request $request): array
    {
        $exploded_uri_patterns = explode('/', ltrim($this->pass, '/'));
        $exploded_uris = explode('/', ltrim($request->getRequestUri(), '/'));
        if (count($exploded_uri_patterns) !== count($exploded_uris)) {
            return [];
        }

        return array_combine($exploded_uri_patterns, $exploded_uris);
    }

    // eazy DI
    public function service(...$instance)
    {
        if (!is_array($instance)) {
            $instance = [$instance];
        }
        foreach ($instance as $i) {
            if (get_class($i) === false) {
                throw new ClassNotFoundException('Class is Not Found');
            }
            /*
            if (! class_exists(get_class($instance))) {
                throw new ClassNotFoundException('Class is Not Found');
            }
            */
            $this->service[] = $i;
        }

        return $this;
    }

    public function startsWith($haystack, $needle)
    {
        return strpos($haystack, $needle) === 0;
    }

    public function name(string $alias)
    {
        $this->alias = $alias;
        return $this;
    }

    public function equalAlias(string $alias)
    {
        return $this->alias === $alias;
    }

    public function generatePath($vars = [])
    {
        $path = [];
        foreach (
            explode('/', ltrim($this->pass, '/'))
            as $key => $exploded_uri_pattern
        ) {
            if ($this->startsWith($exploded_uri_pattern, ':')) {
                $path[] = $vars[ltrim($exploded_uri_pattern, ':')];
            } else {
                $path[] = $exploded_uri_pattern;
            }
        }

        return implode('/', $path);
    }
}
