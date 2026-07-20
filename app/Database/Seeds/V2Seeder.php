<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class V2Seeder extends Seeder
{
    public function run()
    {
        // Nettoyage avant insertion
        $this->db->disableForeignKeyChecks();
        $this->db->table('operateur')->emptyTable();
        $this->db->query("DELETE FROM sqlite_sequence WHERE name = 'operateur'");
        // Optionnel : $this->db->table('solde')->emptyTable(); // Attention : cela supprime les soldes V1
        $this->db->enableForeignKeyChecks();

        // 1. Ajouter les opérateurs
        $this->db->table('operateur')->insertBatch([
            ['nom' => 'Telma', 'est_notre_operateur' => 1, 'commission_exterieur' => 0.0],
            ['nom' => 'Airtel', 'est_notre_operateur' => 0, 'commission_exterieur' => 0.02], // 2%
            ['nom' => 'Orange', 'est_notre_operateur' => 0, 'commission_exterieur' => 0.02], // 2%
        ]);

        // 2. Créer/lier les prefixe_operateur avec id_operateur
        // insertBatch ci-dessus assigne les IDs dans l'ordre : 1 = Telma, 2 = Airtel, 3 = Orange
        // '033' -> Airtel (2), '037' -> Orange (3), '034' -> Telma (1)
        $prefixes = [
            '033' => 2,
            '037' => 3,
            '034' => 1,
        ];
        foreach ($prefixes as $prefixe => $idOperateur) {
            if (!$this->db->table('prefixe_operateur')->where('prefixe', $prefixe)->countAllResults()) {
                $this->db->table('prefixe_operateur')->insert(['prefixe' => $prefixe]);
            }
            $this->db->table('prefixe_operateur')
                ->where('prefixe', $prefixe)
                ->update(['id_operateur' => $idOperateur]);
        }

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
