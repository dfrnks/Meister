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

    private $ambiente;
    
    public function __construct($ambiente = null){
        $this->app          = new Container();
        $this->db           = [];
        $this->ambiente     = $ambiente;
    }

    public function Run(){
        try{

            $this->config       = $this->loadConfig();

            $this->app['cache'] = $this->getCache();

            $router = filter_input(INPUT_GET, 'router');

            $this->checkRota($router);

            $action = $this->action;

            $this->controller->$action();

        }catch (\Exception $e){
            $this->twigException($e);
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

        $file = $this->getConfig($this->ambiente);

        if(file_exists($file)){
            return Yaml::parse(file_get_contents($file));
        }

        throw  new \Exception('Config file not found',420502);

    }

    public function twigException(\Exception $e){

        $data['message']= $e->getMessage();
        $data['string'] = $e->getTraceAsString();

        $trace = $e->getTrace();

        foreach($trace as $key => $val) {
            $file = explode('/',$val['file']);
            $class = explode('\\',$val['class']);

            $trace[$key]['filename']  = array_pop($file);
            $trace[$key]['classname'] = array_pop($class);
        }

        $file = explode('/',$e->getFile());
        $data['onTrace'] = [
            "file" => $e->getFile(),
            "line" => $e->getLine(),
            "filename" => array_pop($file)
        ];
        $data['traces'] = $trace;

        $data['debug'] = $this->config['twig']['debug'];
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/Views/');

        $twigConfig = [];

        $twigConfig["debug"] = $this->config['twig']['debug'];

        $twig = new \Twig_Environment($loader, $twigConfig);

        echo $twig->render('error.html.twig',$data);
        exit();
    }
}