<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card" style="max-width:500px;">
    <div class="card-body">
        <form method="POST" action="<?= isset($tranche) ? '/admin/tranches/mettreAJour/' . $tranche['id'] : '/admin/tranches/creer' ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="id_type_operation" class="form-label">Type d'opération</label>
                <select class="form-select" id="id_type_operation" name="id_type_operation" required>
                    <option value="">-- Choisir --</option>
                    <?php foreach ($types as $type): ?>
                        <option value="<?= $type['id'] ?>" <?= (old('id_type_operation', $tranche['id_type_operation'] ?? '') == $type['id']) ? 'selected' : '' ?>>
                            <?= esc(ucfirst($type['nom'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="montant_min" class="form-label">Montant min</label>
                <input type="number" class="form-control" id="montant_min" name="montant_min" step="0.01"
                       value="<?= esc(old('montant_min', $tranche['montant_min'] ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label for="montant_max" class="form-label">Montant max</label>
                <input type="number" class="form-control" id="montant_max" name="montant_max" step="0.01"
                       value="<?= esc(old('montant_max', $tranche['montant_max'] ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label for="montant_frais" class="form-label">Montant frais</label>
                <input type="number" class="form-control" id="montant_frais" name="montant_frais" step="0.01"
                       value="<?= esc(old('montant_frais', $tranche['montant_frais'] ?? '')) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary"><?= isset($tranche) ? 'Modifier' : 'Ajouter' ?></button>
            <a href="/admin/tranches" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
