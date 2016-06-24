<?php

namespace Meister\Meister;

use Meister\Meister\Interfaces\InitInterface;
use Pimple\Container;
use Symfony\Component\Yaml\Yaml;

abstract class init implements InitInterface{

	private $controller;

	private $action;

	private $app;

	private $config;

	private $db;

	public function __construct(){
		$this->app = new Container();
		$this->config = $this->loadConfig();
		$this->db = [];


		$this->app['cache'] = $this->getCache();
	}

    public function Run(){

        try{
            $router = filter_input(INPUT_GET, 'router');

            $this->checkRota($router);

			$action = $this->action;

			$this->controller->$action();

        }catch (\Exception $e){
			throw $e;
        }
    }

	private function checkRota($router){
		$rotas = $this->getRotas();
		$rota = "";

		foreach($rotas as $ro => $we){
			if($we == "/{$router}"){
				$rota = $ro;
				break;
			}
		}

		if(!$rota){
			throw new \Exception('Router not found',420404);
		}

		$rota = explode('::', $rota);

		$c = 'src\\'.$rota[0].'\\Controller\\'.$rota[1];

		if(!class_exists($c)){
			throw new \Exception("Classe not found ($c)",421404);
		}

		$this->app['Controller']    = $rota[1];
		$this->app['Action']        = $rota[2];
		$this->app['Module']        = 'src\\'.$rota[0];

		$this->app['ModuleDir']     = str_replace('/web/app.php','',$_SERVER['SCRIPT_FILENAME']).'/src/'.$rota[0];

		$this->controller = new $c($this->app,$this->config,$this->db);

		if(!method_exists($this->controller,$rota[2])){
			throw new \Exception('Method not found',422404);
		}

		$this->action = $rota[2];

	}

	private function loadConfig(){

		$file = $this->getConfig();

		if(file_exists($file)){
			return Yaml::parse(file_get_contents($file));
		}

		throw  new \Exception('Config file not found',420502);

	}
}