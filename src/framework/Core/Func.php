<?php

use Collection\Collection;
use framework\Facades\Gate;
use framework\Http\Request;
use framework\Http\View;
use framework\Library\BladeLikeEngine\BladeLikeView;
use framework\Routing\Router;

function view(string $template, array $param = [], bool $filter = false): View
{
    return new BladeLikeView($template, $param, $filter);
}

function response($content = '', $statusCode = 200, $message = '', $requestPath = '')
{
    return new \framework\Http\Response($content, $statusCode, $message, $requestPath);
}

function html($string = '')
{
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function gate(string $pass, ...$instances)
{
    return Gate::getGateInstance($pass, ...$instances);
}

function collect(array $ary)
{
    return new Collection($ary);
}

function collect_column($array, $key)
{
    $result = [];
    foreach ($array as $a) {
        $result[] = $a->{$key};
    }
    return $result;
}

function number_format_jp($num)
{
    if (empty($num)) {
        return 0;
    }
    return preg_replace('/\.?0+$/', '', number_format($num, 2));
}

function config($key, $default = '')
{
    return (new Config())->get($key, $default);
}

function config_path($path)
{
    Config::setPath($path);
}

function shiftjis_strlen($value)
{
    return strlen(mb_convert_encoding($value, 'SJIS', 'UTF-8'));
}

function queryBuilder(array $query = [], array $removeQuery = [])
{
    return Request::queryBuilder($query, $removeQuery);
}

function csrf_token($length = 16)
{
    return Csrf::generate($length);
}

function route(string $alias, array $vars = [])
{
    $url = Router::fetchAlias($alias, $vars);
    return $url ? $url : '';
}

function isAlias(string $alias)
{
    $request = new Request();
}

function auth()
{
    return new Auth(config('auth.fields', ['id']));
}

function spiral()
{
    if (class_exists('Spiral')) {
        global $SPIRAL;
        return $SPIRAL;
    }

    throw new Exception('SPIRAL変数が見つかりませんでした', 500);

    //return new Spiral();
}
function isFile($fullFileName)
{
    ob_start(); //バッファ制御スタート
    if (preg_match('/\.(php)$/i', $fullFileName) === 1) {
        @require $fullFileName;
    } else {
        @require $fullFileName . '.blade.php';
    }
    return !empty(ob_get_clean()); //バッファ制御終了＆変数を取得
}
