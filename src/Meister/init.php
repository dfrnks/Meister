<?php

namespace Meister\Meister;

use Meister\Meister\Interfaces\InitInterface;
use Meister\Meister\Libraries\Annotation;
use Meister\Meister\Libraries\Auth;
use Meister\Meister\Libraries\Email;
use Meister\Meister\Libraries\Hash;
use Meister\Meister\Libraries\i18n;
use Meister\Meister\Libraries\Mongo;
use Meister\Meister\Libraries\Redis;
use Meister\Meister\Libraries\Retorno;
use Meister\Meister\Libraries\Session;
use Pimple\Container;
use Symfony\Component\Yaml\Yaml;

abstract class init implements InitInterface{

    private $controller;

    private $action;

    private $app;

    private $config;

    private $db;

    private $cache;

    private $session;

    private $ambiente;

    public function __construct($ambiente = null){
        $this->app          = new Container();
        $this->ambiente     = $ambiente;

        $this->start();
    }

    public function Run(){
        try{

            $router = filter_input(INPUT_GET, 'router');

            $this->checkRota($router);

            $ann = new Annotation($this->app,$this->config);

            $ann->validation($this->app['Contr'],$this->action,$this->app['options']);

            $action = $this->action;

            $this->controller->$action();

        }catch (\Exception $e){
            $retorno = new Retorno($this->app,$this->config);

            $code = $e->getCode();

            http_response_code($code);

            if($this->app->offsetExists('api') && $this->app['api']){
                $retorno->jsonRPC($e,$code);
            }

            $retorno->twigException($e);
        }
    }

    private function checkRota($router){
        $rotas = $this->getRouters();
        $rota = "";

        foreach($rotas as $we){
            $r0 = explode('/',$we['rota']);
            $r1 = explode('/',"/{$router}");

            if(count($r0) != count($r1)){
                continue;
            }

            $match = false;
            foreach ($r1 as $k => $i){
                if(!array_key_exists($k,$r0) || !preg_match('/{[\w]+}/',$r0[$k],$d) && $r0[$k] != $i){
                    $match = false;
                    break;
                }

                if(preg_match('/{[\w]+}/',$r0[$k],$d)){
                    $p = preg_replace('/[{}]/','',$r0[$k]);

                    if(array_key_exists('validate',$we['options']) && array_key_exists($p,$we['options']['validate'])){
                        if(!preg_match("/{$we['options']['validate'][$p]}$/",$i)){
                            throw new \Exception(sprintf(_('meister_router_%s_not_match_%s'),$p,$we['options']['validate'][$p]));
                        }
                    }

                    $we['params'][$p] = $i;
                }

                $match = true;
            }

            if($match){
                $rota = $we;
                break;
            }
        }

        if(!$rota){
            throw new \Exception('Router not found',420404);
        }

        unset($rota['options']['validate']);

//        if($rota['options'] && array_key_exists('api',$rota['options'])){
//            $this->app['api'] = true;
//        }

        list($modulo,$controller,$action) = explode('::', $rota['destino']);

        $c = $this->getController($modulo,$controller);

        $this->app['Controller']    = $controller;
        $this->app['Action']        = $action;
        $this->app['Module']        = $modulo;
        $this->app['Contr']         = $c;
        $this->app['options']       = (isset($rota['options']) ? $rota['options'] : []);
        $this->app['params']        = (isset($rota['params']) ? $rota['params'] : []);

        $this->app['ModuleDir']     = str_replace('/web/app.php','',$_SERVER['SCRIPT_FILENAME']).'/src/'.$modulo;

        $this->controller = new $c($this->app,$this->config,$this->db,$this->session);

        if(!method_exists($this->controller,$action)){
            throw new \Exception('Method not found',422404);
        }

        $this->action = $action;

    }

    private function getRouters(){
        $router = [
            [
                "rota" => "/login",
                "destino" => "Meister::AuthController::loginAction",
                "options" =>[
                    "api" => true,
                    "request" => ["POST"]
                ]
            ],
            [
                "rota" => "/logout",
                "destino" => "Meister::AuthController::logoutAction",
                "options" =>[
//                    "api" => true,
                    "notview" => true
                ]
            ],
            [
                "rota" => "/auth/session",
                "destino" => "Meister::AuthController::sessionAction",
                "options" =>[
                    "api" => true,
                    "request" => ["POST"]
                ]
            ],
            [
                "rota" => "/auth/register",
                "destino" => "Meister::AuthController::newUser",
                "options" =>[
                    "api" => true,
                    "request" => ["POST"]
                ]
            ],
            [
                "rota" => "/auth/forgot",
                "destino" => "Meister::AuthController::forgotPass",
                "options" =>[
                    "api" => true,
                    "request" => ["POST"]
                ]
            ],
            [
                "rota" => "/auth/recover",
                "destino" => "Meister::AuthController::recoverPass",
                "options" =>[
                    "api" => true,
                    "request" => ["POST"]
                ]
            ],
            [
                "rota" => "/hash/{token}",
                "destino" => "Meister::HashController::check",
                "options" =>[
                    "request" => ["GET"]
                ]
            ]
        ];

        $rotas = $this->getRotas();

        return array_merge($router,$rotas);
    }

    private function getController($modulo,$controller){

        $MModules = [
            "Meister"
        ];

        $se = (array_search($modulo,$MModules));

        if($se === 0 || $se){
            $c = 'Meister\\'.$modulo.'\\Controller\\'.$controller;
        }else{
            $se = (array_search($modulo,$this->config['modules']));

            if(is_bool($se) && !$se){
                throw new \Exception('Modulo não registrado');
            }

            $c = 'src\\'.$modulo.'\\Controller\\'.$controller;
        }

        if(!class_exists($c)){
            throw new \Exception("Controller not found ($c)",421404);
        }

        return $c;
    }

    public function loadConfig(){

        $file = $this->getConfig($this->ambiente);

        if(file_exists($file)){
            $this->config = Yaml::parse(file_get_contents($file));
            $this->app['config'] = $this->config;
            return $this;
        }

        throw  new \Exception('Config file not found',420502);
    }

    public function start(){
        $this->loadConfig();

        $this->app['Modules']  = str_replace('/web/app.php','',$_SERVER['SCRIPT_FILENAME']).'/src/';
        $this->app['BASE_DIR'] = $this->getBaseDir();
        $this->app['WEB_DIR']  = str_replace('app.php','',$_SERVER['SCRIPT_NAME']);
        $this->app['WEB_LINK'] = $this->app['WEB_DIR'];

        $this->i18n();

        $this->app['cache'] = $this->getCache();

        $this->cache   = $this->Cache();
        $this->db      = $this->newDB();
        $this->session = $this->Session();

        $this->app['hash'] = $this->Hash();
        $this->app['auth'] = $this->Auth();
        $this->app['mail'] = $this->Mail();

        $this->app['data'] = (array) json_decode(file_get_contents('php://input'));
    }

    public function newDB(){
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

    private function i18n(){
        $i18n = new i18n($this->app);

        $i18n->init();
    }

    private function Cache(){
        $type = $this->config['cache']['type'];

        switch ($type){
            case 'redis':
                $cache = new Redis($this->config);
                break;
            default;
                $cache = null;
        }

        if(!$cache){
            throw new \Exception('Type Cache not found');
        }

        return $cache;
    }

    private function Session(){
        return new Session($this->cache,$this->config["session"]["time"]);
    }

    private function Auth(){
        return new Auth($this->app, $this->db, $this->config, $this->session);
    }

    private function Hash(){
        return new Hash($this->app, $this->db);
    }

    private function Mail(){
        return new Email($this->app, $this->db);
    }
}