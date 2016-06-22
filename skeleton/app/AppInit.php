<?php

namespace app;

use Meister\Meister\init;

class AppInit extends init {

    public function getConfig($ambiente){
        return __DIR__ . '/config/config'.$ambiente.'.yml';
    }

    public function getRotas(){
        return [
            "rota/teste" => "teste::homeController::indexAction"
        ];
    }
}