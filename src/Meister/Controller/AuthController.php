<?php

namespace Meister\Meister\Controller;

use Meister\Meister\Controller;
use Meister\Meister\Libraries\Crypt;
use Meister\Meister\Libraries\Curl;
use Meister\Meister\Libraries\Data;

/**
 * @notauthenticated
 * @api
 */
class AuthController extends Controller {

    public function loginAction() {
        $_username = $this->request('_username');
        $_password = $this->request('_password');

        $auth = $this->app['auth'];

        $s = $auth->login($_username,$_password);

        $this->app["cod_pass"] = $s["cod"];

        $this->Render([
            "token" => $s['token']
        ]);
    }

    public function sessionAction() {

        $da = $this->app['auth']->checkToken(Data::getHeader('X-Token'));

        $pessoa = $this->db->doc()->getRepository($this->config['auth']['entity'])->findOneBy([
            "id" => $da["id"]
        ]);

        if(empty($pessoa)){
            throw new \Exception("Credentials incorrect",403);
        }

        $this->app['auth']->setSession($pessoa);

        $this->Render([
            true
        ]);
    }

    public function logoutAction() {

        $this->app['auth']->logout();

        $this->Render([
            true
        ]);

//        header('Location:'.$this->app['WEB_DIR']);
    }

    public function newUser() {
        $data   = $this->request();
        $config = $this->getConfig('auth');

        if(!array_key_exists($config['field'],$data)){
            throw new \Exception(sprintf(_("meister_%s_empty"), $config['field']));
        }

        if(!array_key_exists('password',$data)){
            throw new \Exception(sprintf(_("meister_%s_empty"), $config['field']));
        }

        $data['password'] = Crypt::gpass($data['password']);

        $data = $this->data($this->db->insert(new $config['entity'],$data));

        unset($data['password']);

        $this->Render($data);
    }

    public function forgotPass() {
        $_username = $this->request("_username");

        $this->app['auth']->forgotPass($_username);

        $this->Render([true]);
    }

    public function recover() {
        $data = (new Curl())->getRedirect();

        var_dump($data);

        #$this->app['auth']->recoverPass($id);
        # Busca a pessoa e joga na tela

        $this->Render([]);
    }

    public function recoverPass() {
        $_username = $this->request('_username');
        $_password = $this->request('_password');

        $s = $this->app['auth']->recoverPass($_username,$_password);

        $this->Render([$s]);
    }
}