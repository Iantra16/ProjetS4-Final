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
        $recherche = $this->request->getGet('q') ?: null;

        $builder = $model->db->table('numero_telephone nt')
            ->select('nt.id, nt.numero, op.nom as operateur, s.montant as solde_actuel, s.date as date_solde')
            ->join('prefixe_operateur po', 'po.id = nt.id_prefixe')
            ->join('operateur op', 'op.id = po.id_operateur')
            ->join('solde s', 's.id = (SELECT id FROM solde WHERE id_numero_tel = nt.id ORDER BY id DESC LIMIT 1)', 'left');
        if ($recherche) {
            $builder->groupStart()
                ->like('nt.numero', $recherche)
                ->orLike('op.nom', $recherche)
            ->groupEnd();
        }
        $data['comptes'] = $builder->orderBy('nt.id', 'ASC')->get()->getResultArray();
        $data['recherche'] = $recherche;
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
