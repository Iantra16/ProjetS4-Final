<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTranchesFrais extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INTEGER', 'auto_increment' => true],
            'montant_min'   => ['type' => 'REAL', 'null' => false],
            'montant_max'   => ['type' => 'REAL', 'null' => false],
            'montant_frais' => ['type' => 'REAL', 'null' => false],
            'date'          => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('tranches_frais');
    }

    public function down()
    {
        $this->forge->dropTable('tranches_frais');
    }
}
