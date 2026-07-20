<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="/admin/types-operation" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" class="form-control form-control-sm" name="q" placeholder="Rechercher par nom..." value="<?= esc($recherche ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-search"></i> Filtrer</button>
                <a href="/admin/types-operation" class="btn btn-secondary btn-sm">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div></div>
    <a href="/admin/types-operation/nouveau" class="btn btn-primary"><i class="bi bi-plus"></i> Ajouter</a>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($types)): ?>
            <tr><td colspan="2" class="text-center">Aucun type trouvé.</td></tr>
        <?php else: ?>
            <?php foreach ($types as $t): ?>
                <tr>
                    <td><?= esc(ucfirst($t['nom'])) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="voirTranches(<?= $t['id'] ?>, '<?= esc($t['nom']) ?>')">Voir Tranches</button>
                        <a href="/admin/types-operation/modifier/<?= $t['id'] ?>" class="btn btn-sm btn-warning">Modifier</a>
                        <a href="/admin/types-operation/supprimer/<?= $t['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Supprimer ce type ?')"><i class="bi bi-trash"></i> Supprimer</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal Tranches -->
<div class="modal fade" id="modalTranches" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Tranches de frais — <span id="modalTypeNom"></span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <table class="table table-bordered table-striped table-sm">
          <thead class="table-dark">
            <tr><th>Min</th><th>Max</th><th>Frais</th><th>Actions</th></tr>
          </thead>
          <tbody id="modalTranchesBody"></tbody>
        </table>
        <div id="modalVide" class="text-center text-muted" style="display:none;">Aucune tranche pour ce type.</div>
      </div>
    </div>
  </div>
</div>

<script>
function voirTranches(idType, nom) {
    document.getElementById('modalTypeNom').textContent = nom;
    document.getElementById('modalTranchesBody').innerHTML = '';
    document.getElementById('modalVide').style.display = 'none';

    fetch('/admin/types-operation/tranches-json/' + idType)
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                document.getElementById('modalVide').style.display = 'block';
            } else {
                let html = '';
                data.forEach(t => {
                    html += '<tr>'
                        + '<td>' + formatM(t.montant_min) + '</td>'
                        + '<td>' + formatM(t.montant_max) + '</td>'
                        + '<td>' + formatM(t.montant_frais) + '</td>'
                        + '<td>'
                        + '<a href="/admin/tranches/modifier/' + t.id + '" class="btn btn-sm btn-warning">Modifier</a> '
                        + '<a href="/admin/tranches/supprimer/' + t.id + '" class="btn btn-sm btn-danger" onclick="return confirm(\'Supprimer ?\')">Supprimer</a>'
                        + '</td></tr>';
                });
                document.getElementById('modalTranchesBody').innerHTML = html;
            }
            new bootstrap.Modal(document.getElementById('modalTranches')).show();
        });
}

function formatM(v) {
    return parseFloat(v).toLocaleString('fr-FR');
}
</script>

<?= $this->endSection() ?>
