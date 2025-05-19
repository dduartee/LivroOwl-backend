<?php

namespace App\Controllers;

use App\Core\Controller;

class Error extends Controller {
    public function index() 
    {
        $this->renderError(404, "Página não encontrada");
    }
}