<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= esc($title ?? 'Mon Site') ?></title>
  <link href="/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
  <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body>

  <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
      <a class="navbar-brand" href="/">Mon Site</a>
      <div class="d-flex align-items-center">
        <?php if (session()->get('numero_id')): ?>
          <a href="/client/solde" class="btn btn-outline-light btn-sm me-2">Mon solde</a>
          <a href="/client/operation" class="btn btn-outline-light btn-sm me-2">Opération</a>
          <a href="/client/historique" class="btn btn-outline-light btn-sm me-2">Historique</a>
          <form method="POST" action="/client/logout" class="d-inline">
            <?= csrf_field() ?>
            <button class="btn btn-outline-danger btn-sm">Déconnexion</button>
          </form>
        <?php else: ?>
          <a href="/contact" class="btn btn-outline-light btn-sm me-2">Contact</a>
          <a href="/client/login" class="btn btn-outline-light btn-sm">Connexion client</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <?php if (session()->getFlashdata('success')): ?>
      <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
  </main>

  <footer class="text-center text-muted py-3 mt-5 border-top">
    &copy; <?= date('Y') ?> — Mon Site
  </footer>

</body>

</html>