<?php

namespace App\Controllers\Front;   // <-- avec \Front en plus, pas juste App\Controllers

use App\Controllers\BaseController;
use App\Models\ProduitModel;

class Front extends BaseController
{
    public function index()
    {
        $model = new ProduitModel();

        $data['produits'] = $model->orderBy('created_at', 'DESC')->findAll(12);
        $data['title']    = 'Nos produits';

        return view('Front/index', $data);
    }

    public function contact()
    {
        return view('Front/contact', ['title' => 'Contact']);
    }

    public function contactSend()
    {
        return redirect()->to('/contact')->with('success', 'Message envoyé, merci !');
    }
}