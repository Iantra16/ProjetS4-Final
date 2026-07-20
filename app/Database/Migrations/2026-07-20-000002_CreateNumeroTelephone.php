<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNumeroTelephone extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'INTEGER', 'auto_increment' => true],
            'id_prefixe'    => ['type' => 'INTEGER', 'null' => false],
            'numero'        => ['type' => 'CHAR', 'constraint' => 10, 'null' => false, 'unique' => true],
            'date_creation' => ['type' => 'DATETIME', 'null' => false, 'default' => 'CURRENT_TIMESTAMP'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('id_prefixe', 'prefixe_operateur', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('numero_telephone');
    }

    public function down()
    {
        $this->forge->dropTable('numero_telephone');
    }
}
