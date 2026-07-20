<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">Montants à envoyer aux opérateurs externes</h5>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Opérateur</th>
                    <th>Total à envoyer (Montant + Commission)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($montants)): ?>
                    <tr><td colspan="2" class="text-center">Aucune donnée.</td></tr>
                <?php else: ?>
                    <?php foreach ($montants as $m): ?>
                        <tr>
                            <td><?= esc($m['nom']) ?></td>
                            <td><?= number_format($m['total_a_envoyer'], 0, ',', ' ') ?> Ar</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
