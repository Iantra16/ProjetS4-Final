<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $allowedFields = ['id_type_operation', 'id_numero_tel', 'id_numero_tel_dest', 'montant', 'frais', 'date'];

    public function gainsParType(): array
    {
        return $this->db->table('operation')
            ->select('type_operation.nom, SUM(operation.frais) as total_frais, COUNT(*) as nb_operations')
            ->join('type_operation', 'type_operation.id = operation.id_type_operation')
            ->groupBy('type_operation.nom')
            ->get()->getResultArray();
    }

    public function gainsParPeriode(?string $debut = null, ?string $fin = null): array
    {
        $builder = $this->db->table('operation')
            ->select('type_operation.nom, SUM(operation.frais) as total_frais, COUNT(*) as nb_operations')
            ->join('type_operation', 'type_operation.id = operation.id_type_operation');
        if ($debut) $builder->where('operation.date >=', $debut);
        if ($fin)   $builder->where('operation.date <=', $fin . ' 23:59:59');
        return $builder->groupBy('type_operation.nom')->get()->getResultArray();
    }

    public function historiquePourNumero(int $idNumero): array
    {
        return $this->db->table('operation o')
            ->select('o.*, t.nom as type_nom, nd.numero as numero_dest')
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->join('numero_telephone nd', 'nd.id = o.id_numero_tel_dest', 'left')
            ->groupStart()
                ->where('o.id_numero_tel', $idNumero)
                ->orWhere('o.id_numero_tel_dest', $idNumero)
            ->groupEnd()
            ->orderBy('o.date', 'DESC')
            ->get()->getResultArray();
    }
}
