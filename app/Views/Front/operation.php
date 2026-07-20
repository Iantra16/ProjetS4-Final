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
              <option value="transfert">Transfert (unique)</option>
              <option value="transfert_multiple">Transfert (multiple)</option>
            </select>
          </div>

          <div class="mb-3" id="zoneMontantGlobal">
            <label for="montant" class="form-label">Montant (F)</label>
            <input type="number" class="form-control" id="montant" name="montant"
                   min="1" step="any" value="<?= old('montant') ?>">
          </div>

          <div id="zoneDestinataires" style="display:none;">
            <div class="destinataire-group" id="destinataire1">
              <div class="mb-3">
                <label class="form-label">Numéro destinataire</label>
                <input type="text" class="form-control numero-dest" name="numero_dest[]" maxlength="10" placeholder="Ex: 0331234567">
              </div>
              <div class="mb-3">
                <label class="form-label">Montant (F)</label>
                <input type="number" class="form-control montant-dest" name="montant_dest[]" min="1" step="any">
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

// ... (keep previous listeners)

document.getElementById('formOperation').addEventListener('submit', async function(e) {
    const type = document.getElementById('type_operation').value;
    if (type === 'transfert_multiple') {
        const numeros = document.querySelectorAll('.numero-dest');
        let operateurId = null;
        
        for (let numInput of numeros) {
            const num = numInput.value.trim();
            if (!num) continue;
            
            const resp = await fetch('/client/operateur-du-numero-json?numero=' + encodeURIComponent(num));
            const data = await resp.json();
            
            if (!data.operateur) {
                alert('Numéro destinataire invalide : ' + num);
                e.preventDefault();
                return;
            }
            
            if (operateurId === null) {
                operateurId = data.operateur.id;
            } else if (operateurId !== data.operateur.id) {
                alert('Tous les destinataires doivent être du même opérateur.');
                e.preventDefault();
                return;
            }
        }
    }
});
// ...
</script>

<?= $this->endSection() ?>
