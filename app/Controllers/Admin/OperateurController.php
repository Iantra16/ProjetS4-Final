<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\OperateurModel;

class OperateurController extends BaseController
{
    protected $operateurModel;

    public function __construct()
    {
        $this->operateurModel = new OperateurModel();
    }

    public function index()
    {
        $data['operateurs'] = $this->operateurModel->findAll();
        return view('Admin/operateurs/index', $data);
    }

    public function nouveau()
    {
        return view('Admin/operateurs/form');
    }

    public function creer()
    {
        $this->operateurModel->save([
            'nom' => $this->request->getPost('nom'),
            'est_notre_operateur' => 0, // Force external operator
            'commission_exterieur' => ($this->request->getPost('commission_exterieur') ?? 0) / 100,
        ]);
        return redirect()->to('/admin/operateurs');
    }

    public function modifier($id)
    {
        $data['operateur'] = $this->operateurModel->find($id);
        return view('Admin/operateurs/form', $data);
    }

    public function mettreAJour($id)
    {
        $this->operateurModel->update($id, [
            'nom' => $this->request->getPost('nom'),
            'est_notre_operateur' => 0, // Force external operator
            'commission_exterieur' => ($this->request->getPost('commission_exterieur') ?? 0) / 100,
        ]);
        return redirect()->to('/admin/operateurs');
    }

    public function supprimer($id)
    {
        $this->operateurModel->delete($id);
        return redirect()->to('/admin/operateurs');
    }
}
