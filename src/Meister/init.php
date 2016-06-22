<?php

namespace Meister\Meister;

use Meister\Meister\Interfaces\InitInterface;

abstract class init implements InitInterface{

	private $controller;

	private $action;

    public function Run(){

        try{
            $router = filter_input(INPUT_GET, 'router');

            $this->checkRota($router);

        }catch (\Exception $e){

        }

    }

	private function checkRota($router){
		$rotas = $this->getRotas();
		$rota = "";
		foreach($rotas as $ro => $we){
			if($router == $we){
				$rota = $ro;
				break;
			}
		}

		if($rota){
			throw new \Exception('Router not found',420404);
		}

		$rota = explode('::', $rota);

		$c = 'src\\'.$rota[0].'\\Controller\\'.$rota[1];

		if(!class_exists($c)){
			throw new \Exception('Classe not found',421404);
		}

		$this->controller = new $c();

		if(!method_exists($this->controller,$rota[2])){
			throw new \Exception('Method not found',422404);
		}

		$this->action = $this->controller->$rota[2]();
	}
}