<?php

namespace App\Models;

use CodeIgniter\Model;

class NumeroTelephoneModel extends Model
{
    protected $table = 'numero_telephone';
    protected $allowedFields = ['id_prefixe', 'numero', 'date_creation'];
}
