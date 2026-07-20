<?php

namespace App\Models;

use CodeIgniter\Model;

class NumeroTelephoneModel extends Model
{
    protected $table         = 'numero_telephone';
    protected $allowedFields = ['id_prefixe', 'numero', 'date_creation'];

    public function findByNumero(string $numero): ?array
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
}
