<?php

namespace app;

use Meister\Meister\init;

class AppInit extends init {

    public function getConfig($ambiente = null){
        return __DIR__ . '/config/config_'.$ambiente.'.yml';
    }

    public function getBaseDir(){
        return __DIR__ .'/..';
    }

    public function getCache(){
        return [
            "twig" => __DIR__.'/cache/twig',
            "doctrine" => __DIR__.'/cache/doctrine'
        ];
    }

    public function getRotas(){
        return [
            [
                "rota" => "/home",
                "destino" => "teste::TesteController::indexAction",
                "options" =>[
                    "api" => true,
                    "autentication" => true
                ]
            ],
            [
                "rota" => "/",
                "destino" => "teste::TesteController::indexAction",
                "options" =>[
                    "api" => true,
                    "autentication" => true
                ]
            ]
        ];
    }
    
}