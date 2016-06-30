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

        $user = $this->db->doc()->getRepository(get_class(new Users()))->findAll();

        $this->Render(["nome" => $this->data($user)]);
    }
}