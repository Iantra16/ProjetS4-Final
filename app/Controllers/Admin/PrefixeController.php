<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PrefixeOperateurModel;

class PrefixeController extends BaseController
{
    public function index()
    {
        $model = new PrefixeOperateurModel();
        $data['prefixes'] = $model->findAll();
        $data['title'] = 'Gestion des préfixes';
        return view('Admin/prefixes/index', $data);
    }

    public function nouveau()
    {
        $data['title'] = 'Ajouter un préfixe';
        return view('Admin/prefixes/form', $data);
    }

    public function creer()
    {
        $model = new PrefixeOperateurModel();
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
