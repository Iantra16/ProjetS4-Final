<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card" style="max-width:500px;">
    <div class="card-body">
        <form method="POST" action="<?= isset($prefixe) ? '/admin/prefixes/mettreAJour/' . $prefixe['id'] : '/admin/prefixes/creer' ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="prefixe" class="form-label">Préfixe</label>
                <input type="text" class="form-control" id="prefixe" name="prefixe" maxlength="3"
                       value="<?= esc(old('prefixe', $prefixe['prefixe'] ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label for="id_operateur" class="form-label">Opérateur</label>
                <select class="form-control" id="id_operateur" name="id_operateur" required>
                    <?php foreach ($operateurs as $op): ?>
                        <option value="<?= $op['id'] ?>" <?= (isset($prefixe) && $prefixe['id_operateur'] == $op['id']) ? 'selected' : '' ?>>
                            <?= $op['nom'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary"><?= isset($prefixe) ? 'Modifier' : 'Ajouter' ?></button>
            <a href="/admin/prefixes" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
