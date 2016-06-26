<?php

namespace app;

use Meister\Meister\init;

class AppInit extends init {

    public function getConfig($ambiente = null){
        return __DIR__ . '/config/config_'.$ambiente.'.yml';
    }

    public function getCache(){
        return [
            "twig" => __DIR__.'/cache/twig',
            "doctrine" => __DIR__.'/cache/doctrine'
        ];
    }

    public function getRotas(){
        return [
            "teste::TesteController::indexAction" => "/home"
        ];
    }
}