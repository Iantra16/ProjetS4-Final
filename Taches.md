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

---

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

#### Client

1. option inclure frais de retrait lors de l'envoie
   + pas de frais de retrait pour les autres operateurs

2. envoi multiple vers plusieurs numéros ( divisé le montant pour chaque numéro)
   + même opérateur uniquement

##### Corrections et Améliorations transversales
- BDD / Seeders
    - Fix MobileMoneySeeder : Retrait de la colonne nom obsolète dans prefixe_operateur et ajout de DELETE FROM sqlite_sequence (reset autoincrement) pour éviter les erreurs de clés étrangères (FK) lors des réinitialisations.
    - Fix V2Seeder : Implémentation du mapping id_operateur dynamique pour lier correctement les préfixes aux opérateurs, et reset de séquence pour éviter le drift des IDs.
- Fixes de bugs
    - Fatal Error : Correction de l'erreur Array to string conversion dans OperationController.php (variable dead code supprimée).
    - Bug JS Calcul : Refonte de calculerFrais() pour éviter de détruire les éléments DOM (spans) lors du rendu, supprimant les affichages erronés.
    - Bug Admin : Correction du contrôleur OperateurController qui forçait est_notre_operateur = 0 lors de chaque modification, rendant les opérateurs internes "externe" par erreur.
    - Permission : Fix de l'erreur Cache unable to write par recréation et mise à jour des droits sur writable/cache/.
- Améliorations
    - Fiabilisation JS : Conversion du submit handler en async propre avec preventDefault systématique et soumission programmatique pour garantir que les validations serveur/JS ne soient pas bypassées.
    - UX : Ajout d'une case à cocher dans le formulaire admin pour gérer le flag "Notre opérateur" manuellement.    
    - UX : Affichage explicite du montant divisé reçu par chaque destinataire (label "÷ N") dans le formulaire clien

Alea : tag etu(4 dernier chiffre)
1. promotion sur les frais de transfert en % (meme operateur)
- cree dans la base 
- frais - %promotion

## 
- ajouter une colonne promotion dans operateur [ok]
- ajouter la colonne promotion dans les controller, models [ok]
- dans operationController, sur la methode de transfert, ajouter la logique de promotion pour le meme operateur
+ bonus ( page de modif )


epargne 
  interface qui dis epargne 50% pour client modifiable 
  l'user choisit le taux d'epargne a faire
  lors d'un transfert vers  le client qui fais de l'epargne : ex 50% d'eparge -> dans son compte epargne  et le reste dans vers son solde
    
  
