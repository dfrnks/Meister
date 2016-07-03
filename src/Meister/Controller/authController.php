<?php

namespace Meister\Meister\Controller;

use Meister\Meister\Controller;
use Meister\Meister\Libraries\Data;

/**
 * @notauthenticated
 * @api
 */
class authController extends Controller {
    
    public function loginAction() {
        $_username = $this->app["data"]["_username"];
        $_password = $this->app["data"]["_password"];
        
        $auth = $this->app['auth'];

        $s = $auth->login($_username,$_password);

        $this->app["cod_pass"] = $s["cod"];

        $this->Render([
            "token" => $s['token']
        ]);
    }

    public function sessionAction() {

        $auth = $this->app['auth'];

        $da = $auth->checkToken(Data::getHeader('X-Token'));

        $pessoa = $this->app['db']->db()->getRepository("Main\\app\\Document\\Users")->findOneBy([
            "id" => $da["id"]
        ]);

        if(empty($pessoa)){
            throw new \Exception("Credentials incorrect",403);
        }

        $auth->setSession($pessoa);

        $this->Render([
            true
        ]);
    }

    /**
     * @notview
     */
    public function logoutAction() {

        $this->app['auth']->logout();

        header('Location:'.$this->app['WEB_DIR']);
    }

//    public function forgotAction() {
//        $_username = $this->app["data"]["_username"];
//
//        $auth = new Auth($this->app);
//
//        $s = $auth->forgotPass($_username);
//
//        $this->Render([]);
//    }
}