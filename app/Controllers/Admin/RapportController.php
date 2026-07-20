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

    public function gainsParMois()
    {
        $annee = $this->request->getGet('annee') ?: date('Y');
        $model = new OperationModel();

        $resultats = $model->db->table('operation o')
            ->select("strftime('%m', o.date) as mois, SUM(o.frais) as total_frais, COUNT(*) as nb_operations")
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->where("strftime('%Y', o.date)", $annee)
            ->groupBy('mois')
            ->orderBy('mois', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON([
            'annee'   => $annee,
            'donnees'  => $resultats,
        ]);
    }
}
