<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProduits extends Migration
{
     public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'INTEGER', 'auto_increment' => true],
            'nom'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'categorie'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'prix'       => ['type' => 'DECIMAL', 'constraint' => '10,2'],
            'stock'      => ['type' => 'INTEGER', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('produits');
    }

    public function down()
    {
        $this->forge->dropTable('produits');
    }
}
