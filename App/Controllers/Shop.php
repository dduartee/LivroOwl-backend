<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Products;

class Shop extends Controller
{
    public function index() 
    {
        $products = new Products;
        $this->render('Shop/index', ['produtos' => $products->getAll()]);
    }
}