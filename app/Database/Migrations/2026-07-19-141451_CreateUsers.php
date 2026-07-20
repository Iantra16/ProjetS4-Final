<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'       => ['type' => 'INTEGER', 'auto_increment' => true],
            'nom'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'email'    => ['type' => 'VARCHAR', 'constraint' => 150, 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'role'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'user'],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
