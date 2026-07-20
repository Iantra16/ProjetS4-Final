<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3"><i class="bi bi-arrow-left-right text-primary"></i> Effectuer une opération</h3>

        <p class="text-muted">Solde actuel : <strong><?= number_format($solde['montant'] ?? 0, 0, ',', ' ') ?> F</strong></p>

        <form method="POST" action="/client/operation" id="formOperation">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label for="type_operation" class="form-label">Type d'opération</label>
            <select class="form-select" id="type_operation" name="type_operation" required>
              <option value="">-- Choisir --</option>
              <option value="depot">Dépôt</option>
              <option value="retrait">Retrait</option>
              <option value="transfert">Transfert</option>
            </select>
          </div>

          <div class="mb-3" id="zoneDestinataire" style="display:none;">
            <label for="numero_dest" class="form-label">Numéro destinataire</label>
            <input type="text" class="form-control" id="numero_dest" name="numero_dest"
                   maxlength="10" pattern="[0-9]{10}" placeholder="Ex: 0331234567">
            <div class="form-text text-danger" id="erreurNumero" style="display:none;">Ce numéro n'existe pas.</div>
          </div>

          <div class="mb-3">
            <label for="montant" class="form-label">Montant (F)</label>
            <input type="number" class="form-control" id="montant" name="montant"
                   min="1" step="any" value="<?= old('montant') ?>" required>
          </div>

          <div class="mb-3" id="zoneFrais" style="display:none;">
            <div class="alert alert-info mb-0">
              <strong>Frais :</strong> <span id="affichageFrais">0</span> F<br>
              <strong>Total débité :</strong> <span id="affichageTotal">0</span> F
            </div>
          </div>

          <div id="zoneBareme" style="display:none;">
            <p class="fw-bold small">Barème des frais :</p>
            <table class="table table-sm table-bordered" id="tableBareme">
              <thead class="table-light">
                <tr><th>Min</th><th>Max</th><th>Frais</th></tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>

          <div class="d-flex gap-2 mt-3">
            <button type="submit" class="btn btn-primary" id="btnValider" disabled>
              <i class="bi bi-check-lg"></i> Confirmer
            </button>
            <a href="/client/solde" class="btn btn-outline-secondary">Annuler</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
const typesData = <?= json_encode($typesJson) ?>;
let tranchesCache = {};

document.getElementById('type_operation').addEventListener('change', function() {
    const type = this.value;
    const zoneDest = document.getElementById('zoneDestinataire');
    const zoneFrais = document.getElementById('zoneFrais');
    const zoneBareme = document.getElementById('zoneBareme');

    zoneDest.style.display = (type === 'transfert') ? 'block' : 'none';
    zoneFrais.style.display = (type !== 'depot' && type !== '') ? 'block' : 'none';
    zoneBareme.style.display = (type !== 'depot' && type !== '') ? 'block' : 'none';

    // Activer/désactiver le bouton selon le type
    if (type === 'depot' || type === 'retrait') {
        document.getElementById('btnValider').disabled = false;
    } else if (type === 'transfert') {
        document.getElementById('btnValider').disabled = true;
    } else {
        document.getElementById('btnValider').disabled = true;
    }

    if (type === 'depot' || type === '') {
        document.getElementById('affichageFrais').textContent = '0';
        document.getElementById('affichageTotal').textContent = document.getElementById('montant').value || '0';
        document.getElementById('tableBareme').querySelector('tbody').innerHTML = '';
    }

    if (type && type !== 'depot') {
        chargerTranches(type);
    }
});

function chargerTranches(typeNom) {
    const type = typesData.find(t => t.nom === typeNom);
    if (!type) return;

    if (tranchesCache[type.id]) {
        afficherTranches(tranchesCache[type.id]);
        calculerFrais();
        return;
    }

    fetch('/client/tranches-json?type_id=' + type.id)
        .then(r => r.json())
        .then(data => {
            tranchesCache[type.id] = data;
            afficherTranches(data);
            calculerFrais();
        });
}

function afficherTranches(tranches) {
    const tbody = document.getElementById('tableBareme').querySelector('tbody');
    tbody.innerHTML = '';
    tranches.forEach(t => {
        const tr = document.createElement('tr');
        tr.innerHTML = '<td>' + formatMontant(t.montant_min) + '</td><td>' + formatMontant(t.montant_max) + '</td><td>' + formatMontant(t.montant_frais) + '</td>';
        tbody.appendChild(tr);
    });
}

document.getElementById('montant').addEventListener('input', calculerFrais);

function calculerFrais() {
    const type = document.getElementById('type_operation').value;
    if (type === 'depot' || !type) {
        document.getElementById('affichageFrais').textContent = '0';
        document.getElementById('affichageTotal').textContent = document.getElementById('montant').value || '0';
        return;
    }

    const montant = parseFloat(document.getElementById('montant').value) || 0;
    const typeObj = typesData.find(t => t.nom === type);
    if (!typeObj || !tranchesCache[typeObj.id]) return;

    const tranche = tranchesCache[typeObj.id].find(t => montant >= t.montant_min && montant <= t.montant_max);
    const frais = tranche ? parseFloat(tranche.montant_frais) : 0;

    document.getElementById('affichageFrais').textContent = formatMontant(frais);
    document.getElementById('affichageTotal').textContent = formatMontant(montant + frais);
}

document.getElementById('numero_dest').addEventListener('blur', function() {
    const numero = this.value.trim();
    const erreur = document.getElementById('erreurNumero');
    if (numero.length === 0) { erreur.style.display = 'none'; return; }

    fetch('/client/numero-existe-json?numero=' + encodeURIComponent(numero))
        .then(r => r.json())
        .then(data => {
            erreur.style.display = data.existe ? 'none' : 'block';
            document.getElementById('btnValider').disabled = !data.existe;
        });
});

document.getElementById('formOperation').addEventListener('submit', function(e) {
    const type = document.getElementById('type_operation').value;
    if (!type) { e.preventDefault(); return; }
    if (type === 'transfert') {
        const numero = document.getElementById('numero_dest').value.trim();
        const erreur = document.getElementById('erreurNumero');
        if (erreur.style.display === 'block' || !numero) {
            e.preventDefault();
        }
    }
});

function formatMontant(val) {
    return parseFloat(val).toLocaleString('fr-FR');
}
</script>

<?= $this->endSection() ?>
