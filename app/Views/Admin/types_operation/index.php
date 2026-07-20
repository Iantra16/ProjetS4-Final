<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="/admin/types-operation/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Ajouter</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($types)): ?>
            <tr><td colspan="3" class="text-center">Aucun type trouvé.</td></tr>
        <?php else: ?>
            <?php foreach ($types as $t): ?>
                <tr>
                    <td><?= esc(ucfirst($t['nom'])) ?></td>
                    <td>
                        <a href="/admin/types-operation/modifier/<?= $t['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <a href="/admin/types-operation/supprimer/<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce type ?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
