<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOperateur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 5, 'unsigned' => true, 'auto_increment' => true],
            'nom' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'est_notre_operateur' => ['type' => 'BOOLEAN', 'default' => false],
            'commission_exterieur' => ['type' => 'REAL', 'default' => 0.0],
            'promotion' => ['type' => 'REAL', 'default' => 0.0],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('operateur');
    }

    public function down()
    {
        $this->forge->dropTable('operateur');
    }
}
