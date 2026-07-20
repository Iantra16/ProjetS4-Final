## V0
- initialisation git

## V1
- base
    - table prefixe_operateur
        - id
        - prefixe
        - nom
    - table numero_telephone
        - id
        - id_prefixe
        - numero (10)
        - date_creation
    - table solde
        - id
        - id_numero_tel
        - montant
        - date
    - table type_operation
        - id
        - nom
    - table tranches_frais
        - id
        - montant_min
        - montant_max
        - montant_frais
        - date
    - table operation
        - id
        - id_type_operation
        - id_numero_tel
        - id_numero_tel_dest
        - montant
        - frais
        - date

### Taches v1
#### etape 1
1. modifier le config/database.php sur le projetfinal.db avec le nom de la base
2. s'assurer que .env contient developpement
3. tester avec

```bash
php spark serve
```
4. Migrations
- cree les migrations
```bash
php spark make:migration CreatePrefixeOperateur
php spark make:migration CreateNumeroTelephone
php spark make:migration CreateSolde
php spark make:migration CreateTypeOperation
php spark make:migration CreateTranchesFrais
php spark make:migration CreateOperation
```
5. remplir ces fichiers 
6. lancer la migration
```bash
php spark migrate
```
7. Seeder
- cree les seeders
```bash
php spark make:seeder DemoSeeder
```
- lancer le seeder
```bash
php spark db:seed DemoSeeder
```
8. le projetfinal.db est generer automatiquement

#### etape 2 : fonctionnalites
##### Operateur
1. config prefixe operateur
    - crud 
2. crud type operations
    - depot
    - retrait
    - transfert
    crud tranches frais
3. situation gain via frais
4. situation compte client
    
##### Client
1. login avec numero
2. voir solde
3. faire operation 
    - depot auto
    - retrait auto
    - transfert
4. voir historique
## Côté Opérateur

### 1. Config préfixe opérateur (CRUD)
- [ ] `PrefixeController` : méthodes `index`, `new`, `create`, `edit`, `update`, `delete`
- [ ] Vue `prefixe/index.php` : tableau liste + bouton "Ajouter"
- [ ] Vue `prefixe/form.php` : formulaire (prefixe, nom) réutilisé pour create/edit
- [ ] Validation CI4 : `prefixe` unique, format numérique (ex: exactement 3 chiffres), `nom` requis
- [ ] Protection suppression : empêcher de supprimer un préfixe déjà utilisé par un `numero_telephone` (sinon `RESTRICT` va throw une erreur SQL à gérer proprement avec un message utilisateur)

### 2. CRUD type opération + CRUD tranches frais
**Type opération** (probablement pré-rempli via seeder : dépôt/retrait/transfert, mais CRUD quand même demandé)
- [ ] `TypeOperationController` : CRUD classique
- [ ] Vue simple liste + form

**Tranches de frais**
- [ ] `TrancheFraisController` : CRUD (montant_min, montant_max, montant_frais)
- [ ] Validation : `montant_max > montant_min`, pas de chevauchement entre tranches existantes (à vérifier en callback ou côté controller avant insert/update)
- [ ] Vue liste triée par `montant_min` croissant (plus lisible pour vérifier les trous/chevauchements)
- [ ] Réflexion : est-ce que les tranches sont différentes par type d'opération ? Si oui il manque un `id_type_operation` dans `tranches_frais` — à trancher avant de coder (l'énoncé montre un seul barème dans l'exemple, donc peut-être commun à retrait+transfert)

### 3. Situation gain via frais
- [ ] `RapportController@gains` : requête `SUM(frais)` groupé par type d'opération, et/ou par période (jour/mois)
- [ ] Vue avec total général + tableau détaillé par type
- [ ] Bonus si temps : filtre par date (input date début/fin)

```php
$this->db->table('operation')
    ->select('type_operation.nom, SUM(operation.frais) as total_frais, COUNT(*) as nb_operations')
    ->join('type_operation', 'type_operation.id = operation.id_type_operation')
    ->groupBy('type_operation.nom')
    ->get()->getResultArray();
```

### 4. Situation compte client
- [ ] `CompteController@index` : liste tous les `numero_telephone` avec leur solde actuel (dernière ligne de `solde` par numéro)
- [ ] Requête : dernier solde par client (attention, ta table `solde` semble être un historique — donc `MAX(date)` ou `MAX(id)` par `id_numero_tel`)
- [ ] Vue détail par client (`CompteController@show/$id`) : solde + historique de ses opérations

## Côté Client

### 1. Login avec numéro
- [ ] `AuthController@login` : formulaire simple (numéro de téléphone)
- [ ] Logique : chercher le numéro dans `numero_telephone`
  - Si trouvé → vérifier le préfixe toujours valide, puis créer session (`session()->set(['numero_id' => ...])`)
  - Si pas trouvé → vérifier que le préfixe (3 premiers chiffres) existe dans `prefixe_operateur` → si oui, créer le compte + solde initial à 0 ; si non, message d'erreur
- [ ] Middleware/filter CI4 (`ClientAuthFilter`) pour protéger les routes clients suivantes (solde, opération, historique)

### 2. Voir solde
- [ ] `CompteClientController@solde` : récupère le solde courant du client connecté (via session)
- [ ] Vue simple : affiche montant + numéro

### 3. Faire opération (dépôt/retrait/transfert)
C'est le cœur métier — je détaille plus :

- [ ] `OperationController@depot` : formulaire montant → calcul frais (0 selon ton exemple) → insert `operation` + nouvelle ligne `solde` (ancien + montant)
- [ ] `OperationController@retrait` : formulaire montant → vérifier solde suffisant (**montant + frais ≤ solde actuel**) → calcul frais selon tranche → insert `operation` + nouvelle ligne `solde` (ancien - montant - frais)
- [ ] `OperationController@transfert` : formulaire montant + numéro destinataire → vérifications :
  - destinataire existe ?
  - destinataire ≠ soi-même ?
  - solde suffisant (montant + frais) ?
  - → insert `operation` (avec `id_numero_tel_dest`) + 2 lignes `solde` (débit expéditeur, crédit destinataire)
- [ ] **Transaction SQLite obligatoire** pour transfert et retrait (atomicité débit/insertion) :

```php
$this->db->transStart();
// insert solde débit
// insert solde crédit  
// insert operation
$this->db->transComplete();
if ($this->db->transStatus() === false) {
    // rollback auto, retour erreur utilisateur
}
```

- [ ] Fonction utilitaire commune `calculerFrais($montant, $type)` : cherche la bonne tranche → à mettre dans un `Model` ou `Service`, pas dupliquée dans chaque controller

### 4. Voir historique
- [ ] `OperationController@historique` : requête sur `operation` avec `WHERE id_numero_tel = X OR id_numero_tel_dest = X`
- [ ] Vue tableau : date, type, montant, frais, et libellé dynamique ("Transfert envoyé à...", "Transfert reçu de...", "Dépôt", "Retrait")
- [ ] Tri par date décroissante

## Ordre de développement suggéré (pour le binôme)

Vu les dépendances entre fonctionnalités, je proposerais :

**Personne A (Opérateur)** :
1. CRUD préfixe → 2. CRUD type opération + tranches → 3. Rapports (gains, comptes)

**Personne B (Client)** :
1. Login/création auto compte → 2. Solde → 3. Dépôt (le plus simple, sert de brique de base) → 4. Retrait → 5. Transfert (le plus complexe) → 6. Historique

La logique `calculerFrais()` doit être écrite **une fois** et partagée — c'est le point de synchronisation le plus important entre vous deux, à faire tôt pour ne pas bloquer retrait/transfert.

Tu veux que je commence à écrire le code d'un de ces controllers/models en particulier (je suggérerais `calculerFrais()` en premier, vu que retrait ET transfert en dépendent) ?