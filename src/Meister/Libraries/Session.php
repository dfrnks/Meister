<?php

namespace Meister\Meister\Libraries;

use Meister\Meister\Interfaces\CacheInterface;

class Session {

    private $cache;

    private $time;

    private $prefix;

    public function __construct(CacheInterface $cache,$time){
        $this->cache = $cache;
        $this->time  = $time;
        $this->prefix = $this->getPrefix();
    }

    private function getPrefix() {

        if(isset($_COOKIE['uid'])){
            $uidSession = $_COOKIE['uid'];

            setcookie("uid", $uidSession, time() + $this->time);

            $values = $this->cache->getAll($uidSession.'/');
            foreach ($values as $val){
                $this->cache->expire($val,$this->time);
            }

            return $uidSession.'/';
        }

        $uidSession = Data::uid();

        setcookie("uid", $uidSession, time() + $this->time);

        return $uidSession.'/';
    }

    /**
     * @param $var
     * @return mixed
     */
    public function get($var) {
        if($this->exist($this->prefix.$var)) {
            return $this->cache->get($this->prefix.$var);
        }

        return null;
    }

    /**
     * @param $var
     * @param $value
     * @return mixed
     */
    public function set($var,$value) {
        return $this->cache->set($this->prefix.$var,$value,$this->time);
    }

    /**
     * @return mixed
     */
    public function getAll() {
        return $this->cache->getAll($this->prefix);
    }

    /**
     * @param $var
     */
    public function remove($var) {
       $this->cache->remove($this->prefix.$var);
    }

    /**
     * @param $var
     * @return bool
     */
    public function exist($var) {
        return $this->cache->exists($var);
    }
    
    public function destroy() {
        $values = $this->cache->getAll($this->prefix);
        foreach ($values as $val){
            $this->cache->remove($val,false);
        }
    }

    public function Cache(){
        return $this->cache;
    }
}