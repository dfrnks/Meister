<?php

namespace src\teste\Controller;

use Meister\Meister\Controller;
use src\teste\Document\Users;

class TesteController extends Controller {

    public function indexAction() {

        $user = $this->db->doc()->getRepository(get_class(new Users()))->findAll();

        var_dump($user);

        $this->Render(["nome" => "Douglas"]);
    }
}