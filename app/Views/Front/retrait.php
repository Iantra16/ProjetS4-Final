<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3"><i class="bi bi-dash-circle text-warning"></i> Retrait</h3>

        <p class="text-muted">Solde actuel : <strong><?= number_format($montantSolde, 0, ',', ' ') ?> F</strong></p>

        <form method="POST" action="/client/retrait">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label for="montant" class="form-label">Montant (FCFA)</label>
            <input type="number" class="form-control" id="montant" name="montant"
                   min="1" step="any" value="<?= old('montant') ?>" required autofocus>
          </div>

          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-warning">
              <i class="bi bi-check-lg"></i> Confirmer le retrait
            </button>
            <a href="/client/solde" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>

        <hr>
        <h6>Barème des frais :</h6>
        <table class="table table-sm table-bordered mb-0">
          <thead class="table-light">
            <tr><th>De</th><th>À</th><th>Frais</th></tr>
          </thead>
          <tbody>
            <?php foreach ($frais as $t): ?>
            <tr>
              <td><?= number_format($t['montant_min'], 0, ',', ' ') ?></td>
              <td><?= number_format($t['montant_max'], 0, ',', ' ') ?></td>
              <td><?= number_format($t['montant_frais'], 0, ',', ' ') ?> F</td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
