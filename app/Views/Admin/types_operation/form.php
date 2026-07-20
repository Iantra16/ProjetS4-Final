<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card" style="max-width:500px;">
    <div class="card-body">
        <form method="POST" action="<?= isset($type) ? '/admin/types-operation/mettreAJour/' . $type['id'] : '/admin/types-operation/creer' ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom"
                       value="<?= esc(old('nom', $type['nom'] ?? '')) ?>" required>
            </div>

            <button type="submit" class="btn btn-primary"><?= isset($type) ? 'Modifier' : 'Ajouter' ?></button>
            <a href="/admin/types-operation" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
