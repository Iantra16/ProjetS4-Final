<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="/admin/rapport/gains" class="row g-3 align-items-end">
            <div class="col-auto">
                <label for="debut" class="form-label">Date début</label>
                <input type="date" class="form-control" id="debut" name="debut" value="<?= esc($debut ?? '') ?>">
            </div>
            <div class="col-auto">
                <label for="fin" class="form-label">Date fin</label>
                <input type="date" class="form-control" id="fin" name="fin" value="<?= esc($fin ?? '') ?>">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="/admin/rapport/gains" class="btn btn-secondary">Réinitialiser</a>
            </div>
        </form>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead class="table-dark">
        <tr>
            <th>Opérateur</th>
            <th>Type</th>
            <th>Opération</th>
            <th>Total frais</th>
            <th>Total commission</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($gains)): ?>
            <tr><td colspan="5" class="text-center">Aucune donnée.</td></tr>
        <?php else: ?>
            <?php foreach ($gains as $g): ?>
                <tr>
                    <td><?= esc(ucfirst($g['nom'] ?? 'Inconnu')) ?></td>
                    <td>
                        <span class="badge bg-<?= $g['est_notre_operateur'] ? 'success' : 'warning' ?>">
                            <?= $g['est_notre_operateur'] ? 'Interne' : 'Externe' ?>
                        </span>
                    </td>
                    <td><?= esc(ucfirst($g['type_nom'] ?? 'Inconnu')) ?></td>
                    <td><?= number_format($g['total_frais'], 0, ',', ' ') ?> Ar</td>
                    <td><?= number_format($g['total_commission'], 0, ',', ' ') ?> Ar</td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
    <?php if (!empty($gains)): ?>
    <tfoot>
        <tr class="table-dark">
            <th colspan="3">Total général</th>
            <th><?= number_format(array_sum(array_column($gains, 'total_frais')), 0, ',', ' ') ?> Ar</th>
            <th><?= number_format(array_sum(array_column($gains, 'total_commission')), 0, ',', ' ') ?> Ar</th>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

<!-- Histogramme -->
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <strong>Progression des gains</strong>
        <div class="d-flex align-items-center gap-2">
            <select id="selectAnnee" class="form-select form-select-sm" style="width:auto;">
                <?php for ($y = date('Y'); $y >= date('Y') - 3; $y--): ?>
                    <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                <?php endfor; ?>
            </select>
            <button class="btn btn-sm btn-primary" onclick="chargerGraphique()">Filtrer</button>
        </div>
    </div>
    <div class="card-body">
        <canvas id="chartGains" height="100"></canvas>
    </div>
</div>

<script src="/assets/vendor/chartjs/chart.umd.min.js"></script>
<script>
let chart = null;

function chargerGraphique() {
    const annee = document.getElementById('selectAnnee').value;
    fetch('/admin/rapport/gains-par-mois?annee=' + annee)
        .then(r => r.json())
        .then(data => {
            const mois = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Jun', 'Jul', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
            const labels = [];
            const valeurs = [];
            for (let m = 1; m <= 12; m++) {
                const mm = String(m).padStart(2, '0');
                labels.push(mois[m - 1]);
                const trouve = data.donnees.find(d => d.mois === mm);
                valeurs.push(trouve ? parseFloat(trouve.total_frais) : 0);
            }
            if (chart) chart.destroy();
            chart = new Chart(document.getElementById('chartGains'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Frais (' + annee + ')',
                        data: valeurs,
                        backgroundColor: 'rgba(13, 110, 253, 0.7)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Frais (Ar)' } }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        });
}

// Charger au démarrage
chargerGraphique();
</script>


<?= $this->endSection() ?>
