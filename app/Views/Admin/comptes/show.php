<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-body">
        <h5><?= esc($compte['numero']) ?></h5>
        <p class="mb-1"><strong>Solde actuel :</strong> <?= number_format($solde['montant'] ?? 0, 0, ',', ' ') ?> Ar</p>
        <p class="mb-0"><strong>Dernière mise à jour :</strong> <?= esc($solde['date'] ?? '-') ?></p>
    </div>
</div>

<h5>Historique des opérations</h5>
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
        <?php if (empty($operations)): ?>
            <tr><td colspan="5" class="text-center">Aucune opération.</td></tr>
        <?php else: ?>
            <?php foreach ($operations as $op): ?>
                <tr>
                    <td><?= esc($op['date']) ?></td>
                    <td><?= esc(ucfirst($op['type_nom'])) ?></td>
                    <td><?= number_format($op['montant'], 0, ',', ' ') ?> Ar</td>
                    <td><?= number_format($op['frais'], 0, ',', ' ') ?> Ar</td>
                    <td>
                        <?php if ($op['type_nom'] === 'transfert' && $op['id_numero_tel'] === $compte['id']): ?>
                            Transfert envoyé à <?= esc($op['numero_dest'] ?? '-') ?>
                        <?php elseif ($op['type_nom'] === 'transfert' && $op['id_numero_tel_dest'] == $compte['id']): ?>
                            Transfert reçu
                        <?php elseif ($op['type_nom'] === 'depot'): ?>
                            Dépôt
                        <?php else: ?>
                            Retrait
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<a href="/admin/comptes" class="btn btn-secondary">Retour</a>

<?= $this->endSection() ?>
