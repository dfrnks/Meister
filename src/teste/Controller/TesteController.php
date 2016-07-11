<?php

namespace src\teste\Controller;

use Meister\Meister\Controller;
use src\teste\Document\Users;

/**
 * Class TesteController
 * @package src\teste\Controller

 */
class TesteController extends Controller {

    /**
     * @notauthenticated
     */
    public function indexAction() {

//        $this->session->set('teste',time());
//        $this->session->set('teste1',time());
//        $this->session->set('teste2',time());
//        $this->session->set('teste3',time());

//        for($x=0; $x<1000; $x++){
//            $this->session->set('teste'.$x,time());
//        }
//        $this->session->Cache()->set('teste','asd');

//        $this->session->Cache()->remove('teste');

//        $this->session->destroy();
//
////        $this->session->remove('teste');
//
//        var_dump($this->session->Cache()->get('teste'));
//        var_dump($this->session->Cache()->getAll());
//        var_dump($this->session->getAll());
//        var_dump($this->session->get('teste0'));

//        $user = $this->db->doc()->getRepository(get_class(new Users()))->findAll();
//
//
//        $this->Render(["nome" => $this->data($user)]);

//        var_dump($this->app['auth']->login('douglasfrancardoso@gmail.com','asdasd'));
//		echo _("string_teste");

		$this->Render([]);

    }
}