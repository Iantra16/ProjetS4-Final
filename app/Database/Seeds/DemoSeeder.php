<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run()
    {
        $this->db->table('users')->insert([
            'nom' => 'Admin', 'email' => 'admin@test.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT), 'role' => 'admin',
        ]);
        $this->db->table('users')->insert([
            'nom' => 'User', 'email' => 'user@test.com',
            'password' => password_hash('password123', PASSWORD_DEFAULT), 'role' => 'user',
        ]);

        $categories = ['Informatique', 'Alimentaire', 'Vêtement'];
        for ($i = 1; $i <= 20; $i++) {
            $this->db->table('produits')->insert([
                'nom'        => 'Produit ' . $i,
                'categorie'  => $categories[array_rand($categories)],
                'prix'       => rand(1000, 50000),
                'stock'      => rand(0, 100),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}