<?= $this->extend('layout/front') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
  <div class="col-md-5 col-lg-4">
    <div class="card shadow-sm">
      <div class="card-body p-4">

        <div class="text-center mb-4">
          <i class="bi bi-phone fs-1 text-primary"></i>
          <h4 class="mt-2 mb-0">Espace Client</h4>
          <p class="text-muted small">Entrez votre numéro pour accéder à votre compte</p>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= esc(session()->getFlashdata('error')) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <form method="post" action="/client/login">
          <?= csrf_field() ?>

          <div class="mb-3">
            <label for="numero" class="form-label fw-semibold">Numéro de téléphone</label>
            <div class="input-group">
              <span class="input-group-text"><i class="bi bi-telephone"></i></span>
              <input
                type="text"
                name="numero"
                id="numero"
                class="form-control form-control-lg"
                placeholder="ex: 0331234567"
                value="<?= esc(old('numero')) ?>"
                maxlength="10"
                inputmode="numeric"
                pattern="\d{10}"
                required
                autofocus
              >
            </div>
            <div class="form-text">10 chiffres — préfixe opérateur reconnu (033, 034, 037…)</div>
          </div>

          <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
              <i class="bi bi-box-arrow-in-right me-1"></i> Accéder à mon compte
            </button>
          </div>
        </form>

        <hr class="my-3">
        <p class="text-center text-muted small mb-0">
          Si votre numéro n'est pas encore enregistré, un compte sera créé automatiquement.
        </p>

      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>
