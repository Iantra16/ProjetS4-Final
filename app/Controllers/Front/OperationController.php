<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\OperationModel;
use App\Models\TypeOperationModel;
use App\Models\TranchesFraisModel;
use App\Models\NumeroTelephoneModel;
use App\Models\SoldeModel;

class OperationController extends BaseController
{
    public function depot()
    {
        if ($this->request->is('post')) {
            $montant = (float) $this->request->getPost('montant');

            if ($montant <= 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Le montant doit être supérieur à 0.');
            }

            $typeModel = new TypeOperationModel();
            $type = $typeModel->where('nom', 'depot')->first();

            $operationModel = new OperationModel();
            $operationModel->insert([
                'id_type_operation' => $type['id'],
                'id_numero_tel'     => session()->get('numero_id'),
                'montant'           => $montant,
                'frais'             => 0.0,
                'date'              => date('Y-m-d H:i:s'),
            ]);

            $soldeModel = new SoldeModel();
            $soldeModel->insererNouveauSolde(session()->get('numero_id'), $montant);

            return redirect()->to('/client/solde')
                ->with('success', "Dépôt de " . number_format($montant, 0, ',', ' ') . " F effectué.");
        }

        return view('Front/depot', [
            'title'  => 'Dépôt',
            'solde'  => (new SoldeModel())->dernierSolde(session()->get('numero_id')),
        ]);
    }

    public function retrait()
    {
        $soldeModel = new SoldeModel();
        $typeModel = new TypeOperationModel();
        $tranchesModel = new TranchesFraisModel();

        if ($this->request->is('post')) {
            $montant = (float) $this->request->getPost('montant');

            if ($montant <= 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Le montant doit être supérieur à 0.');
            }

            $type = $typeModel->where('nom', 'retrait')->first();
            $frais = $tranchesModel->calculerFrais($montant, $type['id']);
            $total = $montant + $frais;

            $soldeActuel = $soldeModel->dernierSolde(session()->get('numero_id'));
            $montantSolde = $soldeActuel ? (float) $soldeActuel['montant'] : 0.0;

            if ($montantSolde < $total) {
                return redirect()->back()->withInput()
                    ->with('error', "Solde insuffisant. Solde actuel : " . number_format($montantSolde, 0, ',', ' ') . " F, total needed : " . number_format($total, 0, ',', ' ') . " F (montant + frais).");
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $operationModel = new OperationModel();
            $operationModel->insert([
                'id_type_operation' => $type['id'],
                'id_numero_tel'     => session()->get('numero_id'),
                'montant'           => $montant,
                'frais'             => $frais,
                'date'              => date('Y-m-d H:i:s'),
            ]);

            $soldeModel->insererNouveauSolde(session()->get('numero_id'), -$total);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()
                    ->with('error', 'Erreur lors du retrait. Veuillez réessayer.');
            }

            return redirect()->to('/client/solde')
                ->with('success', "Retrait de " . number_format($montant, 0, ',', ' ') . " F effectué (frais : " . number_format($frais, 0, ',', ' ') . " F).");
        }

        $type = $typeModel->where('nom', 'retrait')->first();
        $soldeActuel = $soldeModel->dernierSolde(session()->get('numero_id'));
        $montantSolde = $soldeActuel ? (float) $soldeActuel['montant'] : 0.0;

        return view('Front/retrait', [
            'title'        => 'Retrait',
            'frais'        => $tranchesModel->parType($type['id']),
            'montantSolde' => $montantSolde,
        ]);
    }

    public function transfert()
    {
        $soldeModel = new SoldeModel();
        $typeModel = new TypeOperationModel();
        $tranchesModel = new TranchesFraisModel();

        if ($this->request->is('post')) {
            $montant    = (float) $this->request->getPost('montant');
            $numeroDest = trim($this->request->getPost('numero_dest'));

            if ($montant <= 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Le montant doit être supérieur à 0.');
            }

            if (empty($numeroDest)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Veuillez saisir le numéro du destinataire.');
            }

            if ($numeroDest === session()->get('numero')) {
                return redirect()->back()->withInput()
                    ->with('error', 'Vous ne pouvez pas effectuer un transfert vers votre propre numéro.');
            }

            $numeroModel = new NumeroTelephoneModel();
            $destinataire = $numeroModel->trouverParNumero($numeroDest);

            if (!$destinataire) {
                return redirect()->back()->withInput()
                    ->with('error', "Le numéro {$numeroDest} n'existe pas.");
            }

            $type = $typeModel->where('nom', 'transfert')->first();
            $frais = $tranchesModel->calculerFrais($montant, $type['id']);
            $total = $montant + $frais;

            $soldeActuel = $soldeModel->dernierSolde(session()->get('numero_id'));
            $montantSolde = $soldeActuel ? (float) $soldeActuel['montant'] : 0.0;

            if ($montantSolde < $total) {
                return redirect()->back()->withInput()
                    ->with('error', "Solde insuffisant. Solde actuel : " . number_format($montantSolde, 0, ',', ' ') . " F, total needed : " . number_format($total, 0, ',', ' ') . " F (montant + frais).");
            }

            $db = \Config\Database::connect();
            $db->transStart();

            $operationModel = new OperationModel();
            $operationModel->insert([
                'id_type_operation'   => $type['id'],
                'id_numero_tel'       => session()->get('numero_id'),
                'id_numero_tel_dest'  => $destinataire['id'],
                'montant'             => $montant,
                'frais'               => $frais,
                'date'                => date('Y-m-d H:i:s'),
            ]);

            $soldeModel->insererNouveauSolde(session()->get('numero_id'), -$total);
            $soldeModel->insererNouveauSolde($destinataire['id'], $montant);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()
                    ->with('error', 'Erreur lors du transfert. Veuillez réessayer.');
            }

            return redirect()->to('/client/solde')
                ->with('success', "Transfert de " . number_format($montant, 0, ',', ' ') . " F vers {$numeroDest} effectué (frais : " . number_format($frais, 0, ',', ' ') . " F).");
        }

        $type = $typeModel->where('nom', 'transfert')->first();
        $soldeActuel = $soldeModel->dernierSolde(session()->get('numero_id'));
        $montantSolde = $soldeActuel ? (float) $soldeActuel['montant'] : 0.0;

        return view('Front/transfert', [
            'title'        => 'Transfert',
            'frais'        => $tranchesModel->parType($type['id']),
            'montantSolde' => $montantSolde,
        ]);
    }

    public function historique()
    {
        $idNumero = session()->get('numero_id');
        $model = new OperationModel();

        $data['operations'] = $model->historiquePourNumero($idNumero);
        $data['monId']      = $idNumero;
        $data['title']      = 'Mon historique';

        return view('Front/historique', $data);
    }
}
