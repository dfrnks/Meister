<?php

namespace Meister\Meister\Interfaces;

interface CacheInterface {
    public function get($var = "");
    public function getAll($var = "");
    public function set($var,$value, $timeout = null);
    public function remove($var);
    public function exists($var);
    public function destroy();
}