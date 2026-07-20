<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body text-center p-5">
        <i class="bi bi-wallet2 display-1 text-primary"></i>
        <h2 class="mt-3">Mon solde</h2>
        <p class="text-muted"><?= esc($numero) ?></p>

        <div class="display-4 fw-bold text-success my-4">
          <?= number_format($solde['montant'] ?? 0, 2, ',', ' ') ?> F
        </div>

        <p class="text-muted small">
          Dernière mise à jour : <?= esc($solde['date'] ?? '—') ?>
        </p>

        <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
          <a href="/client/depot" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Dépôt
          </a>
          <a href="/client/retrait" class="btn btn-warning">
            <i class="bi bi-dash-circle"></i> Retrait
          </a>
          <a href="/client/transfert" class="btn btn-info text-white">
            <i class="bi bi-send"></i> Transfert
          </a>
        </div>

        <hr>

        <a href="/client/historique" class="btn btn-outline-secondary btn-sm">
          <i class="bi bi-clock-history"></i> Voir mon historique
        </a>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
