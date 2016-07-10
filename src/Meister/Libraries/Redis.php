<?php

namespace Meister\Meister\Libraries;

use Meister\Meister\Interfaces\CacheInterface;

class Redis implements CacheInterface{
    
    private $config = [];

    private $redis;

    public function __construct($config){
        $this->config = $config;

        $this->redis = $this->connect();
    }

    private function getPrefix() {
        return md5(__DIR__).'/';
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
        return json_decode($this->redis->get($this->getPrefix().$var));
    }

    public function getAll($var = "") {
        return $this->redis->keys($this->getPrefix().$var . "*");
    }

    public function set($var,$value, $timeout = 86400) {
        return $this->redis->setex($this->getPrefix().$var,$timeout,json_encode($value));
    }

    public function remove($var,$prefix = true) {
        if($prefix){
            return $this->redis->del($this->getPrefix().$var);
        }
        return $this->redis->del($var);
    }

    public function expire($key,$timeout){
        return $this->redis->expire( $key, $timeout );
    }

    public function destroy() {
//        return $this->redis->flushAll();
    }

    public function exists($var){
       return $this->redis->exists($this->getPrefix().$var);
    }
}