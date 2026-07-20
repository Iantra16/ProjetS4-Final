<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrefixeOperateurModel;
use App\Models\OperateurModel;

class PrefixeController extends BaseController
{
    public function index()
    {
        $model = new PrefixeOperateurModel();
        $recherche = $this->request->getGet('q') ?: null;

        $builder = $model->builder()
            ->select('prefixe_operateur.*, operateur.nom as operateur_nom')
            ->join('operateur', 'operateur.id = prefixe_operateur.id_operateur', 'left');

        if ($recherche) {
            $builder->groupStart()
                ->like('prefixe', $recherche)
            ->groupEnd();
        }
        $data['prefixes'] = $builder->orderBy('prefixe_operateur.id', 'ASC')->get()->getResultArray();
        $data['recherche'] = $recherche;
        $data['title'] = 'Gestion des préfixes';
        return view('Admin/prefixes/index', $data);
    }

    public function nouveau()
    {
        $data['title'] = 'Ajouter un préfixe';
        $data['operateurs'] = (new OperateurModel())->findAll();
        return view('Admin/prefixes/form', $data);
    }

    public function creer()
    {
        $model = new PrefixeOperateurModel();
        // Modification temporaire pour validation, inclure id_operateur
        if (!$this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez corriger les erreurs.');
        }
        $model->insert($this->request->getPost());
        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe ajouté.');
    }

    public function modifier(int $id)
    {
        $model = new PrefixeOperateurModel();
        $data['prefixe'] = $model->find($id);
        $data['operateurs'] = (new OperateurModel())->findAll();
        $data['title'] = 'Modifier le préfixe';
        return view('Admin/prefixes/form', $data);
    }

    public function mettreAJour(int $id)
    {
        $model = new PrefixeOperateurModel();
        if (!$this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez corriger les erreurs.');
        }
        $model->update($id, $this->request->getPost());
        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe modifié.');
    }

    public function supprimer(int $id)
    {
        $model = new PrefixeOperateurModel();
        if ($model->estUtilise($id)) {
            return redirect()->back()->with('error', 'Ce préfixe est utilisé par des numéros existants.');
        }
        $model->delete($id);
        return redirect()->to('/admin/prefixes')->with('success', 'Préfixe supprimé.');
    }
}
