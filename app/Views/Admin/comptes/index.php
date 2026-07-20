<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="/admin/comptes" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" name="q" placeholder="Rechercher par numéro ou opérateur..." value="<?= esc($recherche ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filtrer</button>
                <a href="/admin/comptes" class="btn btn-secondary btn-sm">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Numéro</th>
            <th>Opérateur</th>
            <th>Solde actuel</th>
            <th>Dernière mise à jour</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($comptes)): ?>
            <tr><td colspan="5" class="text-center">Aucun compte trouvé.</td></tr>
        <?php else: ?>
            <?php foreach ($comptes as $c): ?>
                <tr>
                    <td><?= esc($c['numero']) ?></td>
                    <td><?= esc($c['operateur']) ?></td>
                    <td><?= number_format($c['solde_actuel'] ?? 0, 0, ',', ' ') ?> Ar</td>
                    <td><?= esc($c['date_solde'] ?? '-') ?></td>
                    <td>
                        <a href="/admin/comptes/<?= $c['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Détail</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
