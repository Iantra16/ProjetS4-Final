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
    public function operation()
    {
        $typeModel = new TypeOperationModel();
        $soldeModel = new SoldeModel();

        $data['types']        = $typeModel->findAll();
        $data['solde']        = $soldeModel->dernierSolde(session()->get('numero_id'));
        $data['title']        = 'Effectuer une opération';
        $data['typesJson']    = $data['types'];

        return view('Front/operation', $data);
    }

    public function tranchesJson()
    {
        $idType = (int) $this->request->getGet('type_id');
        $tranches = (new TranchesFraisModel())->parType($idType);
        return $this->response->setJSON($tranches);
    }

    public function numeroExisteJson()
    {
        $numero = $this->request->getGet('numero');
        $existe = (new NumeroTelephoneModel())->trouverParNumero($numero) !== null;
        return $this->response->setJSON(['existe' => $existe]);
    }

    public function enregistrer()
    {
        $typeNom       = $this->request->getPost('type_operation');
        $montant       = (float) $this->request->getPost('montant');
        $numeroDest    = trim((string) $this->request->getPost('numero_dest'));
        $idNumero      = session()->get('numero_id');
        $soldeModel    = new SoldeModel();
        $typeModel     = new TypeOperationModel();
        $tranchesModel = new TranchesFraisModel();
        $numeroModel   = new NumeroTelephoneModel();
        $operationModel = new OperationModel();

        if ($montant <= 0) {
            return redirect()->back()->withInput()->with('error', 'Le montant doit être supérieur à 0.');
        }

        $type = $typeModel->where('nom', $typeNom)->first();
        if (!$type) {
            return redirect()->back()->withInput()->with('error', 'Type d\'opération invalide.');
        }

        $soldeActuel = $soldeModel->dernierSolde($idNumero);
        $montantSolde = $soldeActuel ? (float) $soldeActuel['montant'] : 0.0;

        // Dépôt : frais = 0, pas de destinataire
        if ($typeNom === 'depot') {
            $operationModel->insert([
                'id_type_operation' => $type['id'],
                'id_numero_tel'     => $idNumero,
                'montant'           => $montant,
                'frais'             => 0.0,
                'date'              => date('Y-m-d H:i:s'),
            ]);
            $soldeModel->insererNouveauSolde($idNumero, $montant);
            return redirect()->to('/client/solde')->with('success', "Dépôt de " . number_format($montant, 0, ',', ' ') . " F effectué.");
        }

        // Retrait : frais selon tranche, pas de destinataire
        if ($typeNom === 'retrait') {
            $frais  = $tranchesModel->calculerFrais($montant, $type['id']);
            $total  = $montant + $frais;
            if ($montantSolde < $total) {
                return redirect()->back()->withInput()->with('error', "Solde insuffisant. Solde : " . number_format($montantSolde, 0, ',', ' ') . " F, total requis : " . number_format($total, 0, ',', ' ') . " F.");
            }

            $db = \Config\Database::connect();
            $db->transStart();
            $operationModel->insert([
                'id_type_operation' => $type['id'],
                'id_numero_tel'     => $idNumero,
                'montant'           => $montant,
                'frais'             => $frais,
                'date'              => date('Y-m-d H:i:s'),
            ]);
            $soldeModel->insererNouveauSolde($idNumero, -$total);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors du retrait.');
            }
            return redirect()->to('/client/solde')->with('success', "Retrait de " . number_format($montant, 0, ',', ' ') . " F effectué (frais : " . number_format($frais, 0, ',', ' ') . " F).");
        }

        // Transfert : frais selon tranche, destinataire obligatoire
        if ($typeNom === 'transfert') {
            if (empty($numeroDest)) {
                return redirect()->back()->withInput()->with('error', 'Veuillez saisir le numéro du destinataire.');
            }
            if ($numeroDest === session()->get('numero')) {
                return redirect()->back()->withInput()->with('error', 'Vous ne pouvez pas vous envoyer de l\'argent.');
            }
            $destinataire = $numeroModel->trouverParNumero($numeroDest);
            if (!$destinataire) {
                return redirect()->back()->withInput()->with('error', "Le numéro {$numeroDest} n'existe pas.");
            }

            $opExpediteur = $numeroModel->operateurDuNumero(session()->get('numero'));
            $opDestinataire = $numeroModel->operateurDuNumero($numeroDest);

            $frais  = $tranchesModel->calculerFrais($montant, $type['id']);
            $total  = $montant + $frais;
            $commission = 0.0;

            // Logique externe
            if (!$opDestinataire['est_notre_operateur']) {
                $commission = $montant * $opDestinataire['commission_exterieur'];
            }

            if ($montantSolde < $total) {
                return redirect()->back()->withInput()->with('error', "Solde insuffisant. Solde : " . number_format($montantSolde, 0, ',', ' ') . " F, total requis : " . number_format($total, 0, ',', ' ') . " F.");
            }

            $db = \Config\Database::connect();
            $db->transStart();
            $operationModel->insert([
                'id_type_operation'  => $type['id'],
                'id_numero_tel'      => $idNumero,
                'id_numero_tel_dest' => $destinataire['id'],
                'montant'            => $montant,
                'frais'              => $frais,
                'commission'         => $commission,
                'date'               => date('Y-m-d H:i:s'),
            ]);
            $soldeModel->insererNouveauSolde($idNumero, -$total);
            
            // Destinataire reçoit montant + commission
            $soldeModel->insererNouveauSolde($destinataire['id'], $montant + $commission);
            
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors du transfert.');
            }
            return redirect()->to('/client/solde')->with('success', "Transfert de " . number_format($montant, 0, ',', ' ') . " F vers {$numeroDest} effectué (frais : " . number_format($frais, 0, ',', ' ') . " F).");
        }

        return redirect()->back()->withInput()->with('error', 'Type d\'opération inconnu.');
    }

    public function historique()
    {
        $idNumero = session()->get('numero_id');
        $model = new OperationModel();

        $type       = $this->request->getGet('type') ?: null;
        $montantMin = $this->request->getGet('montant_min') !== '' ? (float) $this->request->getGet('montant_min') : null;
        $montantMax = $this->request->getGet('montant_max') !== '' ? (float) $this->request->getGet('montant_max') : null;
        $dateDebut  = $this->request->getGet('date_debut') ?: null;
        $dateFin    = $this->request->getGet('date_fin') ?: null;

        $data['operations'] = $model->historiqueFiltre($idNumero, $type, $montantMin, $montantMax, $dateDebut, $dateFin);
        $data['monId']      = $idNumero;
        $data['types']      = (new TypeOperationModel())->findAll();
        $data['filters']    = [
            'type'        => $type,
            'montant_min' => $this->request->getGet('montant_min'),
            'montant_max' => $this->request->getGet('montant_max'),
            'date_debut'  => $dateDebut,
            'date_fin'    => $dateFin,
        ];
        $data['title'] = 'Mon historique';

        return view('Front/historique', $data);
    }
}
