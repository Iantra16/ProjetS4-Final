<?= $this->extend('layout') ?>
<?= $this->section('content') ?>
<h1>Gestion des opérateurs</h1>
<a href="/admin/operateurs/nouveau" class="btn btn-primary">Nouvel opérateur</a>
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Est notre opérateur</th>
            <th>Commission extérieur</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($operateurs as $op): ?>
        <tr>
            <td><?= $op['id'] ?></td>
            <td><?= $op['nom'] ?></td>
            <td><?= $op['est_notre_operateur'] ? 'Oui' : 'Non' ?></td>
            <td><?= $op['commission_exterieur'] * 100 ?>%</td>
            <td>
                <a href="/admin/operateurs/modifier/<?= $op['id'] ?>">Modifier</a>
                <a href="/admin/operateurs/supprimer/<?= $op['id'] ?>" onclick="return confirm('Supprimer ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
