<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="/admin/prefixes" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" name="q" placeholder="Rechercher par préfixe ou nom..." value="<?= esc($recherche ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filtrer</button>
                <a href="/admin/prefixes" class="btn btn-secondary btn-sm">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="/admin/prefixes/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Ajouter</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Préfixe</th>
            <th>Nom</th>
            <th>Opérateur</th>
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
                    <td><?= esc($p['operateur_nom'] ?? 'Non défini') ?></td>
                    <td>
                        <a href="/admin/prefixes/modifier/<?= $p['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="/admin/prefixes/supprimer/<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce préfixe ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
