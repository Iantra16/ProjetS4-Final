<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="/admin/tranches/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Ajouter</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th></th>
            <th>Type</th>
            <th>Montant Min</th>
            <th>Montant Max</th>
            <th>Frais</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tranches)): ?>
            <tr><td colspan="6" class="text-center">Aucune tranche trouvée.</td></tr>
        <?php else: ?>
            <?php foreach ($tranches as $t): ?>
                <tr>
                    <td><?= esc($t['id']) ?></td>
                    <td><?= esc(ucfirst($t['type_nom'])) ?></td>
                    <td><?= number_format($t['montant_min'], 0, ',', ' ') ?></td>
                    <td><?= number_format($t['montant_max'], 0, ',', ' ') ?></td>
                    <td><?= number_format($t['montant_frais'], 0, ',', ' ') ?></td>
                    <td>
                        <a href="/admin/tranches/modifier/<?= $t['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <a href="/admin/tranches/supprimer/<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette tranche ?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
