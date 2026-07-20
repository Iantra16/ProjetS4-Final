<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePrefixeOperateur extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'      => ['type' => 'INTEGER', 'auto_increment' => true],
            'prefixe' => ['type' => 'TEXT', 'null' => false, 'unique' => true],
            'nom'     => ['type' => 'TEXT', 'null' => false],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('prefixe_operateur');
    }

    public function down()
    {
        $this->forge->dropTable('prefixe_operateur');
    }
}
