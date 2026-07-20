<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterOperationAddCommission extends Migration
{
    public function up()
    {
        $this->forge->addColumn('operation', [
            'commission' => [
                'type' => 'REAL',
                'default' => 0.0,
                'null' => false,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('operation', 'commission');
    }
}
