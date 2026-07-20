<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TypeOperationModel;

class TypeOperationController extends BaseController
{
    public function index()
    {
        $model = new TypeOperationModel();
        $recherche = $this->request->getGet('q') ?: null;

        $builder = $model->builder();
        if ($recherche) {
            $builder->like('nom', $recherche);
        }
        $data['types'] = $builder->orderBy('id', 'ASC')->get()->getResultArray();
        $data['recherche'] = $recherche;
        $data['title'] = 'Types d\'opération';
        return view('Admin/types_operation/index', $data);
    }

    public function tranchesJson(int $idType)
    {
        $tranches = (new \App\Models\TranchesFraisModel())->parType($idType);
        return $this->response->setJSON($tranches);
    }

    public function nouveau()
    {
        $data['title'] = 'Ajouter un type d\'opération';
        return view('Admin/types_operation/form', $data);
    }

    public function creer()
    {
        $model = new TypeOperationModel();
        if (!$this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez corriger les erreurs.');
        }
        $model->insert($this->request->getPost());
        return redirect()->to('/admin/types-operation')->with('success', 'Type d\'opération ajouté.');
    }

    public function modifier(int $id)
    {
        $model = new TypeOperationModel();
        $data['type'] = $model->find($id);
        $data['title'] = 'Modifier le type d\'opération';
        return view('Admin/types_operation/form', $data);
    }

    public function mettreAJour(int $id)
    {
        $model = new TypeOperationModel();
        if (!$this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez corriger les erreurs.');
        }
        $model->update($id, $this->request->getPost());
        return redirect()->to('/admin/types-operation')->with('success', 'Type d\'opération modifié.');
    }

    public function supprimer(int $id)
    {
        $model = new TypeOperationModel();
        $operateur = new \App\Models\OperationModel();
        $nbOperations = $operateur->where('id_type_operation', $id)->countAllResults();
        if ($nbOperations > 0) {
            return redirect()->back()->with('error', 'Ce type est utilisé par des opérations existantes.');
        }
        $model->delete($id);
        return redirect()->to('/admin/types-operation')->with('success', 'Type d\'opération supprimé.');
    }
}
