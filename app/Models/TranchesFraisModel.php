<?php

namespace App\Models;

use CodeIgniter\Model;

class TranchesFraisModel extends Model
{
    protected $table = 'tranches_frais';
    protected $allowedFields = ['id_type_operation', 'montant_min', 'montant_max', 'montant_frais', 'date'];

    protected $validationRules = [
        'id_type_operation' => 'required|is_natural_no_zero',
        'montant_min'       => 'required|numeric',
        'montant_max'       => 'required|numeric',
        'montant_frais'     => 'required|numeric',
    ];

    public function toutesTriees(): array
    {
        return $this->db->table($this->table . ' tf')
            ->select('tf.*, t.nom as type_nom')
            ->join('type_operation t', 't.id = tf.id_type_operation')
            ->orderBy('tf.id_type_operation', 'ASC')
            ->orderBy('tf.montant_min', 'ASC')
            ->get()->getResultArray();
    }

    public function parType(int $idTypeOperation): array
    {
        return $this->where('id_type_operation', $idTypeOperation)
                    ->orderBy('montant_min', 'ASC')
                    ->findAll();
    }

    public function chevauchementExiste(int $idTypeOperation, float $min, float $max, ?int $excludeId = null): bool
    {
        $builder = $this->db->table($this->table)
            ->where('id_type_operation', $idTypeOperation)
            ->where('montant_min <', $max)
            ->where('montant_max >', $min);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    public function trouverTranche(float $montant, int $idTypeOperation): ?array
    {
        return $this->where('id_type_operation', $idTypeOperation)
                    ->where('montant_min <=', $montant)
                    ->where('montant_max >=', $montant)
                    ->first();
    }

    public function calculerFrais(float $montant, int $idTypeOperation): float
    {
        $tranche = $this->trouverTranche($montant, $idTypeOperation);
        return $tranche ? (float) $tranche['montant_frais'] : 0.0;
    }
}
