<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>


<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Opérateur</th>
            <th>Montant total à envoyer (Montant + Commission)</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($montants)): ?>
            <tr><td colspan="2" class="text-center">Aucune donnée à envoyer.</td></tr>
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

<?= $this->endSection() ?>