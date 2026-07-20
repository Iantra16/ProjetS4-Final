<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $allowedFields = ['id_type_operation', 'id_numero_tel', 'id_numero_tel_dest', 'montant', 'frais', 'date', 'commission'];

    public function gainsParOperateur(): array
    {
        return $this->db->table('operation o')
            ->select('op.nom, op.est_notre_operateur, SUM(o.frais) as total_frais, SUM(o.commission) as total_commission')
            ->join('numero_telephone nt', 'nt.id = o.id_numero_tel')
            ->join('prefixe_operateur po', 'po.id = nt.id_prefixe')
            ->join('operateur op', 'op.id = po.id_operateur')
            ->groupBy('op.nom, op.est_notre_operateur')
            ->get()->getResultArray();
    }

    public function montantsAEnvoyerParOperateur(): array
    {
        return $this->db->table('operation o')
            ->select('op.nom, SUM(o.montant + o.commission) as total_a_envoyer')
            ->join('numero_telephone nt_dest', 'nt_dest.id = o.id_numero_tel_dest')
            ->join('prefixe_operateur po', 'po.id = nt_dest.id_prefixe')
            ->join('operateur op', 'op.id = po.id_operateur')
            ->where('op.est_notre_operateur', 0)
            ->groupBy('op.nom')
            ->get()->getResultArray();
    }


    public function gainsParPeriodeParOperateur(?string $debut = null, ?string $fin = null): array
    {
        $builder = $this->db->table('operation o')
            ->select('op.nom, op.est_notre_operateur, t.nom as type_nom, SUM(o.frais) as total_frais, SUM(o.commission) as total_commission')
            ->join('numero_telephone nt', 'nt.id = o.id_numero_tel', 'left')
            ->join('prefixe_operateur po', 'po.id = nt.id_prefixe', 'left')
            ->join('operateur op', 'op.id = po.id_operateur', 'left')
            ->join('type_operation t', 't.id = o.id_type_operation', 'left');
        if ($debut) $builder->where('o.date >=', $debut);
        if ($fin)   $builder->where('o.date <=', $fin . ' 23:59:59');
        return $builder->groupBy('op.nom, op.est_notre_operateur, t.nom')->get()->getResultArray();
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
            ->select('o.*, t.nom as type_nom, nd.numero as numero_dest, ne.numero as numero_exp')
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->join('numero_telephone nd', 'nd.id = o.id_numero_tel_dest', 'left')
            ->join('numero_telephone ne', 'ne.id = o.id_numero_tel', 'left')
            ->groupStart()
                ->where('o.id_numero_tel', $idNumero)
                ->orWhere('o.id_numero_tel_dest', $idNumero)
            ->groupEnd()
            ->orderBy('o.date', 'DESC')
            ->get()->getResultArray();
    }

    public function historiqueFiltre(int $idNumero, ?string $type = null, ?float $montantMin = null, ?float $montantMax = null, ?string $dateDebut = null, ?string $dateFin = null): array
    {
        $builder = $this->db->table('operation o')
            ->select('o.*, t.nom as type_nom, nd.numero as numero_dest, ne.numero as numero_exp')
            ->join('type_operation t', 't.id = o.id_type_operation')
            ->join('numero_telephone nd', 'nd.id = o.id_numero_tel_dest', 'left')
            ->join('numero_telephone ne', 'ne.id = o.id_numero_tel', 'left')
            ->groupStart()
                ->where('o.id_numero_tel', $idNumero)
                ->orWhere('o.id_numero_tel_dest', $idNumero)
            ->groupEnd();

        if ($type) {
            $builder->where('t.nom', $type);
        }
        if ($montantMin !== null) {
            $builder->where('o.montant >=', $montantMin);
        }
        if ($montantMax !== null) {
            $builder->where('o.montant <=', $montantMax);
        }
        if ($dateDebut) {
            $builder->where('o.date >=', $dateDebut . ' 00:00:00');
        }
        if ($dateFin) {
            $builder->where('o.date <=', $dateFin . ' 23:59:59');
        }

        return $builder->orderBy('o.date', 'DESC')->get()->getResultArray();
    }
}
