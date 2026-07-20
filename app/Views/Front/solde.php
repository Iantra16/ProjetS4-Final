<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body text-center p-5">
        <i class="bi bi-wallet2 display-1 text-primary"></i>
        <h2 class="mt-3">Mon solde</h2>
        <p class="text-muted"><?= esc($numero) ?></p>

        <form method="GET" action="/client/solde" class="row g-2 justify-content-center mb-3">
          <div class="col-auto">
            <input type="date" class="form-control form-control-sm" name="date" value="<?= esc($date) ?>">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-search"></i> Chercher</button>
          </div>
          <?php if ($date !== date('Y-m-d')): ?>
            <div class="col-auto">
              <a href="/client/solde" class="btn btn-outline-secondary btn-sm">Aujourd'hui</a>
            </div>
          <?php endif; ?>
        </form>

        <div class="display-4 fw-bold text-success my-4">
          <?= number_format($solde['montant'] ?? 0, 2, ',', ' ') ?> F
        </div>

        <p class="text-muted small">
          <?php if ($date !== date('Y-m-d')): ?>
            Solde au <?= esc($date) ?>
          <?php else: ?>
            Dernière mise à jour : <?= esc($solde['date'] ?? '—') ?>
          <?php endif; ?>
        </p>

        <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
          <a href="/client/operation" class="btn btn-primary">
            <i class="bi bi-arrow-left-right"></i> Effectuer une opération
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
