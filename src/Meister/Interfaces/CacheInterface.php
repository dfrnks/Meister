<?php

namespace Meister\Meister\Interfaces;

interface CacheInterface {
    public function get($var = "");
    public function getAll($var = "");
    public function set($var,$value, $timeout = null);
    public function remove($var,$prefix = true);
    public function exists($var);
    public function expire($var, $timeout);
    public function destroy();
}