<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'solde';
    protected $allowedFields = ['id_numero_tel', 'montant', 'date'];

    public function dernierSolde(int $idNumeroTel): ?array
    {
        return $this->where('id_numero_tel', $idNumeroTel)
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    public function insererNouveauSolde(int $idNumeroTel, float $delta): void
    {
        $ancien = $this->dernierSolde($idNumeroTel);
        $ancienMontant = $ancien ? (float) $ancien['montant'] : 0.0;
        $this->insert([
            'id_numero_tel' => $idNumeroTel,
            'montant'       => $ancienMontant + $delta,
            'date'          => date('Y-m-d H:i:s'),
        ]);
    }
}
