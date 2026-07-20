<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/rapport/gains" class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="debut" class="form-label">Date début</label>
                <input type="date" class="form-control" id="debut" name="debut" value="<?= esc($debut ?? '') ?>">
            </div>
            <div class="col-auto">
                <label for="fin" class="form-label">Date fin</label>
                <input type="date" class="form-control" id="fin" name="fin" value="<?= esc($fin ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="/admin/rapport/gains" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Type d'opération</th>
            <th>Nombre d'opérations</th>
            <th>Total frais</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($gains)): ?>
            <tr><td colspan="3" class="text-center">Aucune donnée.</td></tr>
        <?php else: ?>
            <?php foreach ($gains as $g): ?>
                <tr>
                    <td><?= esc(ucfirst($g['nom'])) ?></td>
                    <td><?= $g['nb_operations'] ?></td>
                    <td><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <?php if (!empty($gains)): ?>
    <tfoot>
        <tr class="table-dark">
            <th colspan="2">Total général</th>
            <th><?= number_format($totalGains, 0, ',', ' ') ?> Ar</th>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

<?= $this->endSection() ?>
