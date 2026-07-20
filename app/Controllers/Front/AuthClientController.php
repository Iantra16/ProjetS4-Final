<?php

namespace App\Controllers\Front;

use App\Controllers\BaseController;
use App\Models\NumeroTelephoneModel;
use App\Models\PrefixeOperateurModel;
use App\Models\SoldeModel;

class AuthClientController extends BaseController
{
    public function login()
    {
        // Si déjà connecté → rediriger vers le solde
        if (session()->get('numero_id')) {
            return redirect()->to('/client/solde');
        }

        if ($this->request->is('post')) {
            $numero = trim($this->request->getPost('numero'));

            // Validation basique : 10 chiffres
            if (!preg_match('/^\d{10}$/', $numero)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Le numéro doit contenir exactement 10 chiffres.');
            }

            // Extraire le préfixe (3 premiers chiffres)
            $prefixe = substr($numero, 0, 3);

            $prefixeModel = new PrefixeOperateurModel();
            $prefixeData  = $prefixeModel->findByPrefixe($prefixe);

            if (!$prefixeData) {
                return redirect()->back()->withInput()
                    ->with('error', "L'opérateur avec le préfixe « {$prefixe} » n'est pas reconnu.");
            }

            $numeroModel = new NumeroTelephoneModel();
            $numeroData  = $numeroModel->findByNumero($numero);

            if ($numeroData) {
                // Compte existant → connexion directe
                session()->set([
                    'numero_id' => $numeroData['id'],
                    'numero'    => $numeroData['numero'],
                ]);
            } else {
                // Compte inexistant → création automatique
                $idNouveauNumero = $numeroModel->creerCompte($prefixeData['id'], $numero);

                // Solde initial à 0
                $soldeModel = new SoldeModel();
                $soldeModel->insert([
                    'id_numero_tel' => $idNouveauNumero,
                    'montant'       => 0.0,
                    'date'          => date('Y-m-d H:i:s'),
                ]);

                session()->set([
                    'numero_id' => $idNouveauNumero,
                    'numero'    => $numero,
                ]);

                session()->setFlashdata('success', "Compte créé avec succès ! Bienvenue {$numero}.");
            }

            return redirect()->to('/client/solde');
        }

        return view('Front/auth/login', ['title' => 'Espace Client']);
    }

    public function logout()
    {
        session()->remove(['numero_id', 'numero']);
        return redirect()->to('/client/login')->with('success', 'Déconnexion réussie.');
    }
}
