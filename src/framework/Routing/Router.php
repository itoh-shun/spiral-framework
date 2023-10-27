<?php

namespace framework\Routing;

use Closure;
use Exception;
use framework\Exception\NotFoundException;
use framework\Http\Middleware\MiddlewareRouterTrait;
use framework\Http\Middleware\PrefixTrait;
use framework\Http\Request;
use framework\Support\ServiceProvider;
use Response;

class Router
{
    use MiddlewareRouterTrait;
    use PrefixTrait;

    public static array $routes = [];

    private static ?ServiceProvider $serviceProvider;

    /**
     * Router constructor.
     *
     * @param ServiceProvider $container
     */
    public function __construct()
    {

    }

    public static function setServiceProvider(ServiceProvider $serviceProvider)
    {
        self::$serviceProvider = $serviceProvider;
    }

    /**
     * @param string         $method
     * @param string         $pass
     * @param array|Closure $handler
     *
     * @return Route
     */
    final public static function map(
        string $method,
        string $pass,
        $handler
    ): Route {

        if(!empty(self::$prefix)) {
            $pass = (ltrim($pass, '/') === '') ? self::$prefix : self::$prefix.'/'.ltrim($pass, '/');
        }
        $route = new Route($method, $pass, $handler);

        self::$routes[] = $route;

        return $route->middleware(self::$groupMiddlewares);
    }

    public function url(string $routeName, array $params = []): string {
        foreach ($this->routes as $route) {
            if ($route->equalAlias($routeName)) {
                // ルート名が一致するルートを見つけたら、URLを生成して返す
                return $route->generatePath($params);
            }
        }
        throw new Exception("Route with the name {$routeName} not found.");
    }

    final public static function fetchAlias(string $alias, array $vars = [])
    {
        foreach (self::$routes as $route) {
            if ($route->equalAlias($alias)) {
                return $route->generatePath($vars);
            }
        }
        return null;
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    final public function dispatch(Request $request, $isMethodCheck = true)
    {
        foreach (self::$routes as $route) {
            if ($route->processable($request, $isMethodCheck)) {
                $route->middleware($this->middlewares);
                $result = $route->process($request, $route->service, self::$serviceProvider);

                if ($result === false) {
                    continue;
                }
                return $result;
            }
        }

        throw new NotFoundException('Not Found.', 404);
    }

    public static function redirect($uri, Request $request)
    {
        $request->setRequestUri($uri);
        $router = new Router();
        return $router->dispatch($request, false);
    }

    public static function metaRedirect($path, Request $request , $paramsToKeep = [])
    {
        $baseUrl = strtok($_SERVER['REQUEST_URI'], '?');
        // 現在のURLからクエリパラメータを取得
        $currentParams = $request->all();

        // 引き継ぐべきパラメータを保持するための配列
        $paramsToInclude = [];

        // 特定のパラメータのみを新しいURLに含める
        foreach ($paramsToKeep as $param) {
            if (isset($currentParams[$param])) {
                $paramsToInclude[$param] = $currentParams[$param];
            }
        }

        // _path パラメータを新しいパスで更新
        $paramsToInclude[$request::getPathKey()] = $path;

        // クエリ文字列を生成
        $queryString = http_build_query($paramsToInclude);

        // 新しいURLを生成
        $url = $baseUrl . '?' . $queryString;

        // リダイレクト実行
        echo "<meta http-equiv='refresh' content='0;url={$url}'>";
        exit;
    }

    public static function abort(int $code, string $message = '')
    {
        if ($message == '') {
            switch ($code) {
                case 404:
                    $message = 'Not Found';
                    break;
                case 403:
                    $message = 'Forbidden';
                    break;
            }
        }

        throw new Exception($message, $code);
    }

    public static function resource(string $resource, string $controller): void {
        self::map('GET', "/{$resource}", [$controller , 'index']);
        self::map('GET', "/{$resource}/create", [$controller , 'create']);
        self::map('POST', "/{$resource}", [$controller , 'store']);
        self::map('GET', "/{$resource}/:id", [$controller , 'show']);
        self::map('GET', "/{$resource}/:id/edit", [$controller , 'edit']);
        self::map('PUT', "/{$resource}/:id", [$controller , 'update']);
        self::map('DELETE', "/{$resource}/:id", [$controller , 'destroy']);
    }
}
