<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3"><i class="bi bi-plus-circle text-success"></i> Dépôt</h3>

        <p class="text-muted">Solde actuel : <strong><?= number_format($solde['montant'] ?? 0, 0, ',', ' ') ?> F</strong></p>

        <form method="POST" action="/client/depot">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label for="montant" class="form-label">Montant (FCFA)</label>
            <input type="number" class="form-control" id="montant" name="montant"
                   min="1" step="any" value="<?= old('montant') ?>" required autofocus>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-lg"></i> Confirmer le dépôt
            </button>
            <a href="/client/solde" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
