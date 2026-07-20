<?php

namespace App\Models;

use CodeIgniter\Model;

class TranchesFraisModel extends Model
{
    protected $table = 'tranches_frais';
    protected $allowedFields = ['montant_min', 'montant_max', 'montant_frais', 'date'];
}
