<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('error')): ?>
  <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<form method="post" action="/admin/entites/import" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <div class="mb-3">
    <label>Fichier CSV (colonnes : Nom;Catégorie;Prix;Stock, séparateur point-virgule)</label>
    <input type="file" name="fichier" class="form-control" accept=".csv" required>
  </div>

  <button class="btn btn-primary">Importer</button>
  <a href="/admin/entites" class="btn btn-secondary">Annuler</a>
</form>

<?= $this->endSection() ?>