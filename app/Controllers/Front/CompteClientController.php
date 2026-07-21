<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\NumeroTelephoneModel;
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
    
    public function mettreAJour($id)
    {
        $estNotre = $this->request->getPost('est_notre_operateur') === '1';
        $this->operateurModel->update($id, [
            'nom' => $this->request->getPost('nom'),
            'est_notre_operateur' => $estNotre ? 1 : 0,
            'commission_exterieur' => $estNotre ? 0.0 : ($this->request->getPost('commission_exterieur') ?? 0) / 100,
            'promotion' => $estNotre ? 0.0 : ($this->request->getPost('promotion') ?? 0) / 100,
        ]);
        return redirect()->to('/admin/operateurs');
    }
}
