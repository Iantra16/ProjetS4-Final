<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title ?? 'Back Office') ?></title>
<link href="/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
  <!-- Sidebar -->
  <nav class="bg-dark text-white vh-100 p-3" style="width:230px; position:fixed;">
    <h5 class="mb-4">Admin</h5>
    <ul class="nav flex-column">
      <li class="nav-item"><a class="nav-link text-white" href="/admin/operateurs">Opérateurs</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="/admin/prefixes">Préfixes</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="/admin/types-operation"></i> Types opération</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="/admin/tranches">Tranches frais</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="/admin/comptes"> Comptes clients</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="/admin/rapport/gains">Gains</a></li>
    </ul>
    <form method="POST" action="/logout" class="mt-4">
      <?= csrf_field() ?>
      <button class="btn btn-outline-light btn-sm w-100">Déconnexion</button>
    </form>
  </nav>

  <!-- Contenu -->
  <main class="p-4" style="margin-left:230px; width:100%;">
    <h3><?= esc($title ?? '') ?></h3>
    <hr>

    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
  </main>
</div>

<script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>