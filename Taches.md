## V0
- initialisation git

## V1
### base - 1h
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
    - id_type_operation
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

### etape 1
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
php spark make:seeder MobileMoneySeeder
```
- lancer le seeder
```bash
php spark db:seed MobileMoneySeeder
```
8. le projetfinal.db est generer automatiquement


### etape 2 : fonctionnalites - 3h
#### Operateur - Salomon ETU003967 
1. config prefixe operateur
    - crud + filtre

2. crud type operations + filtre + fenetre modal pour afficher leur tranches respectifs
    - depot
    - retrait
    - transfert
    crud tranches frais + filtre

3. situation gain via frais

4. situation compte client

#### Client - Iantra ETU003970
1. login avec numero

2. voir solde
    + filtre par date

3. faire operation + form dynamique selon type operation
    - depot auto
    - retrait auto
    - transfert

4. voir historique
    + filtre , recherche & tri

---------------------------------------------------

## V2
### base
- ajouter une table operateur
    - id
    - nom
    - est_notre_operateur
    - commission_exterieur
- modifie la table prefixe_operateur
    - id
    - id_operateur
    - prefixe

### fonctionnalite
#### Operateur
1. config prefixes pour les autres operateurs

2. config commission vers les autres operateurs
- notre operateur garde le frais de transfert
- autre operateur recoit le montant a transferer + commission

3. dans situation gain via les differents frais, separe operateur et autre operateur

4. situation montant a envoyer a chaque operateur

### Client
1. option inclure frais de retrait lors de l'envoie
    + pas de frais de retrait pour les autres operateurs

2. envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)
    même opérateur uniquement


## 1. Base de données

- [ ] Nouvelle migration `CreateOperateur.php` : table `operateur` (`id`, `nom`, `est_notre_operateur` BOOL, `commission_exterieur` REAL)
- [ ] Nouvelle migration `AlterPrefixeOperateurAddOperateur.php` : `ALTER TABLE prefixe_operateur ADD COLUMN id_operateur INTEGER REFERENCES operateur(id)` — ne touchez pas à la migration V1 existante
- [ ] Nouvelle migration `AlterOperationAddCommission.php` : `ALTER TABLE operation ADD COLUMN commission REAL NOT NULL DEFAULT 0.0`
- [ ] Seed/migration de données — **ajout uniquement, aucune modification des lignes V1 existantes** :
  - insérer 3 lignes `operateur` : Telma (`est_notre_operateur = 1`), Airtel (`= 0`, `commission_exterieur` à fixer), Orange (`= 0`, `commission_exterieur` à fixer)
  - `UPDATE prefixe_operateur SET id_operateur = <id Telma> WHERE prefixe = '034'`
  - `UPDATE prefixe_operateur SET id_operateur = <id Airtel> WHERE prefixe = '033'`
  - `UPDATE prefixe_operateur SET id_operateur = <id Orange> WHERE prefixe = '037'`
  - **ne pas** retoucher les lignes `operation` déjà présentes (leur `commission` reste à 0 par défaut, c'est normal, ce sont des données pré-V2)
- [ ] Ajouter dans le seed **un nouveau compte** avec préfixe `034` (Telma) si vous n'en avez pas déjà un avec un solde suffisant, pour pouvoir tester un transfert interne → externe fraîchement créé après migration (le compte 5 existant est déjà Telma avec 200 000 Ar de solde, donc probablement pas nécessaire — à vérifier)
- [ ] Mettre à jour `base.sql` en conséquence (ajouter les nouvelles instructions `CREATE TABLE`/`ALTER`/`INSERT`, sans supprimer ni modifier les `INSERT` déjà présents pour V1)
- [ ] `php spark migrate` puis vérifier `projetfinal.db`

## 2. Modèles
- [ ] `Models/OperateurModel.php` (nouveau) : `allowedFields`, validation, méthode `notreOperateur()`
- [ ] `Models/PrefixeOperateurModel.php` : ajouter `id_operateur` à `allowedFields`, méthode `estExterieur(int $idPrefixe): bool`
- [ ] `Models/NumeroTelephoneModel.php` : méthode `operateurDuNumero(string $numero): ?array`
- [ ] `Models/OperationModel.php` : `gainsParOperateur()` et `montantsAEnvoyerParOperateur()` (calculés **uniquement sur les opérations créées après la migration**, puisque `commission` vaut 0 par défaut sur les anciennes — ça reste correct mathématiquement, pas besoin de filtrer par date)

## 3. Côté opérateur — config
- [ ] `OperateurController` (CRUD) + vues `Admin/operateurs/index.php`, `form.php`
- [ ] `PrefixeController` : select opérateur + filtre interne/externe
- [ ] Routes admin : `operateurs`, `operateurs/nouveau`, `operateurs/creer`, `operateurs/modifier/(:num)`, `operateurs/mettreAJour/(:num)`, `operateurs/supprimer/(:num)`

## 4. Logique de transfert
- [ ] `OperationController::enregistrer()` bloc `transfert` : résoudre l'opérateur du destinataire, brancher interne (comportement V1 inchangé) vs externe (frais gardés + `commission` calculée et stockée)
- [ ] Décider/documenter dans `Taches.md` le mode de calcul de `commission_exterieur` (% ou montant fixe)

## 5. Rapports
- [ ] `RapportController::gains()` scindé interne/externe
- [ ] Nouvelle action `montantsAEnvoyer()` + vue, route `admin/rapport/montants-a-envoyer`

## 6. Côté client
- [ ] Checkbox "inclure frais de retrait", désactivé si destinataire externe
- [ ] Formulaire envoi multiple (même opérateur uniquement), transaction globale

## 7. Divers
- [ ] `Taches.md` mis à jour à chaque étape
- [ ] Tag `v2` + push avant 17h10

Le point important par rapport à avant : votre étape "seed" devient une étape purement **additive** (`INSERT`/`UPDATE` ciblés) plutôt qu'un retraitement — ça réduit le risque de casser les tests déjà validés en V1.