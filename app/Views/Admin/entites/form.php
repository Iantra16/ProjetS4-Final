<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<form method="post" action="<?= isset($produit) ? '/admin/entites/update/' . $produit['id'] : '/admin/entites/create' ?>">
  <?= csrf_field() ?>

  <div class="mb-3">
    <label>Nom</label>
    <input type="text" name="nom" class="form-control" value="<?= esc($produit['nom'] ?? '') ?>" required>
  </div>

  <div class="mb-3">
    <label>Catégorie</label>
    <input type="text" name="categorie" class="form-control" value="<?= esc($produit['categorie'] ?? '') ?>">
  </div>

  <div class="mb-3">
    <label>Prix</label>
    <input type="number" step="0.01" name="prix" class="form-control" value="<?= esc($produit['prix'] ?? '') ?>" required>
  </div>

  <div class="mb-3">
    <label>Stock</label>
    <input type="number" name="stock" class="form-control" value="<?= esc($produit['stock'] ?? '') ?>">
  </div>

  <button class="btn btn-primary">Enregistrer</button>
  <a href="/admin/entites" class="btn btn-secondary">Annuler</a>
</form>

<?= $this->endSection() ?>