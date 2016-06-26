<?php

namespace src\teste\Controller;

use Meister\Meister\Controller;

class TesteController extends Controller {

    public function indexAction() {

        $this->Render(["nome" => "Douglas"]);
    }
}