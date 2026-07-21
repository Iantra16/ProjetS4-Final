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

    public function getPromotion()
    {
        return $this->where('promotion')->first();
    }


}
