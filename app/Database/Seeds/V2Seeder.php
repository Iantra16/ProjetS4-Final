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
            ['nom' => 'Telma', 'est_notre_operateur' => 1, 'commission_exterieur' => 0.0, 'promotion' => 0.02], // promotion 2%
            ['nom' => 'Airtel', 'est_notre_operateur' => 0, 'commission_exterieur' => 0.02 , 'promotion' => 0.02], // 2% , 2%
            ['nom' => 'Orange', 'est_notre_operateur' => 0, 'commission_exterieur' => 0.02 , 'promotion' => 0.00], // 2%
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

        // 3. Numéros de test supplémentaires pour tester l'envoi multiple (même opérateur)
        $prefixeId = [];
        foreach (['033', '037', '034'] as $p) {
            $row = $this->db->table('prefixe_operateur')->where('prefixe', $p)->get()->getRow();
            $prefixeId[$p] = $row ? $row->id : null;
        }
        $testNumeros = [
            '0331111111', '0332222222', '0333333333', // Airtel
            '0375555555', '0376666666', '0377777777', // Orange
            '0348888888', '0349999999',               // Telma
        ];
        $nouveauxIds = [];
        foreach ($testNumeros as $num) {
            if (!$this->db->table('numero_telephone')->where('numero', $num)->countAllResults()) {
                $this->db->table('numero_telephone')->insert([
                    'id_prefixe'    => $prefixeId[substr($num, 0, 3)],
                    'numero'        => $num,
                    'date_creation' => date('Y-m-d H:i:s'),
                ]);
                $nouveauxIds[] = $this->db->insertID();
            }
        }

        // 4. Ajouter des fonds à tous les comptes de test (V2 + nouveaux)
        // Comptes V2 de base : id 1 à 5 (créés par MobileMoneySeeder)
        $allIds = [1, 2, 3, 4, 5];
        $allIds = array_merge($allIds, $nouveauxIds);
        $soldes = [];
        foreach ($allIds as $id) {
            $soldes[] = [
                'id_numero_tel' => $id,
                'montant'       => 500000,
                'date'          => date('Y-m-d H:i:s'),
            ];
        }
        $this->db->table('solde')->insertBatch($soldes);
    }
}
