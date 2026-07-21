<?php

namespace App\Models;

use CodeIgniter\Model;

class OperateurModel extends Model
{
    protected $table = 'operateur';
    protected $primaryKey = 'id';
    protected $allowedFields = ['nom', 'est_notre_operateur', 'commission_exterieur', 'promo_frais_percent'];

    public function notreOperateur()
    {
        return $this->where('est_notre_operateur', 1)->first();
    }

    public static function getPromoPercent($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('operateur');
        $operateur = $builder->where('id', $id)->get()->getRowArray();
        return $operateur ? (float) $operateur['promo_frais_percent'] : 0.0;
    }


}
