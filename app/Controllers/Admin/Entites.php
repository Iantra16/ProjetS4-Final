<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProduitModel;

class Entites extends BaseController
{
    protected $model;

    public function __construct()
    {
        $this->model = new ProduitModel();
    }

    public function index()
    {
        $keyword   = $this->request->getGet('q');
        $categorie = $this->request->getGet('categorie');

        $data['produits']  = $this->model->rechercheEtFiltre($keyword, $categorie)->paginate(10, 'produits');
        $data['pager']     = $this->model->pager;
        $data['keyword']   = $keyword;
        $data['categorie'] = $categorie;
        $data['title']     = 'Gestion des produits';

        return view('Admin/entites/index', $data);
    }

    public function new()
    {
        return view('Admin/entites/form', ['title' => 'Nouveau produit']);
    }

    public function create()
    {
        if (!$this->model->save($this->request->getPost())) {
            return redirect()->back()->withInput()->with('error', 'Erreur de validation');
        }
        return redirect()->to('/admin/entites')->with('success', 'Produit ajouté');
    }

    public function edit($id)
    {
        $produit = $this->model->find($id);
        if (!$produit) {
            return redirect()->to('/admin/entites')->with('error', 'Produit introuvable');
        }
        return view('Admin/entites/form', ['title' => 'Modifier produit', 'produit' => $produit]);
    }

    public function update($id)
    {
        $this->model->update($id, $this->request->getPost());
        return redirect()->to('/admin/entites')->with('success', 'Produit modifié');
    }

    public function delete($id)
    {
        $this->model->delete($id);
        return redirect()->to('/admin/entites')->with('success', 'Produit supprimé');
    }

    public function exportPdf()
    {
        $produits = $this->model->findAll();

        $lines = [];
        $lines[] = 'Liste des produits';
        $lines[] = 'Genere le ' . date('d/m/Y a H:i');
        $lines[] = '';
        $lines[] = str_pad('Nom', 25) . str_pad('Categorie', 20) . str_pad('Prix', 12) . 'Stock';
        $lines[] = str_repeat('-', 70);

        foreach ($produits as $p) {
            $lines[] = str_pad($p['nom'], 25) . str_pad($p['categorie'], 20) . str_pad((string) $p['prix'], 12) . $p['stock'];
        }

        $pdf = new \App\Libraries\SimplePdf();
        $pdf->download('produits.pdf', $lines);
    }

    public function exportExcel()
    {
        $produits = $this->model->findAll();

        $rows = [];
        foreach ($produits as $p) {
            $rows[] = [$p['nom'], $p['categorie'], $p['prix'], $p['stock']];
        }

        $csv = new \App\Libraries\SimpleCsv();
        $csv->download('produits.csv', ['Nom', 'Catégorie', 'Prix', 'Stock'], $rows);
    }

    public function importForm()
    {
        return view('Admin/entites/import', ['title' => 'Importer des produits']);
    }

    public function import()
    {
        $file = $this->request->getFile('fichier');

        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Aucun fichier valide envoyé');
        }

        if ($file->getExtension() !== 'csv') {
            return redirect()->back()->with('error', 'Le fichier doit être un .csv');
        }

        $handle = fopen($file->getTempName(), 'r');

        // Enlever le BOM UTF-8 s'il existe (généré par notre propre export)
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF) . chr(0xBB) . chr(0xBF)) {
            rewind($handle);
        }

        $header = fgetcsv($handle, 0, ';'); // on ignore la ligne d'en-tête
        $count = 0;

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            if (empty($row[0])) {
                continue; // ignore les lignes vides
            }

            $this->model->insert([
                'nom'       => $row[0],
                'categorie' => $row[1] ?? '',
                'prix'      => $row[2] ?? 0,
                'stock'     => $row[3] ?? 0,
            ]);
            $count++;
        }

        fclose($handle);

        return redirect()->to('/admin/entites')->with('success', $count . ' produit(s) importé(s)');
    }
}
