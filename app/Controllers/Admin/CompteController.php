<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NumeroTelephoneModel;
use App\Models\SoldeModel;
use App\Models\OperationModel;

class CompteController extends BaseController
{
    public function index()
    {
        $model = new NumeroTelephoneModel();
        $data['comptes'] = $model->avecSoldeActuel();
        $data['title'] = 'Comptes clients';
        return view('Admin/comptes/index', $data);
    }

    public function afficher(int $id)
    {
        $numeroModel = new NumeroTelephoneModel();
        $soldeModel = new SoldeModel();
        $operationModel = new OperationModel();

        $data['compte'] = $numeroModel->find($id);
        $data['solde'] = $soldeModel->dernierSolde($id);
        $data['operations'] = $operationModel->historiquePourNumero($id);
        $data['title'] = 'Détail client';
        return view('Admin/comptes/show', $data);
    }
}
