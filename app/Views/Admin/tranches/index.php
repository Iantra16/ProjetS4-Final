<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="/admin/tranches" class="row g-2 align-items-end">
            <div class="col-md-4">
                <select name="type" class="form-select form-select-sm">
                    <option value="">Tous les types</option>
                    <?php foreach ($types as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($typeFiltre ?? '') == $t['id'] ? 'selected' : '' ?>>
                            <?= esc(ucfirst($t['nom'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filtrer</button>
                <a href="/admin/tranches" class="btn btn-secondary btn-sm">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="/admin/tranches/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Ajouter</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Type</th>
            <th>Montant Min</th>
            <th>Montant Max</th>
            <th>Frais</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tranches)): ?>
            <tr><td colspan="5" class="text-center">Aucune tranche trouvée.</td></tr>
        <?php else: ?>
            <?php foreach ($tranches as $t): ?>
                <tr>
                    <td><?= esc(ucfirst($t['type_nom'])) ?></td>
                    <td><?= number_format($t['montant_min'], 0, ',', ' ') ?></td>
                    <td><?= number_format($t['montant_max'], 0, ',', ' ') ?></td>
                    <td><?= number_format($t['montant_frais'], 0, ',', ' ') ?></td>
                    <td>
                        <a href="/admin/tranches/modifier/<?= $t['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="/admin/tranches/supprimer/<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer cette tranche ?')">Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
