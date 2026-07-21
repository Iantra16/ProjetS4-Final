<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEparnge extends Migration
{
    public function up()
    {
        $this->forge->addColumn('numero_telephone', [
            'eparnge' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
        ]);
        $this->forge->addField([
            'id'            => ['type' => 'INTEGER', 'auto_increment' => true],
            'id_numero_tel' => ['type' => 'INTEGER', 'null' => false],
            'montant'       => ['type' => 'REAL', 'null' => false, 'default' => 0.0],
            'date'          => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_numero_tel', 'numero_telephone', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('solde_eparnge');
    }

    public function down()
    {
        //
    }
}
