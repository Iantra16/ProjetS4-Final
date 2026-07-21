<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeepargneModel extends Model
{
    protected $table = 'solde_eparnge';
    protected $allowedFields = ['id_numero_tel', 'montant', 'date'];

    public function dernierSolde(int $idNumeroTel): ?array
    {
        return $this->where('id_numero_tel', $idNumeroTel)
                    ->orderBy('id', 'DESC')
                    ->first();
    }

    public function dernierSoldeAvantDate(int $idNumeroTel, string $date): ?array
    {
        return $this->where('id_numero_tel', $idNumeroTel)
                    ->where('date <=', $date . ' 23:59:59')
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

        public function repatimentmontant(int $idNumeroTel, float $montant): array
    {
        $modetel = new NumeroTelephoneModel();
        $client = $modetel->find($idNumeroTel);
        
        $taux = (float)($client["taux_epargne"] ?? 0); 
        $montantEpargne = $montant * ($taux / 100);
        $montantSolde = $montant - $montantEpargne;
        
        return [
            'solde' => $montantSolde ,
            'epargne' => $montantEpargne
        ];
    }

}
