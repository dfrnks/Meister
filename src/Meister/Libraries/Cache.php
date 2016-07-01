<?php

namespace Meister\Meister\Libraries;

use Meister\Meister\Interfaces\CacheInterface;

class Redis implements CacheInterface{
    
    private $cache = "cache_0022125522552";
    
    private $config = [];

    private $redis;

    public function __construct($config){
        $this->config = $config;

        $this->redis = $this->connect();
    }

    /**
     * @return \Redis
     */
    private function connect(){
        $redis = new \Redis();
        
        $redis->connect($this->config['cache']['host']);
        
        return $redis;
    }

    public function get($var = "") {
        return $this->redis->get($var);
    }

    public function getAll($var = "") {
        return $this->redis->keys($var . "*");
    }

    public function set($var,$value, $timeout = null) {
        return $this->redis->setex($var,$timeout,$value);
    }

    public function remove($var) {
        return $this->redis->del($var);
    }

    public function destroy() {
        return $this->redis->flushAll();
    }

    public function exists($var){
       return $this->redis->exists($var);
    }
}