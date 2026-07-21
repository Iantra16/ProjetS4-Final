<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPromoFraisToOperateur extends Migration
{
    public function up()
    {
        $this->forge->addColumn('operateur', [
            'promo_frais_percent' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0.00,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('operateur', 'promo_frais_percent');
    }
}
