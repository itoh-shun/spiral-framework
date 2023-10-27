<?php

namespace framework\Http;

use Auth;
use framework\Http\Session\RequestSession;
use framework\Library\SiValidator;

/**
 * Class Request
 *
 * @package App\Http\Message
 */
class Request
{
    private static self $instance;
    private array $request = [];
    private array $server = [];
    private ?RequestSession $session = null;
    private $user;

    private static string $pathkey = '_path';

    public function __construct()
    {
        $this->request = $_REQUEST;
        $this->server = $_SERVER;
        $this->session = new RequestSession();
        self::$instance = $this;
    }

    public function isSsl(): bool
    {
        return isset($this->server['HTTPS']) && $this->server['HTTPS'] === 'on';
    }

    public function getHost(): string
    {
        return $this->server['SERVER_NAME'];
    }

    public function setUser(Auth $auth)
    {
        $this->user = $auth;
    }

    public function user()
    {
        return $this->user;
    }

    public static function setPathKey(string $pathkey)
    {
        self::$pathkey = $pathkey;
    }

    public static function getPathKey(): string
    {
        return self::$pathkey;
    }
    public function getRequestUri(): string
    {
        //return $_SERVER['REQUEST_URI'];

        if (!isset($this->request[self::$pathkey])) {
            return '';
        }

        $path = $this->request[self::$pathkey];

        if (is_array($path)) {
            $path = $path[count($path) - 1];
        }

        return ltrim($path, '/');
    }

    public function setRequestUri(string $path)
    {
        $this->set(self::$pathkey, $path);
    }

    public function getQueryParams(): array
    {
        return $_GET;
    }

    public function getRequestBody(): array
    {
        return self::requestUrldecode($this->request);
    }

    private static function requestUrldecode($v)
    {
        if (is_array($v)) {
            $result = [];
            foreach ($v as $key => $value) {
                $result[$key] = self::requestUrldecode($value);
            }
            return $result;
        }
        return rawurldecode(rawurldecode(urlencode($v)));
    }

    public function getMethod(): string
    {
        if (empty($this->get('_method'))) {
            //return $this->server['REQUEST_METHOD'];
            return 'get';
        }
        return $this->get('_method');
    }
    
    public function setMethod($method): void
    {
        $this->set('_method',$method);
    }

    public function get($key, $default = '')
    {
        $value = $this->request[$key] ?? $default;
        return self::requestUrldecode($value);
    }

    public function only(array $keys)
    {
        $val = [];
        foreach ($keys as $k) {
            $val[$k] = $this->get($k, '');
        }

        return $val;
    }

    public function except(array $keys)
    {
        $val = $this->all();
        foreach ($keys as $k) {
            unset($val[$k]);
        }

        return $val;
    }

    public function all()
    {
        return (array) $this->request;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    public function set($key, $val)
    {
        $this->request[$key] = $val;
    }

    public function merge(array $value)
    {
        $this->request = array_merge($this->request, $value);
    }

    public function is(string $pattern)
    {
        $pattern = ltrim($pattern, '/');

        $pattern = str_replace('.', '/', $pattern);
        $pattern = str_replace('*', '.*', $pattern);
        $pattern = explode('/', $pattern);
        $pattern = implode('\/', $pattern);

        return preg_match('/^' . $pattern . '$/', $this->getRequestUri()) === 1;
    }

    public function validate(array $rules, $labels = [])
    {
        $values = [];
        foreach ($rules as $key => $rule) {
            $values[$key] = $this->get($key, $this->session()->get($key, ''));
        }

        return SiValidator::make($values, $rules, $labels);
    }

    public function session()
    {
        return $this->session;
    }

    public static function queryBuilder(
        array $query = [],
        array $removeQuery = []
    ) {
        $query = array_merge(self::$instance->getQueryParams(), $query);
        foreach ($removeQuery as $key) {
            unset($query[$key]);
        }
        return http_build_query($query);
    }

    public function updateQueryParameter($key, $value) {
        // 現在のURLとクエリストリングを取得
        $url = $_SERVER['REQUEST_URI'];
        $parsedUrl = parse_url($url);
        $query = [];
        if (isset($parsedUrl['query'])) {
            parse_str($parsedUrl['query'], $query);
        }
    
        // クエリの中の特定のキーの値を書き換え
        $query[$key] = $value;
    
        // 書き換えたクエリをURLに戻す
        $newQueryStr = http_build_query($query);
        $newUrl = $parsedUrl['path'] . '?' . $newQueryStr;
    
        return $newUrl;
    }
}
