<?php
namespace App\Models;
use CodeIgniter\Model;

class ProduitModel extends Model
{
    protected $table = 'produits';
    protected $allowedFields = ['nom', 'categorie', 'prix', 'stock'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'nom'  => 'required|min_length[2]',
        'prix' => 'required|numeric',
    ];

    public function rechercheEtFiltre(?string $keyword = null, ?string $categorie = null): self
    {
        $builder = $this->builder();

        if (!empty($keyword)) {
            $builder->groupStart()
                ->like('nom', $keyword)
                ->orLike('categorie', $keyword)
                ->groupEnd();
        }

        if (!empty($categorie)) {
            $builder->where('categorie', $categorie);
        }

        return $this; 
    }
}