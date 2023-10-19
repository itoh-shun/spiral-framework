<?php

namespace framework\SpiralConnecter;

class SpiralRedis {
    private $cache;

    public function __construct() {
        global $SPIRAL;
        $this->cache = $SPIRAL->getCache();
    }
    
    public function get($key) {
        return $this->cache->get($key);
    }

    public function set($key, $value): void {
        $this->cache->set($key, $value);
    }

    public function exists($key) {
        return $this->cache->exists($key);
    }

    public function delete($key) {
        return $this->cache->delete($key);
    }
    public function decr($key , $value = 1) {
        return $this->cache->decr($key , $value);
    }
    public function incr($key , $value = 1) {
        return $this->cache->incr($key , $value);
    }
    public function setTimeout($timeout = 900) {
        return $this->cache->setTimeout($timeout);
    }
}