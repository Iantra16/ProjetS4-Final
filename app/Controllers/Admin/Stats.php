<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Stats extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        $parCategorie = $db->table('produits')
            ->select('categorie, COUNT(*) as total, SUM(stock) as stock_total')
            ->groupBy('categorie')
            ->get()
            ->getResultArray();

        $totalProduits = $db->table('produits')->countAllResults();
        $totalStock    = $db->table('produits')->selectSum('stock')->get()->getRow('stock');
        $prixMoyen     = $db->table('produits')->selectAvg('prix')->get()->getRow('prix');

        $data['title']         = 'Statistiques';
        $data['parCategorie']  = $parCategorie;
        $data['totalProduits'] = $totalProduits;
        $data['totalStock']    = $totalStock;
        $data['prixMoyen']     = round($prixMoyen, 2);

        return view('Admin/stats', $data);
    }
}