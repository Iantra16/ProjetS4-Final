<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Connexion</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex justify-content-center align-items-center vh-100">
  <div class="card p-4" style="width: 350px;">
    <h4 class="mb-3 text-center">Connexion</h4>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <form method="post" action="/login">
      <?= csrf_field() ?>
      <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="admin@test.com" required>
      </div>
      <div class="mb-3">
        <label>Mot de passe</label>
        <input type="password" name="password" class="form-control" value="password123" required>
      </div>
      <button class="btn btn-primary w-100">Se connecter</button>
    </form>
  </div>
</div>

</body>
</html>