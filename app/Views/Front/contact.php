<?= $this->extend('layout/front') ?>
<?= $this->section('content') ?>

<h2 class="mb-4">Contactez-nous</h2>

<?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<form method="post" action="/contact" style="max-width: 500px;">
  <?= csrf_field() ?>
  <div class="mb-3">
    <label>Nom</label>
    <input type="text" name="nom" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" class="form-control" required>
  </div>
  <div class="mb-3">
    <label>Message</label>
    <textarea name="message" class="form-control" rows="4" required></textarea>
  </div>
  <button class="btn btn-primary">Envoyer</button>
</form>

<?= $this->endSection() ?>