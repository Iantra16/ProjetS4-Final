<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MobileMoneySeeder extends Seeder
{
    public function run()
    {
        // prefixe_operateur
        $this->db->table('prefixe_operateur')->insertBatch([
            ['prefixe' => '033', 'nom' => 'Airtel Money'],
            ['prefixe' => '037', 'nom' => 'Orange Money'],
            ['prefixe' => '034', 'nom' => 'Telma Mvola'],
        ]);

        // type_operation
        $this->db->table('type_operation')->insertBatch([
            ['nom' => 'depot'],
            ['nom' => 'retrait'],
            ['nom' => 'transfert'],
        ]);

        // tranches_frais
        $this->db->table('tranches_frais')->insertBatch([
            ['montant_min' => 100, 'montant_max' => 1000, 'montant_frais' => 50],
            ['montant_min' => 1001, 'montant_max' => 5000, 'montant_frais' => 50],
            ['montant_min' => 5001, 'montant_max' => 10000, 'montant_frais' => 100],
            ['montant_min' => 10001, 'montant_max' => 25000, 'montant_frais' => 200],
            ['montant_min' => 25001, 'montant_max' => 50000, 'montant_frais' => 400],
            ['montant_min' => 50001, 'montant_max' => 100000, 'montant_frais' => 800],
            ['montant_min' => 100001, 'montant_max' => 250000, 'montant_frais' => 1500],
            ['montant_min' => 250001, 'montant_max' => 500000, 'montant_frais' => 1500],
            ['montant_min' => 500001, 'montant_max' => 1000000, 'montant_frais' => 2500],
            ['montant_min' => 1000001, 'montant_max' => 2000000, 'montant_frais' => 3000],
        ]);

        // numero_telephone
        $this->db->table('numero_telephone')->insertBatch([
            ['id_prefixe' => 1, 'numero' => '0331234567', 'date_creation' => '2026-07-01 08:00:00'],
            ['id_prefixe' => 1, 'numero' => '0339876543', 'date_creation' => '2026-07-02 09:15:00'],
            ['id_prefixe' => 2, 'numero' => '0371112233', 'date_creation' => '2026-07-01 10:00:00'],
            ['id_prefixe' => 2, 'numero' => '0374445566', 'date_creation' => '2026-07-03 14:30:00'],
            ['id_prefixe' => 3, 'numero' => '0347778899', 'date_creation' => '2026-07-04 16:00:00'],
        ]);

        // solde
        $this->db->table('solde')->insertBatch([
            ['id_numero_tel' => 1, 'montant' => 0, 'date' => '2026-07-01 08:00:00'],
            ['id_numero_tel' => 1, 'montant' => 50000, 'date' => '2026-07-05 10:00:00'],
            ['id_numero_tel' => 2, 'montant' => 0, 'date' => '2026-07-02 09:15:00'],
            ['id_numero_tel' => 2, 'montant' => 120000, 'date' => '2026-07-06 11:00:00'],
            ['id_numero_tel' => 3, 'montant' => 0, 'date' => '2026-07-01 10:00:00'],
            ['id_numero_tel' => 3, 'montant' => 30000, 'date' => '2026-07-04 15:00:00'],
            ['id_numero_tel' => 4, 'montant' => 0, 'date' => '2026-07-03 14:30:00'],
            ['id_numero_tel' => 4, 'montant' => 15000, 'date' => '2026-07-07 09:00:00'],
            ['id_numero_tel' => 5, 'montant' => 0, 'date' => '2026-07-04 16:00:00'],
            ['id_numero_tel' => 5, 'montant' => 200000, 'date' => '2026-07-08 12:00:00'],
        ]);

        // operation
        $this->db->table('operation')->insertBatch([
            // Depots
            ['id_type_operation' => 1, 'id_numero_tel' => 1, 'id_numero_tel_dest' => null, 'montant' => 50000, 'frais' => 0, 'date' => '2026-07-05 10:00:00'],
            ['id_type_operation' => 1, 'id_numero_tel' => 2, 'id_numero_tel_dest' => null, 'montant' => 120000, 'frais' => 0, 'date' => '2026-07-06 11:00:00'],
            ['id_type_operation' => 1, 'id_numero_tel' => 3, 'id_numero_tel_dest' => null, 'montant' => 30000, 'frais' => 0, 'date' => '2026-07-04 15:00:00'],
            ['id_type_operation' => 1, 'id_numero_tel' => 4, 'id_numero_tel_dest' => null, 'montant' => 15000, 'frais' => 0, 'date' => '2026-07-07 09:00:00'],
            ['id_type_operation' => 1, 'id_numero_tel' => 5, 'id_numero_tel_dest' => null, 'montant' => 200000, 'frais' => 0, 'date' => '2026-07-08 12:00:00'],
            // Retrait
            ['id_type_operation' => 2, 'id_numero_tel' => 1, 'id_numero_tel_dest' => null, 'montant' => 5000, 'frais' => 50, 'date' => '2026-07-09 08:30:00'],
            // Transferts
            ['id_type_operation' => 3, 'id_numero_tel' => 2, 'id_numero_tel_dest' => 4, 'montant' => 10000, 'frais' => 100, 'date' => '2026-07-09 09:00:00'],
            ['id_type_operation' => 3, 'id_numero_tel' => 5, 'id_numero_tel_dest' => 3, 'montant' => 25000, 'frais' => 200, 'date' => '2026-07-09 09:30:00'],
        ]);
    }
}
