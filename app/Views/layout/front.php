<?php require_once APPPATH . 'Views/partials/icons.php'; ?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Espace Client') ?></title>
  <link href="/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="/">
        <?= icon('wallet') ?> Espace Client
      </a>
      <div class="d-flex align-items-center gap-2">
        <?php if (session()->get('numero_id')): ?>
          <a href="/client/solde" class="btn btn-nav-ghost btn-sm d-flex align-items-center gap-1">
            <?= icon('wallet') ?> <span class="d-none d-sm-inline">Mon solde</span>
          </a>
          <a href="/client/operation" class="btn btn-nav-ghost btn-sm d-flex align-items-center gap-1">
            <?= icon('operation') ?> <span class="d-none d-sm-inline">Opération</span>
          </a>
          <a href="/client/historique" class="btn btn-nav-ghost btn-sm d-flex align-items-center gap-1">
            <?= icon('historique') ?> <span class="d-none d-sm-inline">Historique</span>
          </a>
          <form method="POST" action="/client/logout" class="d-inline m-0">
            <?= csrf_field() ?>
            <button class="btn btn-nav-danger btn-sm d-flex align-items-center gap-1">
              <?= icon('logout') ?> <span class="d-none d-sm-inline">Déconnexion</span>
            </button>
          </form>
        <?php else: ?>
          <a href="/admin/operateurs" class="btn btn-nav-ghost btn-sm d-flex align-items-center gap-1">
            <?= icon('admin') ?> <span class="d-none d-sm-inline">Admin</span>
          </a>
          <a href="/client/login" class="btn btn-nav-light btn-sm d-flex align-items-center gap-1">
            <?= icon('login') ?> Connexion client
          </a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success d-flex align-items-center gap-2">
        <?= icon('check') ?> <?= esc(session()->getFlashdata('success')) ?>
      </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2">
        <?= icon('alert') ?> <?= esc(session()->getFlashdata('error')) ?>
      </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
  </main>

  <footer class="text-center text-muted py-3 mt-5 border-top">
    &copy; <?= date('Y') ?> — Espace Client
  </footer>

  <script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
</body>

</html>