<?php

namespace App\Models;

use CodeIgniter\Model;

class SoldeModel extends Model
{
    protected $table = 'solde';
    protected $allowedFields = ['id_numero_tel', 'montant', 'date'];
}
