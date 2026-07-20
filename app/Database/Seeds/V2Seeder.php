<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class V2Seeder extends Seeder
{
    public function run()
    {
        // 1. Ajouter les opérateurs
        $this->db->table('operateur')->insertBatch([
            ['nom' => 'Telma', 'est_notre_operateur' => 1, 'commission_exterieur' => 0.0],
            ['nom' => 'Airtel', 'est_notre_operateur' => 0, 'commission_exterieur' => 0.02], // 2%
            ['nom' => 'Orange', 'est_notre_operateur' => 0, 'commission_exterieur' => 0.02], // 2%
        ]);

        // 2. Mettre à jour les prefixe_operateur avec id_operateur
        // Selon MobileMoneySeeder:
        // '033' (Airtel) -> id_operateur (Airtel) est 2
        // '037' (Orange) -> id_operateur (Orange) est 3
        // '034' (Telma)  -> id_operateur (Telma) est 1
        // Let's verify IDs: 1: Telma, 2: Airtel, 3: Orange?
        // Wait, insertBatch in SQLite will assign IDs based on insert order: 1, 2, 3.
        // So:
        // ID 1: Telma
        // ID 2: Airtel
        // ID 3: Orange

        // Re-mapping based on ID 1=Telma, 2=Airtel, 3=Orange:
        // 3. Ajouter des fonds aux comptes de test pour V2
        // id 1: 0331234567, id 2: 0339876543, id 3: 0371112233, id 4: 0374445566, id 5: 0347778899
        $this->db->table('solde')->insertBatch([
            ['id_numero_tel' => 1, 'montant' => 500000, 'date' => date('Y-m-d H:i:s')],
            ['id_numero_tel' => 2, 'montant' => 500000, 'date' => date('Y-m-d H:i:s')],
            ['id_numero_tel' => 3, 'montant' => 500000, 'date' => date('Y-m-d H:i:s')],
            ['id_numero_tel' => 4, 'montant' => 500000, 'date' => date('Y-m-d H:i:s')],
            ['id_numero_tel' => 5, 'montant' => 1000000, 'date' => date('Y-m-d H:i:s')],
        ]);
    }
}
