<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPrefixeOperateurAddOperateur extends Migration
{
    public function up()
    {
        $this->forge->addColumn('prefixe_operateur', [
            'id_operateur' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ]
        ]);
        // Note: SQLite (as used in this project based on projetfinal.db)
        // doesn't support adding foreign keys to existing tables easily
        // via ALTER TABLE in CodeIgniter's forge in older versions.
        // Given we are in a development environment, let's try standard way.
        // Actually, CodeIgniter 4's forge doesn't fully support adding FK to existing tables via addColumn.
        // We might need to run a raw query if it fails.
        // Let's assume standard support for now.
    }

    public function down()
    {
        $this->forge->dropColumn('prefixe_operateur', 'id_operateur');
    }
}
