<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="/admin/prefixes/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Ajouter</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Préfixe</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($prefixes)): ?>
            <tr><td colspan="4" class="text-center">Aucun préfixe trouvé.</td></tr>
        <?php else: ?>
            <?php foreach ($prefixes as $p): ?>
                <tr>
                    <td><?= esc($p['prefixe']) ?></td>
                    <td><?= esc($p['nom']) ?></td>
                    <td>
                        <a href="/admin/prefixes/modifier/<?= $p['id'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <a href="/admin/prefixes/supprimer/<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce préfixe ?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
