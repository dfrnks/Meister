<?php

namespace Meister\Meister;

use Meister\Meister\Interfaces\InitInterface;

abstract class init implements InitInterface{

    public function Run(){

        try{
            $router = filter_input(INPUT_GET, 'router');

            $rotas = $this->getRotas();

            if(!array_key_exists($router,$rotas)){
                throw new \Exception('Router not found');
            }

            $rota = explode('::', $rotas[$router]);

            $c = 'Main\\'.$rota[0].'\\Controller\\'.$rota[1];

            var_dump($c);
        }catch (\Exception $e){

        }

    }
}