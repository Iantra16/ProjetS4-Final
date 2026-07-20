<?= $this->extend('layout/admin') ?>
<?= $this->section('content') ?>

<div class="row mb-4">
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <h6 class="text-muted">Total produits</h6>
      <h3><?= esc($totalProduits) ?></h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <h6 class="text-muted">Stock total</h6>
      <h3><?= esc($totalStock) ?></h3>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card p-3 text-center">
      <h6 class="text-muted">Prix moyen</h6>
      <h3><?= esc($prixMoyen) ?> Ar</h3>
    </div>
  </div>
</div>

<div class="card p-4">
  <h5>Produits par catégorie</h5>
  <canvas id="chartCategories" height="100"></canvas>
</div>

<script src="/assets/vendor/chartjs/chart.umd.min.js"></script>
<script>
const labels = <?= json_encode(array_column($parCategorie, 'categorie')) ?>;
const totaux = <?= json_encode(array_column($parCategorie, 'total')) ?>;

new Chart(document.getElementById('chartCategories'), {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Nombre de produits',
            data: totaux,
            backgroundColor: '#0d6efd'
        }]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
    }
});
</script>

<?= $this->endSection() ?>