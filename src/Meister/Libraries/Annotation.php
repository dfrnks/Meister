<?php

namespace Meister\Meister\Libraries;

use Pimple\Container;
use zpt\anno\AnnotationFactory;
use zpt\anno\Annotations;

/**
 * Classes
 * @notauthenticated -- acessar sem estar autenticado
 * @authenticated    -- acessar estando autenticado
 * @permission       -- [ADM,DEV] -- array com os perfis que pode acessar
 * @post             -- no sei
 *
 * Metodos
 * @authenticated
 * @notauthenticated
 * @permission
 * @request          -- POST or [POST,PUT]
 * @notview          -- Não retornar nada, apenas executar
 * @api              -- Retornar um Json
 */

class Annotation {

    private $config = [];

    private $app;

    public function __construct(Container $app, $config){
        $this->app = $app;
        $this->config = $config;
    }

    /**
     * @param $controller
     * @param $method
     * @return mixed
     * @throws \Exception
     */
    public function validation($controller,$method,$options) {

        $classReflector = new \ReflectionClass($controller);
        $classAnnotations = new Annotations($classReflector);

        $methodAnnotations = array();
        foreach ($classReflector->getMethods() as $methodReflector) {
            $methodAnnotations[$methodReflector->getName()] = new Annotations($methodReflector);
        }

        $interfaces = class_implements($controller);

        if(isset($interfaces['Meister\Meister\interfaces\MeisterRestInterface'])){
            $return['rest'] = true;
        }else{
            $return['rest'] = false;
        }

        if($return['rest']){
            $method = $_SERVER['REQUEST_METHOD'];
            $method = strtolower($method);
        }

        /**
         * Valida annotations classe
         */
        if($this->config['authentication']) {
            if($classAnnotations->hasAnnotation('notauthenticated') === false){
                if(!$this->authenticatedValidation(true)) {
                    if(array_key_exists($method,$methodAnnotations) && !$methodAnnotations[$method]->hasAnnotation('notauthenticated') === true){
                        throw new \Exception("Not authenticated",403);
                    }
                }
            }
        }else{
            if($classAnnotations->hasAnnotation('authenticated') === true){
                if(!$this->authenticatedValidation(true)) {
                    if(array_key_exists($method,$methodAnnotations) && !$methodAnnotations[$method]->hasAnnotation('notauthenticated') === true){
                        throw new \Exception("Not authenticated",403);
                    }
                }
            }
        }

        if($classAnnotations->hasAnnotation('permission') === true){
            $this->checkPermission($classAnnotations['permission']);
        }

        if($classAnnotations->hasAnnotation('post') === true){
            $return['post'] = true;
        }else{
            $return['post'] = false;
        }

        /**
         * Valida annotations metodos
         */
        if(array_key_exists($method,$methodAnnotations) &&  $methodAnnotations[$method]->hasAnnotation('request') === true){
            $this->requestValidation($methodAnnotations[$method]['request']);
        }

        if(array_key_exists($method,$methodAnnotations) && $methodAnnotations[$method]->hasAnnotation('authenticated') === true){
            $this->authenticatedValidation();
        }

        if(array_key_exists($method,$methodAnnotations) && $methodAnnotations[$method]->hasAnnotation('notview') === true){
            $return['view'] = false;
        }else{
            $return['view'] = true;
        }

        if(array_key_exists($method,$methodAnnotations) && $methodAnnotations[$method]->hasAnnotation('api') === true){
            $return['api'] = true;
        }else{
            $return['api'] = false;
        }

        if(array_key_exists($method,$methodAnnotations) && $methodAnnotations[$method]->hasAnnotation('permission') === true){
            $this->checkPermission($methodAnnotations[$method]['permission']);
        }

        /**
         * Valida Options
         */

        if(array_key_exists('request',$options)){
            $this->requestValidation($options['request']);
        }

        if(array_key_exists('permission',$options)){
            $this->checkPermission($options['permission']);
        }

        if(array_key_exists('authenticated',$options)){
            if($options['authenticated'])
                $this->authenticatedValidation();
        }

        if(array_key_exists('notview',$options)){
            $return['view'] = false;
        }

        if(array_key_exists('api',$options)){
            $return['api'] = true;
        }

        foreach ($return as $k => $r){
            $this->app[$k] = $r;
        }

    }

    /**
     * @param $request
     * @return bool
     * @throws \Exception
     */
    private function requestValidation($request){

        $method = $_SERVER['REQUEST_METHOD'];

        $mallow = $request;

        if(is_array($request)) {
            if(in_array($method,$request)) {
                return true;
            }

            $mallow = implode(',',$request);
        }else{
            if($method == $request) {
                return true;
            }
        }

        throw new \Exception("Method Not Allowed - ({$mallow})",405);
    }

    private function checkPermission($permission){
        if(!Auth::isLogged()){
            return true;
        }

        if(!Auth::checkRules($permission)){
            throw new \Exception('forbidden',402);
        }

        return true;
    }

    /**
     * @param bool|false $controller
     * @return bool
     * @throws \Exception
     */
    private function authenticatedValidation($controller = false) {
        if(Auth::isLogged()){
            return true;
        }

        /**
         * Se for controller true, então verifica se o metodo pode acessar sem estar authenticado.
         * Caso contrario retornara um exception
         */
        if($controller) {
            return false;
        }

        throw new \Exception("Not authenticated",403);
    }
}