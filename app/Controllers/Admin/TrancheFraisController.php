<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TranchesFraisModel;
use App\Models\TypeOperationModel;

class TrancheFraisController extends BaseController
{
    public function index()
    {
        $model = new TranchesFraisModel();
        $data['tranches'] = $model->toutesTriees();
        $data['title'] = 'Tranches de frais';
        return view('Admin/tranches/index', $data);
    }

    public function nouveau()
    {
        $data['types'] = (new TypeOperationModel())->findAll();
        $data['title'] = 'Ajouter une tranche';
        return view('Admin/tranches/form', $data);
    }

    public function creer()
    {
        $model = new TranchesFraisModel();
        if (!$this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez corriger les erreurs.');
        }
        $idType = (int) $this->request->getPost('id_type_operation');
        $min = (float) $this->request->getPost('montant_min');
        $max = (float) $this->request->getPost('montant_max');
        if ($max <= $min) {
            return redirect()->back()->withInput()->with('error', 'Le montant max doit être supérieur au montant min.');
        }
        if ($model->chevauchementExiste($idType, $min, $max)) {
            return redirect()->back()->withInput()->with('error', 'Cette tranche chevauche une tranche existante pour ce type.');
        }
        $model->insert($this->request->getPost());
        return redirect()->to('/admin/tranches')->with('success', 'Tranche ajoutée.');
    }

    public function modifier(int $id)
    {
        $model = new TranchesFraisModel();
        $data['tranche'] = $model->find($id);
        $data['types'] = (new TypeOperationModel())->findAll();
        $data['title'] = 'Modifier la tranche';
        return view('Admin/tranches/form', $data);
    }

    public function mettreAJour(int $id)
    {
        $model = new TranchesFraisModel();
        if (!$this->validate($model->validationRules)) {
            return redirect()->back()->withInput()->with('error', 'Veuillez corriger les erreurs.');
        }
        $idType = (int) $this->request->getPost('id_type_operation');
        $min = (float) $this->request->getPost('montant_min');
        $max = (float) $this->request->getPost('montant_max');
        if ($max <= $min) {
            return redirect()->back()->withInput()->with('error', 'Le montant max doit être supérieur au montant min.');
        }
        if ($model->chevauchementExiste($idType, $min, $max, $id)) {
            return redirect()->back()->withInput()->with('error', 'Cette tranche chevauche une tranche existante pour ce type.');
        }
        $model->update($id, $this->request->getPost());
        return redirect()->to('/admin/tranches')->with('success', 'Tranche modifiée.');
    }

    public function supprimer(int $id)
    {
        $model = new TranchesFraisModel();
        $model->delete($id);
        return redirect()->to('/admin/tranches')->with('success', 'Tranche supprimée.');
    }
}
