<?php

namespace app;

class Routers {
    public function register(){
        return [
              "rota/teste" => [
                  "homeController:indexAction"
              ]
        ];
    }
}