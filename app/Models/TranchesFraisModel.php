<?php

namespace App\Models;

use CodeIgniter\Model;

class TranchesFraisModel extends Model
{
    protected $table = 'tranches_frais';
    protected $allowedFields = ['id_type_operation', 'montant_min', 'montant_max', 'montant_frais', 'date'];
}
