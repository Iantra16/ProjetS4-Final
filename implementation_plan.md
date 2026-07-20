# Plan détaillé — ProjetS4-Final (Mobile Money CI4)

## Architecture en place (rappel)

| Élément | Pattern observé |
|---|---|
| Layout admin | `layout/admin.php` → `$this->extend('layout/admin')` + `$this->section('content')` |
| Layout front | `layout/front.php` → idem |
| Controllers admin | `App\Controllers\Admin\NomController` dans `app/Controllers/Admin/` |
| Controllers front | `App\Controllers\Front\NomController` dans `app/Controllers/Front/` |
| Filtre auth admin | ~~aucun pour l'instant~~ — groupe `admin` accessible directement par URL |
| Bootstrap | offline dans `/assets/vendor/bootstrap/` |
| Flash messages | `session()->getFlashdata('success')` / `'error'` déjà gérés dans les layouts |

---

## PARTIE OPÉRATEUR (Admin)

> [!NOTE]
> Pas d'authentification pour l'instant. Le groupe `admin` dans `Routes.php` est déclaré **sans filtre** :
> ```php
> // AVANT (avec auth) → NE PAS UTILISER
> $routes->group('admin', ['filter' => 'auth:admin'], function ($routes) {
> 
> // APRÈS (accès direct par URL)
> $routes->group('admin', function ($routes) {
> ```
> Quand l'auth sera nécessaire plus tard, il suffira de rajouter `['filter' => 'auth:admin']`.

### 1. CRUD Préfixe Opérateur

#### `app/Config/Routes.php` — ajouter dans le groupe `admin`
```php
$routes->get('prefixes', 'Admin\PrefixeController::index');
$routes->get('prefixes/new', 'Admin\PrefixeController::new');
$routes->post('prefixes/create', 'Admin\PrefixeController::create');
$routes->get('prefixes/edit/(:num)', 'Admin\PrefixeController::edit/$1');
$routes->post('prefixes/update/(:num)', 'Admin\PrefixeController::update/$1');
$routes->get('prefixes/delete/(:num)', 'Admin\PrefixeController::delete/$1');
```

#### `app/Models/PrefixeOperateurModel.php` — méthodes à ajouter
```php
protected $validationRules = [
    'prefixe' => 'required|exact_length[3]|is_natural_no_zero|is_unique[prefixe_operateur.prefixe,id,{id}]',
    'nom'     => 'required|min_length[2]',
];

// Vérifie si un préfixe est utilisé par au moins un numéro_telephone
public function estUtilise(int $id): bool
{
    return $this->db->table('numero_telephone')
        ->where('id_prefixe', $id)
        ->countAllResults() > 0;
}
```

#### `app/Controllers/Admin/PrefixeController.php` — [NOUVEAU]
Méthodes : `index`, `new`, `create`, `edit`, `update`, `delete`
- `delete` : appelle `$model->estUtilise($id)` avant de supprimer → si vrai, `redirect()->back()->with('error', 'Ce préfixe est utilisé par des numéros existants.')`

#### Vues à créer
- `app/Views/Admin/prefixes/index.php` — tableau (prefixe, nom) + boutons Edit/Delete + bouton "+ Ajouter"
- `app/Views/Admin/prefixes/form.php` — formulaire réutilisé create/edit (2 champs : prefixe, nom)

---

### 2. CRUD Type Opération

#### `Routes.php` — ajouter dans `admin`
```php
$routes->get('types-operation', 'Admin\TypeOperationController::index');
$routes->get('types-operation/new', 'Admin\TypeOperationController::new');
$routes->post('types-operation/create', 'Admin\TypeOperationController::create');
$routes->get('types-operation/edit/(:num)', 'Admin\TypeOperationController::edit/$1');
$routes->post('types-operation/update/(:num)', 'Admin\TypeOperationController::update/$1');
$routes->get('types-operation/delete/(:num)', 'Admin\TypeOperationController::delete/$1');
```

#### `app/Models/TypeOperationModel.php` — ajouter
```php
protected $validationRules = [
    'nom' => 'required|min_length[2]|is_unique[type_operation.nom,id,{id}]',
];
```

#### `app/Controllers/Admin/TypeOperationController.php` — [NOUVEAU]
Méthodes : `index`, `new`, `create`, `edit`, `update`, `delete`

#### Vues à créer
- `app/Views/Admin/types_operation/index.php`
- `app/Views/Admin/types_operation/form.php`

---

### 3. CRUD Tranches de Frais

#### `Routes.php` — ajouter dans `admin`
```php
$routes->get('tranches', 'Admin\TrancheFraisController::index');
$routes->get('tranches/new', 'Admin\TrancheFraisController::new');
$routes->post('tranches/create', 'Admin\TrancheFraisController::create');
$routes->get('tranches/edit/(:num)', 'Admin\TrancheFraisController::edit/$1');
$routes->post('tranches/update/(:num)', 'Admin\TrancheFraisController::update/$1');
$routes->get('tranches/delete/(:num)', 'Admin\TrancheFraisController::delete/$1');
```

#### `app/Models/TranchesFraisModel.php` — méthodes à ajouter
```php
protected $validationRules = [
    'montant_min'   => 'required|numeric',
    'montant_max'   => 'required|numeric|greater_than[{montant_min}]', // ou vérif manuelle
    'montant_frais' => 'required|numeric',
];

// Récupère toutes les tranches triées par montant_min croissant
public function getAllSorted(): array
{
    return $this->orderBy('montant_min', 'ASC')->findAll();
}

// Vérifie si une tranche [min, max] chevauche une tranche existante (hors id exclu)
public function chevauchementExiste(float $min, float $max, ?int $excludeId = null): bool
{
    $builder = $this->db->table($this->table)
        ->where('montant_min <', $max)
        ->where('montant_max >', $min);
    if ($excludeId) {
        $builder->where('id !=', $excludeId);
    }
    return $builder->countAllResults() > 0;
}

// Trouve la tranche applicable pour un montant donné
public function trouverTranche(float $montant): ?array
{
    return $this->where('montant_min <=', $montant)
                ->where('montant_max >=', $montant)
                ->first();
}
```

#### `app/Controllers/Admin/TrancheFraisController.php` — [NOUVEAU]
- `create` et `update` : appeler `$model->chevauchementExiste(...)` avant save → erreur si chevauchement

#### Vues à créer
- `app/Views/Admin/tranches/index.php` — tableau trié par montant_min + col montant_frais
- `app/Views/Admin/tranches/form.php` — 3 champs numériques

---

### 4. Rapport Gains (Situation des gains via frais)

#### `Routes.php` — ajouter dans `admin`
```php
$routes->get('rapport/gains', 'Admin\RapportController::gains');
```

#### `app/Models/OperationModel.php` — méthodes à ajouter
```php
// Gains groupés par type d'opération
public function gainsParType(): array
{
    return $this->db->table('operation')
        ->select('type_operation.nom, SUM(operation.frais) as total_frais, COUNT(*) as nb_operations')
        ->join('type_operation', 'type_operation.id = operation.id_type_operation')
        ->groupBy('type_operation.nom')
        ->get()->getResultArray();
}

// Gains filtrés par période (optionnel)
public function gainsParPeriode(?string $debut = null, ?string $fin = null): array
{
    $builder = $this->db->table('operation')
        ->select('type_operation.nom, SUM(operation.frais) as total_frais, COUNT(*) as nb_operations')
        ->join('type_operation', 'type_operation.id = operation.id_type_operation');
    if ($debut) $builder->where('operation.date >=', $debut);
    if ($fin)   $builder->where('operation.date <=', $fin . ' 23:59:59');
    return $builder->groupBy('type_operation.nom')->get()->getResultArray();
}
```

#### `app/Controllers/Admin/RapportController.php` — [NOUVEAU]
```php
public function gains()
{
    $debut = $this->request->getGet('debut');
    $fin   = $this->request->getGet('fin');
    $model = new OperationModel();

    $data['gains']      = $model->gainsParPeriode($debut, $fin);
    $data['totalGains'] = array_sum(array_column($data['gains'], 'total_frais'));
    $data['debut']      = $debut;
    $data['fin']        = $fin;
    $data['title']      = 'Rapport des gains';
    return view('Admin/rapport/gains', $data);
}
```

#### Vues à créer
- `app/Views/Admin/rapport/gains.php` — filtre date début/fin + tableau par type + total général

---

### 5. Situation Comptes Clients (vue opérateur)

#### `Routes.php` — ajouter dans `admin`
```php
$routes->get('comptes', 'Admin\CompteController::index');
$routes->get('comptes/(:num)', 'Admin\CompteController::show/$1');
```

#### `app/Models/NumeroTelephoneModel.php` — méthodes à ajouter
```php
// Liste tous les numéros avec leur dernier solde
public function avecSoldeActuel(): array
{
    return $this->db->table('numero_telephone nt')
        ->select('nt.id, nt.numero, po.nom as operateur, s.montant as solde_actuel, s.date as date_solde')
        ->join('prefixe_operateur po', 'po.id = nt.id_prefixe')
        ->join('solde s', 's.id = (SELECT id FROM solde WHERE id_numero_tel = nt.id ORDER BY id DESC LIMIT 1)', 'left')
        ->get()->getResultArray();
}
```

#### `app/Models/SoldeModel.php` — méthodes à ajouter
```php
// Dernier solde d'un numéro
public function dernierSolde(int $idNumeroTel): ?array
{
    return $this->where('id_numero_tel', $idNumeroTel)
                ->orderBy('id', 'DESC')
                ->first();
}
```

#### `app/Controllers/Admin/CompteController.php` — [NOUVEAU]
- `index` : liste tous les numéros avec solde actuel
- `show($id)` : détail d'un client → solde + historique de ses opérations

#### Vues à créer
- `app/Views/Admin/comptes/index.php`
- `app/Views/Admin/comptes/show.php`

---

## Mise à jour Sidebar Admin

Dans `app/Views/layout/admin.php`, mettre à jour le menu :
```html
<li><a href="/admin/prefixes">Préfixes</a></li>
<li><a href="/admin/types-operation">Types d'opération</a></li>
<li><a href="/admin/tranches">Tranches de frais</a></li>
<li><a href="/admin/comptes">Comptes clients</a></li>
<li><a href="/admin/rapport/gains">Gains</a></li>
```

---

## PARTIE CLIENT (Front)

### Nouveau filtre `ClientAuthFilter`

#### `app/Filters/ClientAuthFilter.php` — [NOUVEAU]
```php
// Vérifie session()->get('numero_id') sinon redirect vers /client/login
```

#### `app/Config/Filters.php` — ajouter dans `$aliases`
```php
'client' => \App\Filters\ClientAuthFilter::class,
```

---

### 1. Login / Création auto compte

#### `Routes.php` — hors des groupes existants
```php
$routes->get('client/login', 'Front\AuthClientController::login');
$routes->post('client/login', 'Front\AuthClientController::login');
$routes->post('client/logout', 'Front\AuthClientController::logout');

$routes->group('client', ['filter' => 'client'], function($routes) {
    $routes->get('solde', 'Front\CompteClientController::solde');
    $routes->get('historique', 'Front\OperationController::historique');
    $routes->get('depot', 'Front\OperationController::depot');
    $routes->post('depot', 'Front\OperationController::depot');
    $routes->get('retrait', 'Front\OperationController::retrait');
    $routes->post('retrait', 'Front\OperationController::retrait');
    $routes->get('transfert', 'Front\OperationController::transfert');
    $routes->post('transfert', 'Front\OperationController::transfert');
});
```

#### `app/Models/NumeroTelephoneModel.php` — méthodes à ajouter
```php
// Trouve un numéro par son numéro complet (string)
public function findByNumero(string $numero): ?array
{
    return $this->where('numero', $numero)->first();
}

// Crée un nouveau numéro + solde initial à 0
// (appelé depuis le controller)
public function creerCompte(int $idPrefixe, string $numero): int
{
    $this->insert(['id_prefixe' => $idPrefixe, 'numero' => $numero, 'date_creation' => date('Y-m-d H:i:s')]);
    return $this->insertID();
}
```

#### `app/Models/PrefixeOperateurModel.php` — méthodes à ajouter
```php
// Trouve un préfixe par sa valeur (ex: "034")
public function findByPrefixe(string $prefixe): ?array
{
    return $this->where('prefixe', $prefixe)->first();
}
```

#### `app/Controllers/Front/AuthClientController.php` — [NOUVEAU]
```
login() [GET] → affiche formulaire (1 champ : numéro de téléphone)

login() [POST] :
  1. Extraire les 3 premiers chiffres → chercher dans prefixe_operateur
     - Si préfixe inconnu → erreur "Opérateur non reconnu"
  2. Chercher le numéro complet dans numero_telephone
     - Si trouvé → session()->set(['numero_id' => $id, 'numero' => $numero]) → redirect /client/solde
     - Si pas trouvé → créer le compte (insert numero_telephone + insert solde montant=0)
                     → session set → redirect /client/solde

logout() → session()->remove(['numero_id','numero']) → redirect /client/login
```

#### Vues à créer
- `app/Views/Front/auth/login.php` — layout/front, 1 input numéro de téléphone

---

### 2. Voir Solde

#### `app/Controllers/Front/CompteClientController.php` — [NOUVEAU]
```php
public function solde()
{
    $idNumero = session()->get('numero_id');
    $soldeModel = new SoldeModel();
    $data['solde']  = $soldeModel->dernierSolde($idNumero);
    $data['numero'] = session()->get('numero');
    $data['title']  = 'Mon solde';
    return view('Front/solde', $data);
}
```

#### Vues à créer
- `app/Views/Front/solde.php` — layout/front, affiche numéro + montant en grand

---

### 3. Opérations (Dépôt, Retrait, Transfert)

#### Fonction partagée `calculerFrais` — dans `TranchesFraisModel`
```php
// Déjà défini ci-dessus dans trouverTranche()
// Wrapper à appeler dans les controllers :
public function calculerFrais(float $montant): float
{
    $tranche = $this->trouverTranche($montant);
    return $tranche ? (float) $tranche['montant_frais'] : 0.0;
}
```

#### `app/Models/OperationModel.php` — méthodes à ajouter
```php
// Historique d'un numéro (envoyeur ou destinataire)
public function historiquePourNumero(int $idNumero): array
{
    return $this->db->table('operation o')
        ->select('o.*, t.nom as type_nom, nd.numero as numero_dest')
        ->join('type_operation t', 't.id = o.id_type_operation')
        ->join('numero_telephone nd', 'nd.id = o.id_numero_tel_dest', 'left')
        ->groupStart()
            ->where('o.id_numero_tel', $idNumero)
            ->orWhere('o.id_numero_tel_dest', $idNumero)
        ->groupEnd()
        ->orderBy('o.date', 'DESC')
        ->get()->getResultArray();
}
```

#### `app/Models/SoldeModel.php` — méthodes à ajouter
```php
// Insère un nouveau solde = ancien solde + delta (delta peut être négatif)
public function insertNouveauSolde(int $idNumeroTel, float $delta): void
{
    $ancien = $this->dernierSolde($idNumeroTel);
    $ancienMontant = $ancien ? (float) $ancien['montant'] : 0.0;
    $this->insert([
        'id_numero_tel' => $idNumeroTel,
        'montant'       => $ancienMontant + $delta,
        'date'          => date('Y-m-d H:i:s'),
    ]);
}
```

#### `app/Controllers/Front/OperationController.php` — [NOUVEAU]

**depot() [GET]** → formulaire montant  
**depot() [POST]** :
```
1. Valider montant > 0
2. frais = 0 (dépôt)
3. Trouver id_type_operation pour "depot"
4. insert operation
5. $soldeModel->insertNouveauSolde($idNumero, +$montant)
6. redirect /client/solde avec success
```

**retrait() [GET]** → formulaire montant  
**retrait() [POST]** :
```
1. Valider montant > 0
2. frais = $tranchesModel->calculerFrais($montant)
3. total = montant + frais
4. Vérifier solde actuel >= total → sinon erreur "Solde insuffisant"
5. db->transStart()
   - insert operation
   - $soldeModel->insertNouveauSolde($idNumero, -(montant + frais))
   db->transComplete()
6. Vérifier transStatus → erreur ou redirect success
```

**transfert() [GET]** → formulaire montant + numéro destinataire  
**transfert() [POST]** :
```
1. Valider montant > 0, numero_dest non vide
2. Vérifier numero_dest != propre numéro
3. Chercher destinataire dans numero_telephone → erreur si inexistant
4. frais = $tranchesModel->calculerFrais($montant)
5. Vérifier solde >= montant + frais
6. db->transStart()
   - insert operation (avec id_numero_tel_dest)
   - $soldeModel->insertNouveauSolde($idNumero, -(montant + frais))   // débit expéditeur
   - $soldeModel->insertNouveauSolde($idDest, +$montant)              // crédit destinataire
   db->transComplete()
7. Vérifier transStatus → erreur ou success
```

#### Vues à créer
- `app/Views/Front/depot.php` — layout/front, 1 champ montant
- `app/Views/Front/retrait.php` — layout/front, 1 champ montant + affiche frais estimés (JS optionnel)
- `app/Views/Front/transfert.php` — layout/front, champ montant + champ numéro destinataire

---

### 4. Historique Client

#### `app/Controllers/Front/OperationController.php` — méthode à ajouter
```php
public function historique()
{
    $idNumero = session()->get('numero_id');
    $model = new OperationModel();
    $data['operations'] = $model->historiquePourNumero($idNumero);
    $data['monId']      = $idNumero;
    $data['title']      = 'Mon historique';
    return view('Front/historique', $data);
}
```

#### Vues à créer
- `app/Views/Front/historique.php` — tableau avec libellé dynamique :
  - `id_numero_tel == monId` ET type=transfert → "Transfert envoyé à {numero_dest}"
  - `id_numero_tel_dest == monId` → "Transfert reçu de {expediteur}"
  - type=depot → "Dépôt"
  - type=retrait → "Retrait"

---

## Mise à jour layout/front.php

Ajouter les liens contextuels si session `numero_id` est active :
```html
<?php if (session()->get('numero_id')): ?>
  <a href="/client/solde">Mon solde</a>
  <a href="/client/historique">Historique</a>
  <a href="/client/depot">Dépôt</a>
  <a href="/client/retrait">Retrait</a>
  <a href="/client/transfert">Transfert</a>
  <form method="POST" action="/client/logout">...</form>
<?php else: ?>
  <a href="/client/login">Connexion client</a>
<?php endif; ?>
```

---

## Résumé des fichiers à créer/modifier

### Fichiers MODIFIÉS
| Fichier | Ce qui change |
|---|---|
| `app/Config/Routes.php` | Groupe `admin` **sans filtre** (accès direct) + nouvelles routes admin + groupe `client` |
| `app/Config/Filters.php` | Ajout alias `'client' => ClientAuthFilter::class` (côté admin : pas de filtre pour l'instant) |
| `app/Models/PrefixeOperateurModel.php` | + `validationRules`, + `estUtilise()`, + `findByPrefixe()` |
| `app/Models/TranchesFraisModel.php` | + `validationRules`, + `getAllSorted()`, + `chevauchementExiste()`, + `trouverTranche()`, + `calculerFrais()` |
| `app/Models/NumeroTelephoneModel.php` | + `findByNumero()`, + `creerCompte()`, + `avecSoldeActuel()` |
| `app/Models/SoldeModel.php` | + `dernierSolde()`, + `insertNouveauSolde()` |
| `app/Models/OperationModel.php` | + `gainsParType()`, + `gainsParPeriode()`, + `historiquePourNumero()` |
| `app/Models/TypeOperationModel.php` | + `validationRules` |
| `app/Views/layout/admin.php` | Mise à jour sidebar avec nouveaux liens |
| `app/Views/layout/front.php` | Menu conditionnel client |

### Fichiers NOUVEAUX
| Fichier | Rôle |
|---|---|
| `app/Controllers/Admin/PrefixeController.php` | CRUD préfixes |
| `app/Controllers/Admin/TypeOperationController.php` | CRUD types opération |
| `app/Controllers/Admin/TrancheFraisController.php` | CRUD tranches frais |
| `app/Controllers/Admin/CompteController.php` | Vue comptes clients (opérateur) |
| `app/Controllers/Admin/RapportController.php` | Rapport gains |
| `app/Controllers/Front/AuthClientController.php` | Login/logout client |
| `app/Controllers/Front/CompteClientController.php` | Solde client |
| `app/Controllers/Front/OperationController.php` | Dépôt, retrait, transfert, historique |
| `app/Filters/ClientAuthFilter.php` | Protection routes client |
| `app/Views/Admin/prefixes/index.php` | Liste préfixes |
| `app/Views/Admin/prefixes/form.php` | Formulaire préfixe |
| `app/Views/Admin/types_operation/index.php` | Liste types |
| `app/Views/Admin/types_operation/form.php` | Formulaire type |
| `app/Views/Admin/tranches/index.php` | Liste tranches |
| `app/Views/Admin/tranches/form.php` | Formulaire tranche |
| `app/Views/Admin/comptes/index.php` | Liste comptes clients |
| `app/Views/Admin/comptes/show.php` | Détail client |
| `app/Views/Admin/rapport/gains.php` | Rapport gains |
| `app/Views/Front/auth/login.php` | Connexion client |
| `app/Views/Front/solde.php` | Solde client |
| `app/Views/Front/depot.php` | Formulaire dépôt |
| `app/Views/Front/retrait.php` | Formulaire retrait |
| `app/Views/Front/transfert.php` | Formulaire transfert |
| `app/Views/Front/historique.php` | Historique client |

---

## Ordre d'implémentation recommandé

```
1. TranchesFraisModel::calculerFrais()  ← point de synchronisation critique
2. ClientAuthFilter + Routes client
3. AuthClientController (login/création auto)
4. CompteClientController (solde)
5. OperationController::depot()  ← la plus simple, sert de base
6. OperationController::retrait() + transfert()
7. OperationController::historique()
8. (parallèle) Admin: PrefixeController, TypeOperationController, TrancheFraisController
9. Admin: CompteController, RapportController
```
