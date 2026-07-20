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
        $this->db->query("UPDATE prefixe_operateur SET id_operateur = 1 WHERE prefixe = '034'");
        $this->db->query("UPDATE prefixe_operateur SET id_operateur = 2 WHERE prefixe = '033'");
        $this->db->query("UPDATE prefixe_operateur SET id_operateur = 3 WHERE prefixe = '037'");
    }
}
