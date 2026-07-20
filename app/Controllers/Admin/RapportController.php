<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperationModel;

class RapportController extends BaseController
{
    public function gains()
    {
        $debut = $this->request->getGet('debut');
        $fin = $this->request->getGet('fin');
        $model = new OperationModel();

        $data['gains'] = $model->gainsParPeriode($debut, $fin);
        $data['totalGains'] = array_sum(array_column($data['gains'], 'total_frais'));
        $data['debut'] = $debut;
        $data['fin'] = $fin;
        $data['title'] = 'Rapport des gains';
        return view('Admin/rapport/gains', $data);
    }
}
