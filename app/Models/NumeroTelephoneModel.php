<?php

namespace App\Models;

use CodeIgniter\Model;

class NumeroTelephoneModel extends Model
{
    protected $table = 'numero_telephone';
    protected $allowedFields = ['id_prefixe', 'numero', 'date_creation'];

    public function trouverParNumero(string $numero): ?array
    {
        return $this->where('numero', $numero)->first();
    }

    public function creerCompte(int $idPrefixe, string $numero): int
    {
        $this->insert([
            'id_prefixe'    => $idPrefixe,
            'numero'        => $numero,
            'date_creation' => date('Y-m-d H:i:s'),
        ]);
        return $this->insertID();
    }

    public function avecSoldeActuel(): array
    {
        return $this->db->table('numero_telephone nt')
            ->select('nt.id, nt.numero, po.nom as operateur, s.montant as solde_actuel, s.date as date_solde')
            ->join('prefixe_operateur po', 'po.id = nt.id_prefixe')
            ->join('solde s', 's.id = (SELECT id FROM solde WHERE id_numero_tel = nt.id ORDER BY id DESC LIMIT 1)', 'left')
            ->get()->getResultArray();
    }
}
