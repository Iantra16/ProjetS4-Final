<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card" style="max-width:500px;">
    <div class="card-body">
        <h4 class="card-title"><?= isset($operateur) ? 'Modifier' : 'Nouvel' ?> opérateur</h4>
        <form action="/admin/operateurs/<?= isset($operateur) ? 'mettreAJour/'.$operateur['id'] : 'creer' ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" name="nom" id="nom" class="form-control" value="<?= esc($operateur['nom'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="commission_exterieur" class="form-label">Commission extérieur (en %)</label>
                <input type="number" step="0.01" name="commission_exterieur" id="commission_exterieur" class="form-control" value="<?= isset($operateur) ? esc($operateur['commission_exterieur'] * 100) : '' ?>" required>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="est_notre_operateur" name="est_notre_operateur" value="1" <?= isset($operateur) && $operateur['est_notre_operateur'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="est_notre_operateur">Notre opérateur (interne)</label>
            </div>

            <button type="submit" class="btn btn-primary"><?= isset($operateur) ? 'Mettre à jour' : 'Enregistrer' ?></button>
            <a href="/admin/operateurs" class="btn btn-secondary">Annuler</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
