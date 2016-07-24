<?php

namespace Meister\Meister\Libraries;

use Pimple\Container;

class Twig{

    private $app;

    private $config;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(Container $app,$config){
        $this->app = $app;
        $this->config = $config;

        $this->init();
    }

    public function init(){
        $view_dir = [
            __DIR__.'/../Views'
        ];

        $twigConfig = [];

        if($this->config['twig']['cache']){
            $twigConfig["cache"] = $this->app['cache']['twig'];
        }

        $twigConfig["debug"] = $this->config['twig']['debug'];

        $loader = new \Twig_Loader_Filesystem($view_dir);

        foreach($this->config['modules'] as $app) {
            $loader->addPath($this->app['Modules'].$app.'/Views', $app);
            $loader->addPath($this->app['Modules'].$app.'/Templates', $app);
        }

        $this->twig = new \Twig_Environment(
            $loader,
            $twigConfig
        );

        $this->twig->addExtension(new \Twig_Extensions_Extension_I18n());

        /**
         * Verifica permissões para exibir determinada coisa
         */
        $function = new \Twig_SimpleFunction('permission', function ($rule) {
            return $this->app['auth']->checkRules($rule);
        });

        $this->twig->addFunction($function);
    }

    public function render($v,$data){

        $view = str_replace('{module}',$this->app['Module'],$v);

        if(!$this->twig->resolveTemplate($view)){
            $view = str_replace('{module}','Meister',$v);
        }

        $data = array_merge($data,[
            "logged"  => $this->app['auth']->isLogged(),
            "User"    => $this->app['auth']->getUser(),
            "module"  => $this->app['Module']
        ]);

        foreach ($this->app->keys() as $key){
            $data[$key] = $this->app[$key];
        }

        return $this->twig->render($view,$data);
    }
}