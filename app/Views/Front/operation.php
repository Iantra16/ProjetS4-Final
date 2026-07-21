<?= $this->extend('layout/front') ?>

<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-7">
    <div class="card shadow-sm">
      <div class="card-body p-4">
        <h3 class="mb-3">Effectuer une opération</h3>

        <p class="text-muted">Solde actuel : <strong><?= number_format($solde['montant'] ?? 0, 0, ',', ' ') ?> Ar</strong></p>

        <div class="card my-3">
            <h3>Configuration du tqux de l'eparge</h3>
            <form action="" method="post">
              <div class="form">
                <label for="number" name= "taux_epargne" ></label>
                <input type="number" name="" id="">
              </div>
            </form>
        </div>

        <form method="POST" action="/client/operation" id="formOperation">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label for="type_operation" class="form-label">Type d'opération</label>
            <select class="form-select" id="type_operation" name="type_operation" required>
              <option value="">-- Choisir --</option>
              <?php foreach ($types as $type): ?>
                <option value="<?= esc($type['nom']) ?>"><?= ucfirst(esc($type['nom'])) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="mb-3" id="zoneMontantGlobal" style="display:none;">
            <label for="montant" class="form-label">Montant global (Ar)</label>
            <input type="number" class="form-control" id="montant" name="montant"
                   min="1" step="any" value="<?= old('montant') ?>">
          </div>

          <div id="zoneDestinataires" style="display:none;">
            <div class="destinataire-group" id="destinataire1">
              <div class="mb-3">
                <label class="form-label">Numéro destinataire</label>
                <input type="text" class="form-control numero-dest" name="numero_dest[]" maxlength="10" placeholder="Ex: 0331234567">
              </div>
            </div>
          </div>
          
          <button type="button" class="btn btn-sm btn-secondary mb-3" id="btnAjouterDest" style="display:none;">+ Ajouter destinataire</button>

          <div class="mb-3 form-check" id="zoneInclureFrais" style="display:none;">
            <input type="checkbox" class="form-check-input" id="inclure_frais" name="inclure_frais" checked>
            <label class="form-check-label" for="inclure_frais">Inclure les frais dans le total débité</label>
          </div>

          <div class="mb-3" id="zoneFrais" style="display:none;">
            <div class="alert alert-info mb-0">
              <div><strong>Frais :</strong> <span id="affichageFrais">0</span> Ar</div>
              <div><strong>Total débité :</strong> <span id="affichageTotal">0</span> Ar</div>
              <div id="zoneRecu" style="display:none;"><strong>Montant reçu par destinataire (<span id="affichageDiv">÷ 1</span>) :</strong> <span id="affichageRecu">0</span> Ar</div>
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
              Confirmer
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
const senderOpId = <?= json_encode($senderOperateurId) ?>;
let tranchesCache = {};

function formatMontant(val) {
    return parseFloat(val).toLocaleString('fr-FR');
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

function chargerTranches(typeNom) {
    const nomReel = (typeNom === 'transfert_multiple' ? 'transfert' : typeNom);
    const type = typesData.find(t => t.nom === nomReel);
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
        })
        .catch(() => {
            console.error('Erreur chargement barème');
        });
}

function calculerFrais() {
    const typeSel = document.getElementById('type_operation').value;
    const montantGlobal = parseFloat(document.getElementById('montant').value) || 0;

    const affFrais = document.getElementById('affichageFrais');
    const affTotal = document.getElementById('affichageTotal');
    const affRecu  = document.getElementById('affichageRecu');
    const zoneRecu = document.getElementById('zoneRecu');

    // Dépôt ou montant non saisi : pas de frais
    if (typeSel === 'depot' || typeSel === '' || montantGlobal <= 0) {
        affFrais.textContent = '0';
        affTotal.textContent = formatMontant(montantGlobal);
        zoneRecu.style.display = 'none';
        return;
    }

    const nomReel = (typeSel === 'transfert_multiple' ? 'transfert' : typeSel);
    const typeObj = typesData.find(t => t.nom === nomReel);
    if (!typeObj || !tranchesCache[typeObj.id]) return;

    let nbDestinataires = 1;
    if (typeSel === 'transfert' || typeSel === 'transfert_multiple') {
        const numeros = document.querySelectorAll('.numero-dest');
        let count = 0;
        numeros.forEach(input => { if (input.value.trim() !== '') count++; });
        nbDestinataires = count > 0 ? count : 1;
    }

    const montantParTransfert = montantGlobal / nbDestinataires;
    const tranche = tranchesCache[typeObj.id].find(t => montantParTransfert >= t.montant_min && montantParTransfert <= t.montant_max);
    const fraisParTransfert = tranche ? parseFloat(tranche.montant_frais) : 0;
    const fraisTotal = fraisParTransfert * nbDestinataires;

    const inclureFrais = document.getElementById('inclure_frais').checked;

    let totalDebite = montantGlobal;
    if (inclureFrais) {
        totalDebite = montantGlobal + fraisTotal;
    }

    // Montant que recevra chaque destinataire (montant global divisé par N)
    const montantRecuParDest = inclureFrais ? montantParTransfert : (montantParTransfert - fraisParTransfert);
    affRecu.textContent = formatMontant(montantRecuParDest > 0 ? montantRecuParDest : 0);
    document.getElementById('affichageDiv').textContent = '÷ ' + nbDestinataires;
    zoneRecu.style.display = (typeSel === 'transfert' || typeSel === 'transfert_multiple') ? 'block' : 'none';

    affFrais.textContent = formatMontant(fraisTotal);
    affTotal.textContent = formatMontant(totalDebite);
}

document.getElementById('type_operation').addEventListener('change', function() {
    const type = this.value;
    const zoneDest = document.getElementById('zoneDestinataires');
    const zoneMontant = document.getElementById('zoneMontantGlobal');
    const btnAjouter = document.getElementById('btnAjouterDest');
    const zoneFrais = document.getElementById('zoneFrais');
    const zoneBareme = document.getElementById('zoneBareme');
    const zoneInclureFrais = document.getElementById('zoneInclureFrais');

    zoneMontant.style.display = (type !== '') ? 'block' : 'none';
    zoneDest.style.display = (type === 'transfert' || type === 'transfert_multiple') ? 'block' : 'none';
    btnAjouter.style.display = (type === 'transfert' || type === 'transfert_multiple') ? 'block' : 'none';
    zoneFrais.style.display = (type !== 'depot' && type !== '') ? 'block' : 'none';
    zoneBareme.style.display = (type !== 'depot' && type !== '') ? 'block' : 'none';
    zoneInclureFrais.style.display = (type === 'transfert' || type === 'transfert_multiple') ? 'block' : 'none';

    document.getElementById('btnValider').disabled = (type === '');
    
    if (type && type !== 'depot') {
        chargerTranches(type);
    } else {
        calculerFrais();
    }
    // Ajout explicite pour forcer le calcul si type changé
    calculerFrais();
});

document.getElementById('btnAjouterDest').addEventListener('click', function() {
    const container = document.getElementById('zoneDestinataires');
    const div = document.createElement('div');
    div.className = 'destinataire-group';
    div.innerHTML = `
        <div class="mb-3"><label class="form-label">Numéro destinataire</label><input type="text" class="form-control numero-dest" name="numero_dest[]" maxlength="10" placeholder="Ex: 0331234567"></div>
    `;
    div.querySelector('.numero-dest').addEventListener('input', calculerFrais);
    container.appendChild(div);
});

document.getElementById('montant').addEventListener('input', calculerFrais);
document.getElementById('inclure_frais').addEventListener('change', calculerFrais);

document.getElementById('formOperation').addEventListener('submit', async function(e) {
    e.preventDefault();
    const type = document.getElementById('type_operation').value;
    if (type === 'transfert' || type === 'transfert_multiple') {
        const numeros = document.querySelectorAll('.numero-dest');
        let operateurId = null;
        let hasDest = false;
        let nbDest = 0;

        for (let numInput of numeros) {
            const num = numInput.value.trim();
            if (!num) continue;
            hasDest = true;
            nbDest++;

            try {
                const resp = await fetch('/client/operateur-du-numero-json?numero=' + encodeURIComponent(num));
                const data = await resp.json();

                if (!data.operateur) {
                    alert('Numéro destinataire invalide : ' + num);
                    return;
                }

                if (operateurId === null) {
                    operateurId = data.operateur.id;
                } else if (operateurId !== data.operateur.id) {
                    alert('Tous les destinataires doivent être du même opérateur.');
                    return;
                }
            } catch (err) {
                alert('Erreur lors de la vérification du numéro.');
                return;
            }
        }
        if (!hasDest) {
            alert('Veuillez saisir au moins un destinataire.');
            return;
        }
        // Envoi multiple : l'expéditeur et les destinataires doivent être du même opérateur
        if (nbDest > 1 && senderOpId !== null && operateurId !== senderOpId) {
            alert('L\'expéditeur et les destinataires doivent être du même opérateur.');
            return;
        }
    }
    this.submit();
});
</script>

<?= $this->endSection() ?>
