<?= $this->extend('layout/front') ?>
<?= $this->section('content') ?>

<h2 class="mb-4">Nos produits</h2>

<div class="row">
<?php foreach ($produits as $p): ?>
  <div class="col-md-3 mb-3">
    <div class="card p-3 h-100">
      <h5><?= esc($p['nom']) ?></h5>
      <p class="text-muted mb-1"><?= esc($p['categorie']) ?></p>
      <p class="fw-bold"><?= esc($p['prix']) ?> Ar</p>
      <small class="text-muted">Stock : <?= esc($p['stock']) ?></small>
    </div>
  </div>
<?php endforeach; ?>

<?php if (empty($produits)): ?>
  <p class="text-muted">Aucun produit pour le moment.</p>
<?php endif; ?>
</div>

<?= $this->endSection() ?>