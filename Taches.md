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
1. modifier le config/database.php sur le nom.db avec le nom de la base
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
