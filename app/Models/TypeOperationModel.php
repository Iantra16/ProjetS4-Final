<?php

namespace App\Models;

use CodeIgniter\Model;

class TypeOperationModel extends Model
{
    protected $table = 'type_operation';
    protected $allowedFields = ['nom'];

    protected $validationRules = [
        'nom' => 'required|min_length[2]|is_unique[type_operation.nom,id,{id}]',
    ];
}
