<?php

namespace Meister\Meister\Libraries;


use Pimple\Container;

class Retorno{

    private $app;

    private $config;
    
    public function __construct(Container $app,$config){
        $this->app = $app;
        $this->config = $config;
    }

    public function twig($data){
        $view_dir = [
            __DIR__.'/../Views',
            $this->app['ModuleDir'].'/Views'
        ];

        $twigConfig = [];

        if($this->config['twig']['cache']){
            $twigConfig["cache"] = $this->app['cache']['twig'];
        }

        foreach($this->config['modules'] as $app) {
            $dir = $this->app['Modules'].$app.'/Views';
            if(file_exists($dir)) {
                $view_dir[] = $dir;
            }
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
            return Auth::checkRules($rule);
        });

        $twig->addFunction($function);

        if(array_key_exists('template', $data) && !empty($data['template'])){
            $view = $data['template'];
        }else{
            $controller = str_replace('Controller','',$this->app["Controller"]);
            $method = str_replace('Action','',$this->app["Action"]);

            $view = $controller.'/'.$method.'.html.twig';
        }

        $data = array_merge($data,[
            "logged"  => $this->app['auth']->isLogged(),
            "User"    => $this->app['auth']->getUser()
        ]);

        echo $twig->render($view,$data);
        exit();
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
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Views/');

        $twigConfig = [];

        $twigConfig["debug"] = $this->config['twig']['debug'];

        $twig = new \Twig_Environment($loader, $twigConfig);

        echo $twig->render('error.html.twig',$data);
        exit();
    }

    public function jsonRPC($data, $code = 200){
        @header('Content-Type: application/json');

        $retorno["jsonrpc"] = "2.0";
        $retorno["error"] = null;
        if($code != 200) {
            switch ($code) {
//                case 403:
//
//                    $retorno["error"] = [
//                        "code" => -403,
//                        "message" => "forbidden"
//                    ];
//                    break;

                case 404:

                    $retorno["error"] = [
                        "code" => -32601,
                        "message" => "Method not found"
                    ];
                    break;

                case 204:

                    $retorno["error"] = [
                        "code" => -32700,
                        "message" => "Parse error"
                    ];
                    break;

                case 205:

                    $retorno["error"] = [
                        "code" => -32600,
                        "message" => "Invalid Request"
                    ];
                    break;

                case 206:

                    $retorno["error"] = [
                        "code" => -32602,
                        "message" => "Invalid params"
                    ];
                    break;

                case 501:

                    $retorno["error"] = [
                        "code" => -32603,
                        "message" => "Internal error"
                    ];
                    break;

                case 500:

                    $retorno["error"] = [
                        "code" => -32000,
                        "message" => "Server error"
                    ];
                    break;

                default:
                    $retorno["error"] = [
                        "code" => 0 - $code,
                        "message" => $data->getMessage()
                    ];
                    break;
            }

        }else{
            $retorno["result"] = (array) $data;
        }

        if(!json_encode($retorno)){
            $retorno["result"] = "";
            $retorno["error"] = [
                "code" => -32000,
                "message" => "Server error"
            ];
        }

        $retorno["id"] = 0;

        echo json_encode($retorno);
        exit();

    }

    /**
     * @param $app \Meister\Meister\interfaces\MeisterRestInterface
     * @param $id
     * @throws \Exception
     */
    public function apiRest($app,$id){

        $method = $_SERVER['REQUEST_METHOD'];

        switch ($method){
            case 'POST':
                $data = $app->post();
                break;
            case 'GET':
                $data = $app->get($id);
                break;
            case 'PUT':
                $data = $app->put($id);
                break;
            case 'DELETE':
                $data = $app->delete($id);
                break;
            default:
                throw new \Exception("Method Not Allowed - (GET,POST,PUT,DELETE)",405);
        }

        @header('Content-Type: application/json');

        echo json_encode($data);
        exit();
    }
}