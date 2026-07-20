<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\SoldeModel;

class CompteClientController extends BaseController
{
    public function solde()
    {
        $idNumero = session()->get('numero_id');
        $soldeModel = new SoldeModel();

        $data['solde']  = $soldeModel->dernierSolde($idNumero);
        $data['numero'] = session()->get('numero');
        $data['title']  = 'Mon solde';

        return view('Front/solde', $data);
    }
}
