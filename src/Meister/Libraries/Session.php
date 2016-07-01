<?php

namespace Meister\Meister\Libraries;

use Meister\Meister\Interfaces\CacheInterface;

class Session {

    private $cache;

    public function __construct(CacheInterface $cache){
        $this->cache = $cache;
    }

    private function getPrefix() {
        return md5(__DIR__);
    }

    /**
     * @param $var
     * @return mixed
     */
    public function get($var) {
        if($this->exist($var)) {
            return $this->cache->get($this->getPrefix().$var);
        }

        return null;
    }

    /**
     * @param $var
     * @param $value
     * @return mixed
     */
    public function set($var,$value) {
        return $this->cache->set($this->getPrefix().$var,$value);
    }

    /**
     * @return mixed
     */
    public function getAll() {
        return $this->cache->getAll($this->getPrefix());
    }

    /**
     * @param $var
     */
    public function remove($var) {
       $this->cache->remove($this->getPrefix().$var);
    }

    /**
     * @param $var
     * @return bool
     */
    public function exist($var) {
        $session = (isset($_SESSION[$this->getPrefix()])?$_SESSION[$this->getPrefix()]:$_SESSION);

        if(array_key_exists($var,$session)) {
            return true;
        }

        return false;
    }
    
    public function destroy() {
        unset($_SESSION[$this->getPrefix()]);
    }

    public function Cache(){
        return $this->cache;
    }
}