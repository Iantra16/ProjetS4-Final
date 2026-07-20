<?php

namespace App\Models;

use CodeIgniter\Model;

class OperationModel extends Model
{
    protected $table = 'operation';
    protected $allowedFields = ['id_type_operation', 'id_numero_tel', 'id_numero_tel_dest', 'montant', 'frais', 'date'];
}
