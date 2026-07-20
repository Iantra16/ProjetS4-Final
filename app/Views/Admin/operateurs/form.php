<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h1><?= isset($operateur) ? 'Modifier' : 'Nouvel' ?> opérateur</h1>
<form action="/admin/operateurs/<?= isset($operateur) ? 'mettreAJour/'.$operateur['id'] : 'creer' ?>" method="post">
    <div class="form-group">
        <label>Nom</label>
        <input type="text" name="nom" class="form-control" value="<?= $operateur['nom'] ?? '' ?>" required>
    </div>
    <div class="form-group">
        <label>
            <input type="checkbox" name="est_notre_operateur" value="1" <?= (isset($operateur) && $operateur['est_notre_operateur']) ? 'checked' : '' ?>>
            Est notre opérateur
        </label>
    </div>
    <div class="form-group">
        <label>Commission extérieur (en %)</label>
        <input type="number" step="0.01" name="commission_exterieur" class="form-control" value="<?= isset($operateur) ? $operateur['commission_exterieur'] * 100 : '' ?>">
    </div>
    <button type="submit" class="btn btn-primary">Enregistrer</button>
</form>
<?= $this->endSection() ?>
