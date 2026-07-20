<?php
/**
 * @var array $produits
 * @var \CodeIgniter\Pager\Pager $pager
 * @var string|null $keyword
 * @var string|null $categorie
 */
?>
<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between mb-3">
  <form method="get" class="d-flex gap-2">
    <input type="text" name="q" class="form-control" placeholder="Rechercher un nom ou catégorie..." value="<?= esc($keyword) ?>">

    <select name="categorie" class="form-select">
      <option value="">Toutes catégories</option>
      <?php foreach (['Informatique', 'Alimentaire', 'Vêtement'] as $c): ?>
        <option value="<?= $c ?>" <?= $categorie === $c ? 'selected' : '' ?>><?= $c ?></option>
      <?php endforeach; ?>
    </select>

    <button class="btn btn-primary">Filtrer</button>
    <a href="/admin/entites" class="btn btn-outline-secondary">Réinitialiser</a>
  </form>

  <div class="d-flex gap-2">
    <a href="/admin/entites/import" class="btn btn-outline-secondary">Importer</a>
    <a href="/admin/entites/export/pdf" class="btn btn-danger">Export PDF</a>
    <a href="/admin/entites/export/excel" class="btn btn-outline-success">Export CSV</a>
    <a href="/admin/entites/new" class="btn btn-success">+ Ajouter un produit</a>
  </div>
</div>

<table class="table table-striped">
  <thead>
    <tr><th>Nom</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Actions</th></tr>
  </thead>
  <tbody>
  <?php foreach ($produits as $p): ?>
    <tr>
      <td><?= esc($p['nom']) ?></td>
      <td><?= esc($p['categorie']) ?></td>
      <td><?= esc($p['prix']) ?></td>
      <td><?= esc($p['stock']) ?></td>
      <td>
        <a href="/admin/entites/edit/<?= $p['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
        <a href="/admin/entites/delete/<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Sûr ?')">Suppr.</a>
      </td>
    </tr>
  <?php endforeach; ?>
  <?php if (empty($produits)): ?>
    <tr><td colspan="5" class="text-center text-muted">Aucun résultat</td></tr>
  <?php endif; ?>
  </tbody>
</table>

<?= $pager->only(['q', 'categorie'])->links('produits', 'default_full') ?>

<?= $this->endSection() ?>