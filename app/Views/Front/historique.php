<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-10">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3>Mon historique</h3>
      <a href="/client/solde" class="btn btn-outline-secondary btn-sm">Retour</a>
    </div>

    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <form method="GET" action="/client/historique" class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label form-label-sm">Type</label>
            <select name="type" class="form-select form-select-sm">
              <option value="">Tous</option>
              <?php foreach ($types as $t): ?>
                <option value="<?= esc($t['nom']) ?>" <?= ($filters['type'] ?? '') === $t['nom'] ? 'selected' : '' ?>>
                  <?= esc(ucfirst($t['nom'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label form-label-sm">Montant min</label>
            <input type="number" name="montant_min" class="form-control form-control-sm" value="<?= esc($filters['montant_min'] ?? '') ?>" placeholder="0">
          </div>
          <div class="col-md-2">
            <label class="form-label form-label-sm">Montant max</label>
            <input type="number" name="montant_max" class="form-control form-control-sm" value="<?= esc($filters['montant_max'] ?? '') ?>" placeholder="∞">
          </div>
          <div class="col-md-2">
            <label class="form-label form-label-sm">Date début</label>
            <input type="date" name="date_debut" class="form-control form-control-sm" value="<?= esc($filters['date_debut'] ?? '') ?>">
          </div>
          <div class="col-md-2">
            <label class="form-label form-label-sm">Date fin</label>
            <input type="date" name="date_fin" class="form-control form-control-sm" value="<?= esc($filters['date_fin'] ?? '') ?>">
          </div>
          <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary btn-sm">Filtrer</button>
          </div>
        </form>
      </div>
    </div>

    <?php if (empty($operations)): ?>
      <div class="alert alert-info">Aucune opération trouvée.</div>
    <?php else: ?>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Date</th>
              <th>Type</th>
              <th>Montant</th>
              <th>Frais</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($operations as $op): ?>
              <tr>
                <td><?= esc($op['date']) ?></td>
                <td>
                  <?php
                    $badgeClass = match($op['type_nom']) {
                      'depot'    => 'bg-success',
                      'retrait'  => 'bg-warning text-dark',
                      'transfert'=> 'bg-info',
                      default    => 'bg-secondary',
                    };
                  ?>
                  <span class="badge <?= $badgeClass ?>"><?= ucfirst(esc($op['type_nom'])) ?></span>
                </td>
                <td><?= number_format($op['montant'], 0, ',', ' ') ?> Ar</td>
                <td><?= number_format($op['frais'], 0, ',', ' ') ?> Ar</td>
                <td>
                  <?php if ($op['type_nom'] === 'depot'): ?>
                    Dépôt sur votre compte
                  <?php elseif ($op['type_nom'] === 'retrait'): ?>
                    Retrait depuis votre compte
                  <?php elseif ($op['type_nom'] === 'transfert'): ?>
                    <?php if ($op['id_numero_tel'] == $monId): ?>
                      Transfert envoyé à <?= esc($op['numero_dest']) ?>
                    <?php else: ?>
                      Transfert reçu de <?= esc($op['numero_exp']) ?>
                    <?php endif; ?>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</div>

<?= $this->endSection() ?>
