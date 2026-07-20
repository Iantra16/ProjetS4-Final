<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table = 'prefixe_operateur';
    protected $allowedFields = ['prefixe', 'nom'];

    protected $validationRules = [
        'prefixe' => 'required|exact_length[3]|is_natural_no_zero|is_unique[prefixe_operateur.prefixe,id,{id}]',
        'nom'     => 'required|min_length[2]',
    ];

    public function estUtilise(int $id): bool
    {
        return $this->db->table('numero_telephone')
            ->where('id_prefixe', $id)
            ->countAllResults() > 0;
    }
}
