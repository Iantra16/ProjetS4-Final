<?php

namespace App\Models;

use CodeIgniter\Model;

class PrefixeOperateurModel extends Model
{
    protected $table = 'prefixe_operateur';
    protected $allowedFields = ['prefixe', 'nom', 'id_operateur'];

    protected $validationRules = [
        'prefixe' => 'required|exact_length[3]|is_natural_no_zero|is_unique[prefixe_operateur.prefixe,id,{id}]',
        'nom'     => 'required|min_length[2]',
        'id_operateur' => 'required|is_natural_no_zero',
    ];

    public function estExterieur(int $idPrefixe): bool
    {
        $prefixe = $this->find($idPrefixe);
        if (!$prefixe) return false;
        
        $operateurModel = new OperateurModel();
        $operateur = $operateurModel->find($prefixe['id_operateur']);
        
        return $operateur && !$operateur['est_notre_operateur'];
    }

    public function estUtilise(int $id): bool
    {
        return $this->db->table('numero_telephone')
            ->where('id_prefixe', $id)
            ->countAllResults() > 0;
    }

    public function trouverParPrefixe(string $prefixe): ?array
    {
        return $this->where('prefixe', $prefixe)->first();
    }
}
