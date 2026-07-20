<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h3><i class="bi bi-clock-history"></i> Mon historique</h3>
      <a href="/client/solde" class="btn btn-outline-secondary btn-sm">Retour</a>
    </div>

    <?php if (empty($operations)): ?>
      <div class="alert alert-info">Aucune opération pour l'instant.</div>
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
                <td><?= number_format($op['montant'], 0, ',', ' ') ?> F</td>
                <td><?= number_format($op['frais'], 0, ',', ' ') ?> F</td>
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
