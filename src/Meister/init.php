<?php

namespace Meister\Meister;

use Meister\Meister\Interfaces\InitInterface;
use Meister\Meister\Libraries\Annotation;
use Meister\Meister\Libraries\Mongo;
use Meister\Meister\Libraries\Retorno;
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
        $this->ambiente     = $ambiente;
    }

    public function Run(){
        try{

            $this->config       = $this->loadConfig();

            $this->app['cache'] = $this->getCache();
            $this->app['api']   = false;

            $router = filter_input(INPUT_GET, 'router');

            $this->checkRota($router);

            $ann = new Annotation($this->app,$this->config);
            
            $ann->validation($this->app['Contr'],$this->action);
            
            $action = $this->action;

            $this->controller->$action();

        }catch (\Exception $e){
            $retorno = new Retorno($this->app,$this->config);

            $retorno->twigException($e);
        }
    }

    private function checkRota($router){
        $rotas = $this->getRotas();
        $rota = "";

        foreach($rotas as $we){
            if($we['rota'] == "/{$router}"){
                $rota = $we;
                break;
            }
        }

        if(!$rota){
            throw new \Exception('Router not found',420404);
        }

        if($rota['options'] && $rota['options']['api']){
            $this->app['api'] = true;
        }

        list($modulo,$controller,$action) = explode('::', $rota['destino']);

        $se = (array_search($modulo,$this->config['modules']));
        
        if(is_bool($se) && !$se){
            throw new \Exception('Modulo não registrado');
        }

        $c = 'src\\'.$modulo.'\\Controller\\'.$controller;

        if(!class_exists($c)){
            throw new \Exception("Classe not found ($c)",421404);
        }

        $this->app['Controller']    = $controller;
        $this->app['Action']        = $action;
        $this->app['Module']        = 'src\\'.$modulo;
        $this->app['Contr']         = $c;

        $this->app['ModuleDir']     = str_replace('/web/app.php','',$_SERVER['SCRIPT_FILENAME']).'/src/'.$modulo;
        $this->app['Modules']       = str_replace('/web/app.php','',$_SERVER['SCRIPT_FILENAME']).'/src/';

        $this->db = $this->newDB();

        $this->controller = new $c($this->app,$this->config,$this->db);

        if(!method_exists($this->controller,$action)){
            throw new \Exception('Method not found',422404);
        }

        $this->action = $action;

    }

    private function loadConfig(){

        $file = $this->getConfig($this->ambiente);

        if(file_exists($file)){
            return Yaml::parse(file_get_contents($file));
        }

        throw  new \Exception('Config file not found',420502);

    }

    private function newDB(){
        $type = $this->config['database']['type'];
        
        switch ($type){
            case 'mongo':
                $db = new Mongo($this->config,$this->app);
                break;
            default;
                $db = null;
        }
        
        if(!$db){
            throw new \Exception('Type database not found');
        }
        
        return $db;
    }
}