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
        $date = $this->request->getGet('date');

        $data['solde']     = $date ? $soldeModel->dernierSoldeAvantDate($idNumero, $date) : $soldeModel->dernierSolde($idNumero);
        $data['numero']    = session()->get('numero');
        $data['date']      = $date ?? date('Y-m-d');
        $data['title']     = 'Mon solde';

        return view('Front/solde', $data);
    }
}
