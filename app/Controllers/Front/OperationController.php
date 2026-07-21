<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\OperationModel;
use App\Models\OperateurModel;
use App\Models\TypeOperationModel;
use App\Models\TranchesFraisModel;
use App\Models\NumeroTelephoneModel;
use App\Models\SoldeepargneModel;
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

        $senderNum = (new NumeroTelephoneModel())->find(session()->get('numero_id'));
        $senderOp  = $senderNum ? (new NumeroTelephoneModel())->operateurDuNumero($senderNum['numero']) : null;
        $data['senderOperateurId'] = $senderOp ? $senderOp['id'] : null;

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

    public function operateurDuNumeroJson()
    {
        $numero = $this->request->getGet('numero');
        $operateur = (new NumeroTelephoneModel())->operateurDuNumero($numero);
        return $this->response->setJSON(['operateur' => $operateur]);
    }

    public function enregistrer()
    {
        $typeNom       = $this->request->getPost('type_operation');
        $montant       = (float) $this->request->getPost('montant');
        $idNumero      = session()->get('numero_id');
        $soldeModel    = new SoldeModel();
        $typeModel     = new TypeOperationModel();
        $tranchesModel = new TranchesFraisModel();
        $numeroModel   = new NumeroTelephoneModel();
        $operationModel = new OperationModel();
        $operateurModel = new OperateurModel();

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
            $inclureFrais = $this->request->getPost('inclure_frais') === 'on';
            $db = \Config\Database::connect();
            $db->transStart();

            $numeros = array_filter($this->request->getPost('numero_dest'), fn($n) => !empty(trim($n)));
            $montantGlobal = (float)$this->request->getPost('montant');
            $nbDest = count($numeros);

            if ($nbDest === 0) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Au moins un destinataire est requis.');
            }

            $senderNum = $numeroModel->find($idNumero);
            $senderOp  = $senderNum ? $numeroModel->operateurDuNumero($senderNum['numero']) : null;
            $senderOpId = $senderOp ? $senderOp['id'] : null;

            $montParDest = $montantGlobal / $nbDest;

            $montantTotal = 0;
            // Envoi multiple : l'expéditeur et tous les destinataires doivent être du même opérateur
            $operateurCommuns = ($nbDest >= 2) ? $senderOpId : null;

            for ($i = 0; $i < $nbDest; $i++) {
                $num = trim($numeros[$i]);
                $mont = $montParDest;
                $dest = $numeroModel->trouverParNumero($num);
                $opDest = $numeroModel->operateurDuNumero($num);

                if (!$dest) {
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('error', "Le numéro {$num} n'existe pas.");
                }

                if ($operateurCommuns === null) $operateurCommuns = $opDest['id'];
                else if ($operateurCommuns !== $opDest['id']) {
                    $db->transRollback();
                    return redirect()->back()->withInput()->with('error', "L'expéditeur et les destinataires doivent être du même opérateur.");
                }

                $promotion = $operateurModel->getPromotion();
                $frais = $tranchesModel->calculerFrais($mont, $type['id']);
                // "il n'y a pas de frais de retrait pour les autres opérateurs" -> Si externe, frais = 0
                if (!$opDest['est_notre_operateur']) $frais = 0 ;

                $commission = (!$opDest['est_notre_operateur']) ? ($mont * $opDest['commission_exterieur']) : 0;
                $promotion = (!$opDest['est_notre_operateur']) ? ($mont * $opDest['commission_exterieur']) : 0;

                // Logique "Inclure frais"
                if ($inclureFrais) {
                    $cout = $mont + $frais;
                    $montantFinalEnvoye = $mont;
                } else {
                    $cout = $mont;
                    $montantFinalEnvoye = $mont - $frais;
                }
                
                $montantTotal += $cout;

                $operationModel->insert([
                    'id_type_operation'  => $type['id'],
                    'id_numero_tel'      => $idNumero,
                    'id_numero_tel_dest' => $dest['id'],
                    'montant'            => $montantFinalEnvoye,
                    'frais'              => $frais,
                    'commission'         => $commission,
                    'date'               => date('Y-m-d H:i:s'),
                ]);
                
                // Destinataire reçoit montant + commission

                $repartition = $soldeModel->repatimentmontant($dest['id'] , $montantFinalEnvoye + $commission);
                $soldeModel->insererNouveauSolde($dest['id'], $repartition['solde']);
                (new SoldeepargneModel())->insererNouveauSolde($dest['id'], $repartition['epargne']);
                
            }

            if ($montantSolde < $montantTotal) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', "Solde insuffisant.");
            }

            $soldeModel->insererNouveauSolde($idNumero, -$montantTotal);
            $db->transComplete();

            if ($db->transStatus() === false) {
                return redirect()->back()->withInput()->with('error', 'Erreur lors du transfert.');
            }
            return redirect()->to('/client/solde')->with('success', "Transfert effectué.");
        }

        return redirect()->back()->withInput()->with('error', 'Type d\'opération inconnu.');
    }

    public function historique()
    {
        $idNumero = session()->get('numero_id');
        $model = new OperationModel();

        $type       = $this->request->getGet('type') ?: null;
        $montantMin = $this->request->getGet('montant_min') !== null && $this->request->getGet('montant_min') !== '' ? (float) $this->request->getGet('montant_min') : null;
        $montantMax = $this->request->getGet('montant_max') !== null && $this->request->getGet('montant_max') !== '' ? (float) $this->request->getGet('montant_max') : null;
        $dateDebut  = $this->request->getGet('date_debut') ?: null;
        $dateFin    = $this->request->getGet('date_fin') ?: null;

        // Si aucun filtre n'est appliqué, on affiche tout l'historique
        if (!$type && $montantMin === null && $montantMax === null && !$dateDebut && !$dateFin) {
            $data['operations'] = $model->historiquePourNumero($idNumero);
        } else {
            $data['operations'] = $model->historiqueFiltre($idNumero, $type, $montantMin, $montantMax, $dateDebut, $dateFin);
        }

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
