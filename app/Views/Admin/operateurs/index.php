<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Gestion des opérateurs</h3>
    <a href="/admin/operateurs/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Nouvel opérateur</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Nom</th>
            <th>Commission extérieur</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($operateurs as $op): ?>
        <tr>
            <td><?= esc($op['nom']) ?></td>
            <td><?= esc($op['commission_exterieur'] * 100) ?>%</td>
            <td>
                <a href="/admin/operateurs/modifier/<?= $op['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                <a href="/admin/operateurs/supprimer/<?= $op['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cet opérateur ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
