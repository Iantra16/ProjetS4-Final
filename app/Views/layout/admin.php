<?php
require_once APPPATH . 'Views/partials/icons.php';

// Détection du lien actif de la sidebar, sans dépendance à un helper CI
$cheminActuel = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '';

function lienActif(string $chemin, string $cheminActuel): string
{
    return str_starts_with($cheminActuel, $chemin) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= esc($title ?? 'Back Office') ?></title>
<link href="/assets/vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="d-flex">
  <!-- Sidebar -->
  <nav class="admin-sidebar text-white vh-100 p-3">
    <h5 class="mb-4 d-flex align-items-center gap-2"><?= icon('admin') ?> Admin</h5>
    <ul class="nav flex-column gap-1">
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/operateurs', $cheminActuel) ?>" href="/admin/operateurs">
          <?= icon('operateurs') ?> Opérateurs
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/prefixes', $cheminActuel) ?>" href="/admin/prefixes">
          <?= icon('prefixes') ?> Préfixes
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/types-operation', $cheminActuel) ?>" href="/admin/types-operation">
          <?= icon('types') ?> Types opération
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/tranches', $cheminActuel) ?>" href="/admin/tranches">
          <?= icon('tranches') ?> Tranches frais
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/comptes', $cheminActuel) ?>" href="/admin/comptes">
          <?= icon('comptes') ?> Comptes clients
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/rapport/gains', $cheminActuel) ?>" href="/admin/rapport/gains">
          <?= icon('gains') ?> Gains
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link d-flex align-items-center gap-2 <?= lienActif('/admin/rapport/montants-a-envoyer', $cheminActuel) ?>" href="/admin/rapport/montants-a-envoyer">
          <?= icon('envoyer') ?> Montants à envoyer
        </a>
      </li>
    </ul>
    <form method="POST" action="/client/logout" class="mt-4">
      <?= csrf_field() ?>
      <button class="btn btn-nav-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-1">
        <?= icon('logout') ?> Déconnexion
      </button>
    </form>
  </nav>

  <!-- Contenu -->
  <main class="admin-content p-4">
    <h3><?= esc($title ?? '') ?></h3>
    <hr>

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
</div>

<script src="/assets/vendor/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>