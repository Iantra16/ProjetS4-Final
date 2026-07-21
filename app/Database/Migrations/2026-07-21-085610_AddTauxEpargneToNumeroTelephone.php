<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTauxEpargneToNumeroTelephone extends Migration
{
    public function up()
    {
        $this->forge->addColumn('numero_telephone', [
            'taux_epargne' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
                'after'      => 'eparnge'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('numero_telephone', 'taux_epargne');
    }
}
