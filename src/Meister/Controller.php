<?php

namespace Meister\Meister;

class Controller {

    private $app;

    private $db;

    private $config;

    public function __construct($app, $config, $db){
        $this->app    = $app;
        $this->db     = $db;
        $this->config = $config;
    }

    protected function Render($data){

        $view_dir = [
            __DIR__.'/Views',
            $this->app['ModuleDir'].'/Views'
        ];

        $twigConfig = [];

        if($this->config['twig']['cache']){
            $twigConfig["cache"] = $this->app['cache']['twig'];
        }

        $twigConfig["debug"] = $this->config['twig']['debug'];

        $twig = new \Twig_Environment(
            new \Twig_Loader_Filesystem($view_dir),
            $twigConfig
        );

        /**
         * Verifica permissões para exibir determinada coisa
         */
        $function = new \Twig_SimpleFunction('permission', function ($rule) {
            return true;//Auth::checkRules($rule);
        });

        $twig->addFunction($function);

        if(array_key_exists('template', $data) && !empty($data['template'])){
            $view = $data['template'];
        }else{
            $controller = str_replace('Controller','',$this->app["Controller"]);
            $method = str_replace('Action','',$this->app["Action"]);

            $view = $controller.'/'.$method.'.html.twig';
        }

        echo $twig->render($view,$data);

    }

    protected function getConfig($conf=null){
        if($conf){
            if(array_key_exists($conf,$this->config)){
                return $this->config[$conf];
            }

            return null;
        }

        return $this->config;
    }

}