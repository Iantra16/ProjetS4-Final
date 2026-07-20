<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperation extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INTEGER', 'auto_increment' => true],
            'id_type_operation'  => ['type' => 'INTEGER', 'null' => false],
            'id_numero_tel'      => ['type' => 'INTEGER', 'null' => false],
            'id_numero_tel_dest' => ['type' => 'INTEGER', 'null' => true],
            'montant'            => ['type' => 'REAL', 'null' => false],
            'frais'              => ['type' => 'REAL', 'null' => false, 'default' => 0.0],
            'date'               => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_type_operation', 'type_operation', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_numero_tel', 'numero_telephone', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('id_numero_tel_dest', 'numero_telephone', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('operation');
    }

    public function down()
    {
        $this->forge->dropTable('operation');
    }
}
