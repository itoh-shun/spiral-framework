<?php

namespace framework\SpiralConnecter;

class RateLimiter
{
    private static $redis;
    private static $limit = 10;

    private static function init()
    {
        if (self::$redis === null) {
            self::$redis = new SpiralRedis();
            //self::$redis->setTimeout();
        }
    }

    public static function setLimit($limit)
    {
        self::$limit = $limit;
    }

    public static function addRequest()
    {
        self::init();

        $currentTimestampKey = time();

        if (!self::$redis->exists($currentTimestampKey)) {
            self::$redis->set($currentTimestampKey, 1);
        } else {
            self::$redis->incr($currentTimestampKey);
        }
    }

    public static function isRequestAllowed()
    {
        $totalRequestsInLastMinute = self::getTotalRequestsInLastMinute();

        if ($totalRequestsInLastMinute > self::$limit) {
            return false; // リクエスト制限を超えている
        }
        return true; // リクエストが許可される
    }

    public static function getTotalRequestsInLastMinute()
    {
        self::init();

        $total = 0;
        for ($i = 59; $i >= 0; $i--) {
            $key = time() - $i;
            $total += (int)self::$redis->get($key) ?: 0;
        }
        return $total;
    }

    public static function getRemainingRequests()
    {
        return self::$limit - self::getTotalRequestsInLastMinute();
    }
}
